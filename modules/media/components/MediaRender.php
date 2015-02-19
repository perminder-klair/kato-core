<?php

namespace kato\modules\media\components;

use kato\modules\media\models\Media;
use Yii;

/**
 * Usage
 * -----
 *
 * In your configuration file, add the setting component.
 *
 * ```php
 * 'components' => [
 *    ...
 *    'media' => 'kato\modules\media\components\MediaRender',
 *    ...
 * ]
 * ```
 */
class MediaRender extends \yii\base\Component
{

    /**
     * Render remote images
     * Usage: echo Yii::$app->media->render('HTTP://URL_TO_IMAGE_HERE.JPG')
     * @param $imgSrc
     * @param array $data
     * @return bool|string
     */
    public function render($imgSrc, $data = [])
    {
        $media = new Media();
        $media->mimeType = 'image/jpeg';
        $media->filename = basename($imgSrc);
        $media->source = $imgSrc;
        $media->setBaseSourceUrl($imgSrc);

        return $media->render($data);
    }

}