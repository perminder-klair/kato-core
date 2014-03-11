<?php

namespace kato\components;

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
    public function block($slug = null)
    {
        if (is_null($slug)) {
            throw new BadRequestHttpException('Block slug not specified.');
        }

        $model = Block::find()
            ->where(['slug' => $slug])
            ->one();

        if (is_null($model)) {
            return false;
        }

        //TODO check parent
        if (!is_null($model->parent)) {
            //var_dump($model->parent);exit;
        }

        return $model->content_html;
    }
}