<?php

namespace kato\components;

class UrlManager extends \yii\web\UrlManager
{
    public $adminUrl;

    public function createSiteUrl($params)
    {
        return str_replace($this->adminUrl . "/", "", $this->createUrl($params));
    }

    public function createAdminUrl($params)
    {
        if ($this->getBaseUrl() == '/' . $this->adminUrl) {
            return $this->createUrl($params);
        }

        return '/admin' . $this->createUrl($params);
    }
}