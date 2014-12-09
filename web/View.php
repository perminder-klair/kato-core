<?php

namespace kato\web;

use backend\models\Block;
use backend\models\Page;
use Yii;
use yii\web\BadRequestHttpException;

class View extends \yii\web\View
{
    public $description;
    public $pageIcon;

    /**
     * Register required components for admin theme
     */
    /*public function registerTheme()
    {
        $this->registerMetaTag([
            'charset' => Yii::$app->charset,
        ]);
        $this->registerMetaTag([
            'name' => 'robots',
            'content' => 'noindex, nofollow',
        ]);
        $this->registerMetaTag([
            'name' => 'viewport',
            'content' => 'width=device-width, initial-scale=1',
        ]);

        //The Open Sans font is included from Google Web Fonts
        $this->registerLinkTag([
            'rel' => 'stylesheet',
            'href' => 'http://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,700,700italic',
        ]);
    }*/

    /**
     * finds and return content of requested block by name
     * @param $name
     * @param bool $isGlobal
     * @return bool|string
     * @throws BadRequestHttpException
     */
    public function loadBlock($name, $isGlobal = false)
    {
        if (is_null($name)) {
            throw new BadRequestHttpException('Block name not specified.');
        }

        $find = [
            'title' => $name,
        ];

        if ($isGlobal === false) {
            //if not global
            if (isset($this->params['block']['id'])) {
                $find['parent'] = $this->params['block']['id'];
            }
            if (isset($this->params['block']['layout'])) {
                $find['parent_layout'] = $this->params['block']['layout'];
            }
            if (isset($this->params['block']['slug'])) {
                //get parent page id
                if ($page = Page::findOne([
                    'slug' => $this->params['block']['slug'],
                ])) {
                    $find['parent'] = $page->id;
                }
            }
        }


        if ($block = Block::findOne($find)) {
            //if block found
            return $block->render();
        }

        return false;
    }
}