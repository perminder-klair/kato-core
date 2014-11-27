<?php

namespace kato\components;

use backend\models\Page;
use Yii;
use backend\models\Setting;
use backend\models\Block;
use yii\helpers\HtmlPurifier;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;
use yii\web\UploadedFile;
use kato\helpers\KatoBase;
use yii\helpers\Html;
use kartik\markdown\Markdown;

/**
 * Usage
 * -----
 *
 * In your configuration file, add the setting component.
 *
 * ```php
 * 'components' => [
 *	...
 *	'kato' => 'kato\components\Kato',
 *	...
 * ]
 * ```
 */

class Kato extends \yii\base\Component 
{

    /**
     * Get settings for Kato
     * Usage:
     *
     * ```php
     * $setting = Yii::$app->kato->setting('site_name');
     * ```
     * @param $key
     * @return bool
     */
    public static function setting($key)
    {
        $model = Setting::find()
            ->where(['define' => $key])
            ->one();

        if (!is_null($model)) {
            return $model->value;
        }

        return false;
    }

    public function getBlock($name, $pageId, $layout = null)
    {
        if (is_null($layout)) {
            //if it's dynamic page
            $find = [
                'title' => $name,
                'parent' => $pageId,
            ];
        } else {
            $find = [
                'title' => $name,
                'parent' => $pageId,
                'parent_layout' => $layout,
            ];
        }

        if ($block = Block::findOne($find)
        ) {
            //if block found
            return $block->render();
        }

        return false;
    }

    /**
     * returns page slug if set
     * @return string
     */
    public function pageSlug()
    {
        if (isset($_GET['slug'])) {
            return HtmlPurifier::process($_GET['slug']);
        }
        return false;
    }

    /**
     * Returns HTML content of block
     * Usage: \Yii::$app->kato->block('block_slug');
     * @param null $slug
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function globalBlock($slug = null)
    {
        if (is_null($slug)) {
            throw new BadRequestHttpException('Block slug not specified.');
        }

        $model = Block::find()
            ->where(['title' => $slug])
            ->one();

        if (is_null($model)) {
            return false;
        }

        return $model->render();
    }

    /**
     * Uploads the file
     * if success returns json array for media data
     * Usage: echo \Yii::$app->kato->mediaUpload();
     * 
     * @param string $fileName
     * @param bool $useFile
     * @return bool|string
     * @throws \yii\web\BadRequestHttpException
     */
    public function mediaUpload($fileName = 'file', $useFile = false)
    {
        if (isset($_FILES[$fileName])) {
            $media = new \backend\models\Media();
            $uploadTime = date("Y-m-W");
            if ($useFile === false) {
                $file = UploadedFile::getInstanceByName($fileName);
            } else {
                $files = UploadedFile::getInstancesByName($fileName);
                $file = $files[0];
            }

            if ($file->size > Yii::$app->params['maxUploadSize']) {
                throw new BadRequestHttpException('Max upload size limit reached');
            }

            $media->filename = KatoBase::sanitizeFile($file->baseName). '-' . KatoBase::genRandomString(4) . '.' . $file->extension;
            $media->mimeType = $file->type;
            $media->byteSize = $file->size;
            $media->extension = $file->extension;
            $media->source = basename(\Yii::$app->params['uploadPath']) . '/' . $uploadTime . '/' . $media->filename;

            //Save to media table
            if ($media->save(false)) {
                //If saved upload the file
                $uploadPath = \Yii::$app->params['uploadPath'] .  $uploadTime;
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

                if ($file->saveAs($uploadPath . '/' . $media->filename)) {
                    return Json::encode($media);
                }
            }
        }

        return false;
    }

    public function menuItems()
    {
        $items = [];

        $pages = Page::find()
            ->where([
                'parent_id' => 0,
                'menu_hidden' => Page::MENU_HIDDEN_NO,
                'status' => Page::STATUS_PUBLISHED,
                'deleted' => 0,
                'revision_to' => 0,
            ])
            ->orderBy('listing_order ASC')
            ->all();

        foreach ($pages  as $page) {
            $items[] = $this->renderMenuItem($page);
        }

        return $items;
    }

    private function menuChildren($childern)
    {
        $items = [];
        foreach ($childern as $page) {
            $items[] = $this->renderMenuItem($page);
        }

        return $items;
    }

    private function renderMenuItem($page)
    {
        $item = ['label' => $page->menu_title, 'url' => $page->permalink, 'active' => false];

        if ($page->type === Page::TYPE_STATIC) {
            if ($page->slug == Yii::$app->kato->pageSlug()) {
                $item['active'] = true;
            }
        } else {
            if ('/' . $page->slug == \Yii::$app->request->getUrl()) {
                $item['active'] = true;
            }
        }

        if ($page->menuChildren) {
            $item['items'] = $this->menuChildren($page->menuChildren);
        }

        return $item;
    }
}