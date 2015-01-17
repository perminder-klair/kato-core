<?php

namespace kato\behaviors;

use kato\modules\tag\models\Tag;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class NormalizeTags extends Behavior {

    /**
     * @var string attribute
     */
    public $attribute = "tags";

    public $updateTags = false;
    private $oldTags = null;
    public $tagType = null;

    /**
     * @inheritdoc
     */
    public function events()
    {
        $return = [];
        $return[ActiveRecord::EVENT_BEFORE_VALIDATE] = 'normalize';
        if ($this->updateTags === true) {
            $return[ActiveRecord::EVENT_AFTER_FIND] = 'duplicateTags';
            $return[ActiveRecord::EVENT_AFTER_UPDATE] = 'updateTags';
        }

        return $return;
    }

    /**
     * Normalizes the user-entered tags.
     */
    public function normalize()
    {
        if (!empty($this->owner->{$this->attribute})) {
            $this->owner->{$this->attribute} = $this->array2string(array_unique($this->string2array($this->owner->{$this->attribute})));
        }
    }

    public function updateTags()
    {
        $oldTags = $this->string2array($this->oldTags);
        $newTags = $this->string2array($this->owner->{$this->attribute});

        Tag::addTags(array_values(array_diff($newTags,$oldTags)), $this->tagType);
        Tag::removeTags(array_values(array_diff($oldTags,$newTags)), $this->tagType);
    }

    public function duplicateTags()
    {
        if (!empty($this->owner->{$this->attribute})) {
            $this->oldTags = $this->owner->{$this->attribute};
        }
    }

    private function string2array($tags)
    {
        return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
    }

    private function array2string($tags)
    {
        return implode(', ',$tags);
    }
}