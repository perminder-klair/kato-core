<?php

namespace kato\components;

use Yii;
use backend\models\Setting;
use yii\helpers\HtmlPurifier;

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

        if ($model) {
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
        return '';
    }
}