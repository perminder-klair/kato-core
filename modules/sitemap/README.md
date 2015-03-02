# Sitemap Module

## Usage
- Add this to your application configuration

```
'modules' => [
    'sitemap' => [
        'class' => 'kato\modules\sitemap\Sitemap',
    ],
],
```

- Add a new rule for urlManager of your application's configuration file, for example:

```
['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
```
