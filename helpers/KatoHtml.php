<?php

namespace kato\helpers;

use yii\web\BadRequestHttpException;

class KatoHtml extends \yii\base\Object
{
	/**
	 * Generates link to page
	 * Usage: \kato\helpers\KatoHtml::page('page-slug');
	 */
    public static function page($slug = null)
    {	
    	if (is_null($slug)) {
    		throw new BadRequestHttpException('Page slug not specified.');
    	}

    	return \Yii::$app->urlManager->createAbsoluteUrl('page/view', 'slug' => $slug);
    }
}