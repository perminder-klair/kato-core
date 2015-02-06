<?php

namespace kato\modules\setting;

/**
 * Class Setting
 * @package kato\modules\setting
 *
 * @property string $adminLayout
 */
class Setting extends \yii\base\Module
{
    public $controllerNamespace = 'kato\modules\setting\controllers';

    public $adminLayout = null;

    /*
     * Categories for settings
     */
    public $categories = [
        'general',
    ];

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
