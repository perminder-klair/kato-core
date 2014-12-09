<?php

namespace kato\components;

use backend\models\Page;
use Yii;
use backend\models\Setting;
use backend\models\Block;
use yii\helpers\HtmlPurifier;
use yii\web\BadRequestHttpException;

/**
 * Usage
 * -----
 *
 * In your configuration file, add the setting component.
 *
 * ```php
 * 'components' => [
 *    ...
 *    'kato' => 'kato\components\Kato',
 *    ...
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

        foreach ($pages as $page) {
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