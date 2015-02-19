<?php

namespace kato\modules\media;

use Yii;
use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{
    public function __construct()
    {
        Yii::setAlias('@media', dirname(__FILE__));
    }

    public $sourcePath = '@media/assets';

    public $css = [
        'media.css'
    ];

    public $js = [
        'media.js'
    ];

    public $depends = [
        '\kato\BowerAsset',
        '\dosamigos\editable\EditableSelect2Asset'
    ];
}