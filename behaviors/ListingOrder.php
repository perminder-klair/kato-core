<?php

namespace kato\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class ListingOrder extends Behavior {

    /**
     * @var string attribute
     */
    public $attribute = "listing_order";

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

        if ($this->getLastRow()) {
            $id = $this->getLastRow()->id + 1;
        } else {
            $id = 1;
        }
        $this->owner->{$this->attribute} = $id;

        return true;
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