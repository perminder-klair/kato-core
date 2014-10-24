<?php

namespace kato;

use Yii;

Yii::setAlias('kato', __DIR__);

/**
 * This asset bundle provides the base javascript files for the Kato.
 */
class KatoAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@kato/assets';

    public $css = [
        'css/plugins.css',
        'css/main.css',
        'css/themes.css',
    ];

    public $js = [
        'js/vendor/bootstrap.min.js',
        'js/plugins.js',
        'js/main.js',
        'js/vendor/modernizr-2.6.2-respond-1.3.0.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
