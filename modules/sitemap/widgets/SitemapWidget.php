<?php

namespace kato\modules\sitemap\widgets;

use kato\modules\sitemap\Sitemap;
use Yii;
use yii\base\Widget;

class SitemapWidget extends Widget
{
    public $moduleName = 'sitemap';

    /**
     * Gets the module
     * @param $m
     * @return null|\yii\base\Module
     */
    public function getModule($m)
    {
        $mod = Yii::$app->controller->module;
        return $mod && $mod->getModule($m) ? $mod->getModule($m) : Yii::$app->getModule($m);
    }

    public function run()
    {
        /** @var \kato\modules\sitemap\Sitemap $module */
        $module = $this->getModule($this->moduleName);
        $sitemapData = $module->buildSitemap(true); //as array
//dump($sitemapData);exit;
        return $this->render('sitemap', [
            'data' => $sitemapData,
        ]);
    }
}
