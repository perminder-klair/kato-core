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

    /**
     * Evaluates the value of the specified attribute for the given model.
     * The attribute name can be given in a dot syntax. For example, if the attribute
     * is "author.firstName", this method will return the value of "$model->author->firstName".
     * A default value (passed as the last parameter) will be returned if the attribute does
     * not exist or is broken in the middle (e.g. $model->author is null).
     * The model can be either an object or an array. If the latter, the attribute is treated
     * as a key of the array. For the example of "author.firstName", if would mean the array value
     * "$model['author']['firstName']".
     *
     * @param mixed $model the model. This can be either an object or an array.
     * @param mixed $attribute the attribute name (use dot to concatenate multiple attributes)
     * or anonymous function (PHP 5.3+). Remember that functions created by "create_function"
     * are not supported by this method. Also note that numeric value is meaningless when
     * first parameter is object typed.
     * @param mixed $defaultValue the default value to return when the attribute does not exist.
     * @return mixed the attribute value.
     */
    public static function value($model,$attribute,$defaultValue=null)
    {
        if(is_scalar($attribute) || $attribute===null)
            foreach(explode('.',$attribute) as $name)
            {
                if(is_object($model) && isset($model->$name))
                    $model=$model->$name;
                elseif(is_array($model) && isset($model[$name]))
                    $model=$model[$name];
                else
                    return $defaultValue;
            }
        else
            return call_user_func($attribute,$model);
        return $model;
    }

    /**
     * Generates the data suitable for list-based HTML elements.
     * The generated data can be used in {@link dropDownList}, {@link listBox}, {@link checkBoxList},
     * {@link radioButtonList}, and their active-versions (such as {@link activeDropDownList}).
     * @param $models
     * @param $valueField
     * @param $textField
     * @param string $groupField
     * @return array
     */
    public static function listData($models, $valueField, $textField, $groupField='')
    {
        $listData=array();

        if($groupField==='')
        {
            foreach($models as $model)
            {
                $value=self::value($model,$valueField);
                $text=self::value($model,$textField);
                $listData[$value]=$text;
            }
        }
        else
        {
            foreach($models as $model)
            {
                $group=self::value($model,$groupField);
                $value=self::value($model,$valueField);
                $text=self::value($model,$textField);
                if($group===null)
                    $listData[$value]=$text;
                else
                    $listData[$group][$value]=$text;
            }
        }
        return $listData;
    }
}