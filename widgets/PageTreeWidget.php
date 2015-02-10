<?php

namespace kato\widgets;

use yii\base\Widget;
use backend\models\Page;

class PageTreeWidget extends Widget
{
    /**
     * @return string
     */
    public function run()
    {
        return $this->render('pageTree', [
            'dataProvider' => $this->renderTree(),
        ]);
    }

    /**
     * Returns hierarchical data of pages
     * @param int $parentId
     * @return array
     */
    private function renderTree($parentId = 0)
    {
        $query = Page::find();
        $query->andWhere(['deleted' => 0, 'revision_to' => 0, 'parent_id' => $parentId]);
        $query->orderBy('title ASC');

        if (($pages = $query->all()) == null) {
            //empty array if no data found
            return [];
        }

        $tree = $this->getBranch($pages, true);

        return $tree;
    }

    /**
     * Returns the given pages as a branch.
     * @param $pages
     * @return array
     */
    protected function getBranch($pages)
    {
        $result = [];

        foreach ($pages as $page) {
            $result[] = array('model' => $page, 'children' => $this->renderTree($page->id));
        }

        return $result;
    }

    private function registerCss()
    {
        $this->registerCss("body { background: #f00; }");
    }
}
