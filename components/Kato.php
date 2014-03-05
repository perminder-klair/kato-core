<?php

namespace kato\components;

use Yii;
use yii\db\Schema;
use yii\base\Application;
use backend\models\Setting;

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
}