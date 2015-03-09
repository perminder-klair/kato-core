<?php

namespace kato\modules\sitemap;

use Yii;
use yii\base\InvalidConfigException;
use yii\caching\Cache;

/**
 * Class Sitemap
 * @package kato\modules\sitemap
 */
class Sitemap extends \yii\base\Module
{
    public $controllerNamespace = 'kato\modules\sitemap\controllers';

    /** @var int */
    public $cacheExpire = 86400;

    /** @var Cache|string */
    public $cacheProvider = 'cache';

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean Use php's gzip compressing. */
    public $enableGzip = false;

    /** @var array */
    public $models = [];

    /** @var array */
    public $urls = [];

    public function init()
    {
        parent::init();

        if (is_string($this->cacheProvider)) {
            $this->cacheProvider = Yii::$app->{$this->cacheProvider};
        }
        if (!$this->cacheProvider instanceof Cache) {
            throw new InvalidConfigException('Invalid `cacheKey` parameter was specified.');
        }
    }

    /**
     * Build and cache a site map.
     * @param bool $returnAsArray
     * @return array|string
     * @throws InvalidConfigException
     */
    public function buildSitemap($returnAsArray = false)
    {
        $urls = $this->urls;
        foreach ($this->models as $modelName) {
            /** @var \kato\modules\sitemap\behaviors\SitemapBehavior $model */
            if (is_array($modelName)) {
                $model = new $modelName['class'];
                if (isset($modelName['behaviors'])) {
                    $model->attachBehaviors($modelName['behaviors']);
                }
            } else {
                $model = new $modelName;
            }
            $urls = array_merge($urls, $model->generateSiteMap());
        }

        if ($returnAsArray === true) {
            return $urls;
        }

        $sitemapData = $this->createControllerByID('default')->renderPartial('index', [
            'urls' => $urls
        ]);

        $this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);
        return $sitemapData;
    }
}
