<?php

namespace kato;

class ActiveRecord extends \yii\db\ActiveRecord
{

    /**
     * Attached Content Media, by type
     * @return static
     */
    public function getContentMedia()
    {
        return $this->hasMany(\backend\models\ContentMedia::className(), ['content_id' => 'id'])
            ->where('media_type = :type', [':type' => $this->className()]);
    }

    /**
     * Relate Media
     * Usage: $model->media();
     * @return static
     */
    public function getMedia()
    {
        return $this->hasMany(\backend\models\Media::className(), ['id' => 'media_id'])
            ->via('contentMedia');
    }

    /**
     * Actions to be taken before saving the record.
     * @param bool $insert
     * @return bool whether the record can be saved
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $user_id = \Yii::$app->user->id;

            if ($this->isNewRecord) {
                if ($this->hasAttribute('created_by'))
                    $this->created_by = $user_id;
            } else {
                if ($this->hasAttribute('updated_by'))
                    $this->updated_by = $user_id;
            }
            return true;
        }
        return false;
    }

    /**
     * Return basic select options for the record.
     * @param string $key
     * @param string $value
     * @return array
     */
    public static function getSelectOptions($key = 'id', $value = 'title')
    {
        $parents = self::find()
            ->all();

        return \yii\helpers\ArrayHelper::map($parents, $key, $value);
    }

    /**
     * Return last row inserted
     * @return mixed
     */
    public function getLastRow()
    {
        return static::find()
            ->orderBy('id DESC')
            ->one();
    }
}
