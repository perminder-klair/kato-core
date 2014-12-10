<?php

namespace kato;

use kato\modules\media\models\ContentMedia;
use kato\modules\media\models\Media;
use yii\helpers\Inflector;
use ReflectionClass;
use yii\helpers\Url;

class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Attached Content Media, by type
     * @return static
     */
    public function getContentMedia()
    {
        return $this->hasMany(ContentMedia::className(), ['content_id' => 'id'])
            ->where('content_type = :type', [':type' => $this->className()]);
    }

    /**
     * Relate Media
     * Usage: $model->media();
     * @param null $type
     * @return static
     */
    public function getMedia($type = null)
    {
        $media = $this->hasMany(Media::className(), ['id' => 'media_id']);
        if ($type !== null) {
            $media->where('media_type = :type', [':type' => $type]);
        }
        $media->via('contentMedia');

        return $media;
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
     * This method is invoked before deleting a record.
     * The default implementation raises the [[EVENT_BEFORE_DELETE]] event.
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->media) {
                foreach ($this->media as $media)  {
                    $media->delete();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Return basic select options for the record.
     * @param string $key
     * @param string $value
     * @param array $where
     * @return array
     */
    public static function getSelectOptions($key = 'id', $value = 'title', $where = [])
    {
        $parents = self::find();
        if (!empty($where)) {
            $parents->where($where);
        }
        return \yii\helpers\ArrayHelper::map($parents->all(), $key, $value);
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

    /**
     * Returns lists of status available
     * @return array
     */
    public function listStatus()
    {
        $data = [];

        // create a reflection class to get constants
        $refl = new ReflectionClass(get_called_class());
        $constants = $refl->getConstants();

        // check for status constants (e.g., STATUS_ACTIVE)
        foreach ($constants as $constantName => $constantValue) {

            // add prettified name to dropdown
            if (strpos($constantName, "STATUS_") === 0) {
                $prettyName = str_replace("STATUS_", "", $constantName);
                $prettyName = Inflector::humanize(strtolower($prettyName));
                $data[$constantValue] = $prettyName;
            }
        }

        return $data;
    }

    /**
     * Returns status label
     * @return bool
     */
    public function getStatusLabel()
    {
        if ($status =$this->listStatus()) {
            return $status[$this->status];
        }
        return false;
    }

    /**
     * Returns permalink to model
     * @return string
     */
    public function getPermalink() {
        return Url::to(['view', 'id' => $this->id]);
    }

    /**
     * Returns types available
     * @param $type
     * @return array
     */
    public function listTypes($type)
    {
        $data = [];
        // create a reflection class to get constants
        $refl = new ReflectionClass(get_called_class());
        $constants = $refl->getConstants();

        foreach ($constants as $constantName => $constantValue) {

            // add prettified name to dropdown
            if (strpos($constantName, $type) === 0) {
                $prettyName = preg_replace('/' . $type . '/', "", $constantName, 1);
                $prettyName = Inflector::humanize(strtolower($prettyName));
                $data[$constantValue] = trim(ucwords($prettyName));
            }
        }

        return $data;
    }

    /**
     * Returns type label
     * @param $type
     * @param $constId
     * @return bool
     */
    public function getTypeLabel($type, $constId)
    {
        if (!is_null($type)) {
            $array = $this->listTypes($constId);
            if (isset($array[$type])) {
                return $array[$type];
            }
        }

        return false;
    }

    /**
     * return array of listing order
     * @return array
     */
    public function getListingOrderArray()
    {
        $count = static::find()
            ->count();

        $array = [];
        for ($i = 1; $i <= $count; $i++) {
            $array[$i] = $i;
        }

        return $array;
    }
}
