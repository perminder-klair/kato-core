<?php

namespace kato\modules\media;

use Yii;
use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{

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