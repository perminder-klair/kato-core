<?php

namespace kato\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class FindReplace extends Behavior
{
    /**
     * @var string attribute
     */
    public $attribute;

    /**
     * Can be strong or array
     * @var string|array findText
     */
    public $findText;

    /**
     * @var string replaceText
     */
    public $replaceText;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'findReplaceValue',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'findReplaceValue',
        ];
    }

    /**
     * Find and replace text
     * Accepts find as string or array
     */
    public function findReplaceValue()
    {
        if (is_array($this->findText)) {
            //can be array
            $searchReplaceArray = [];
            foreach ($this->findText as $key => $value) {
                $searchReplaceArray[$value] = $this->replaceText;
            }
        } else {
            $searchReplaceArray = [
                $this->findText => $this->replaceText,
            ];
        }

        $this->owner->{$this->attribute} = str_replace(
            array_keys($searchReplaceArray),
            array_values($searchReplaceArray),
            $this->owner->{$this->attribute}
        );
    }
}