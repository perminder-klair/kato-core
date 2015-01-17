<?php

namespace kato\behaviors;

use yii\base\Behavior;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\helpers\Inflector;
use yii\validators\UniqueValidator;

/**
 * Class Slug
 * @package kato
 */
class Slug extends Behavior
{
    public $sourceAttributeName = 'name';
    public $slugAttributeName = 'slug';
    //The replacement to use for spaces in the slug
    public $replacement = '-';
    // Whether to return the string in lowercase or not
    public $lowercase = true;
    //Check if the slug value is unique, add number if not
    public $unique = true;
    //Only set if source attribute is empty
    public $onlyIfEmpty = false;

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'generateSlug'
        ];
    }

    public function generateSlug()
    {
        if (($this->onlyIfEmpty === true) || (empty($this->owner->{$this->slugAttributeName}) && !empty($this->owner->{$this->sourceAttributeName}))) {
            $slug = Inflector::slug(
                $this->owner->{$this->sourceAttributeName},
                $this->replacement,
                $this->lowercase
            );
            $this->owner->{$this->slugAttributeName} = $slug;

            if ($this->unique) {
                $suffix = 1;
                while (!$this->uniqueCheck()) {
                    $this->owner->{$this->slugAttributeName} = $slug . $this->replacement . ++$suffix;
                }
            }
        } else {
            $slug = Inflector::slug(
                $this->owner->{$this->slugAttributeName},
                $this->replacement,
                $this->lowercase
            );

            $this->owner->{$this->slugAttributeName} = $slug;
        }
    }

    public function uniqueCheck()
    {
        if ($this->owner instanceof ActiveRecordInterface) {
            /** @var Model $model */
            $model = clone $this->owner;
            $uniqueValidator = new UniqueValidator;
            $uniqueValidator->validateAttribute($model, $this->slugAttributeName);
            return !$model->hasErrors($this->slugAttributeName);
        }

        throw new Exception('Can\'t check if the slug is unique.');
    }
}