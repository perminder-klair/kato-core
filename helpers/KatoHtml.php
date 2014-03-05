<?php

namespace kato\helpers;

use Yii;
use yii\web\BadRequestHttpException;

class KatoHtml extends \yii\base\Object
{
    /**
     * Generates link to page
     * Usage: \kato\helpers\KatoHtml::page('page-slug');
     * @param null $slug
     * @return string
     * @throws \yii\web\BadRequestHttpException
     */
    public static function page($slug = null)
    {	
    	if (is_null($slug)) {
    		throw new BadRequestHttpException('Page slug not specified.');
    	}

    	return Yii::$app->urlManager->createAbsoluteUrl(['page/view', 'slug' => $slug]);
    }
}