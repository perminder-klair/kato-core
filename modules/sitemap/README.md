# Sitemap Module

## Usage

- Configure the cache component of your application's configuration file, for example:
```php
'components' => [
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
]
```
- Add this to your application configuration

```php
'modules' => [
    'sitemap' => [
        'class' => 'kato\modules\sitemap\Sitemap',
         'models' => [
             'backend\models\Blog',
             'backend\models\Page',
         ],
         'enableGzip' => true, // default is false
         'cacheExpire' => 1, // 1 second. Default is 24 hours
    ],
],
```

- Add behavior in the AR models, for example:

```php
use kato\modules\sitemap\behaviors\SitemapBehavior;

public function behaviors()
{
    return [
        'sitemap' => [
            'class' => SitemapBehavior::className(),
            'scope' => function ($model) {
                /** @var \yii\db\ActiveQuery $model */
                $model->select(['url', 'lastmod']);
                $model->andWhere(['is_deleted' => 0]);
            },
            'dataClosure' => function ($model) {
                /** @var self $model */
                return [
                    'loc' => Url::to($model->url, true),
                    'lastmod' => strtotime($model->lastmod),
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8
                ];
            }
        ],
    ];
}
```

- Add a new rule for urlManager of your application's configuration file, for example:

```php
['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
```
