<?php

namespace kato\modules\setting\models;

use Yii;
use kato\modules\setting\Setting as SettingModule;

/**
 * This is the model class for table "kato_setting".
 *
 * @property integer $id
 * @property string $define
 * @property string $value
 * @property string $category
 * @property string $type
 * @property string $options
 */
class Setting extends \kato\ActiveRecord
{
    const TYPE_TEXT_AREA = 'text-area';
    const TYPE_TEXT_FIELD = 'text-field';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kato_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['define'], 'required'],
            [['value', 'options'], 'string'],
            [['define', 'category', 'type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'define' => 'Define',
            'value' => 'Value',
            'category' => 'Category',
            'type' => 'Type',
            'options' => 'Options',
        ];
    }

    /*
     * Returns categories defined in module
     */
    public function getCategories()
    {
        $module = SettingModule::getInstance();
        return $module->categories;
    }

    /**
     * @return string
     */
    public function defineEncoded()
    {
        return ucwords(str_replace('_', ' ', $this->define));
    }

    /**
     * Returns settings grouped by categories
     * @return array
     */
    public function getByCategories()
    {
        $data = [
            'categories' => $this->getCategories(),
            'settings' => [],
        ];

        foreach ($data['categories'] as $key => $category) {
            $model = self::find()
                ->where(['category' => $category])
                ->indexBy('id')
                ->all();
            $data['settings'][$category] = $model;
        }

        return $data;
    }

    /**
     * Returns settings value
     * @return string
     */
    public function renderOutput()
    {
        return $this->value;
    }

    /**
     * Returns buttons array to show on redactor editor
     * Eg: can be set in database as comma separated in column options as: html, unorderedlist, link
     * @return array
     */
    public function redactorOptions()
    {
        if (strlen($this->options) > 1) {
            $options = str_replace(' ', '', $this->options);
            return explode(",", $options);
        }

        return ['html', 'formatting', 'bold', 'italic', 'deleted',
            'unorderedlist', 'orderedlist', 'outdent', 'indent',
            'image', 'file', 'link', 'alignment', 'horizontalrule'];
    }
}
