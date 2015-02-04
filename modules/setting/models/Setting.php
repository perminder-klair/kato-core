<?php

namespace kato\modules\setting\models;

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

    public $categories = [
        'general',
        'footer',
    ];

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
            'categories' => $this->categories,
            'settings' => [],
        ];

        foreach ($this->categories as $key => $category) {
            $model = self::find()
                ->where(['category' => $category])
                ->indexBy('id')
                ->all();
            $data['settings'][$category] = $model;
        }

        return $data;
    }

    public function renderOutput()
    {
        return $this->value;
    }
}
