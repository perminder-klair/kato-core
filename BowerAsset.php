<?php

namespace kato;

use Yii;

Yii::setAlias('kato', __DIR__);

/**
 * This asset bundle provides the base javascript files for the Kato.
 */
class BowerAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@kato/bower_components';

    public $css = [
        'lightbox2/css/lightbox.css',
    ];

    public $js = [
        'angular/angular.min.js',
        'lightbox2/js/lightbox.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

}
