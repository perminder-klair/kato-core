<?php

namespace kato\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class DefaultTitle extends Behavior {

    /**
     * @var string attribute
     */
    public $attribute = "title";
    public $defaultPrefix = 'New';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [ActiveRecord::EVENT_BEFORE_INSERT => 'setDefaultValue'];
    }

    public function setDefaultValue()
    {
        if (!empty($this->owner->{$this->attribute})) {
            return true;
        }

        $id = $this->getLastRow()->id + 1;
        $this->owner->{$this->attribute} = $this->defaultPrefix . '-' . $id;
    }

    /**
     * Return last row inserted
     * @return mixed
     */
    public function getLastRow()
    {
        return $this->owner->find()
            ->orderBy('id DESC')
            ->one();
    }
}