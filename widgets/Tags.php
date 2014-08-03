<?php

namespace kato\widgets;

use yii\helpers\Html;
use backend\models\Tag;

/**
 * Class Tags
 * Usage:
 * \kato\widgets\Tags::widget([
 *  'model' => $model,
 *  ]);
 *
 * @package kato\widgets
 */
class Tags extends \yii\bootstrap\Widget
{
    public $model;
    public $options = [];
    public $containerOptions = ['class' => 'tags-list'];

    /**
     * @return string
     */
    public function run()
    {
        if ($this->getAllTags()->count() <= 1) {
            return false;
        }

        return Html::tag('ul', $this->getTags(), $this->containerOptions);
    }

    /**
     * Look into Tag model by tag type
     * @return mixed
     */
    private function getAllTags()
    {
        return Tag::find()
            ->where(['tag_type' => $this->model->className()]);
    }

    /**
     * Returns list of tags from model
     * @return string
     */
    private function getTags()
    {
        $return = '';
        foreach ($this->getAllTags()->all() as $tag) {
            if (isset($_GET['tag']) && $_GET['tag'] === $tag->name) {
                $this->options['class'] = 'tag active';
            } else {
                $this->options['class'] = 'tag';
            }
            $return .= Html::tag('li', Html::a($tag->name, ['', $this->get_real_class($this->model) . '[tags]' => $tag->name]), $this->options);
        }

        return $return;
    }

    /**
     * Obtains an object class name without namespaces
     * @param $obj
     * @return string
     */
    private function get_real_class($obj)
    {
        $classname = get_class($obj);

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        return $classname;
    }

}
