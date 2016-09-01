快递鸟API for Yii 2
========================
官方的文档比较混乱, 提供的demo很原生态, 故制作此模块

目前已实现:
- [x] 即时查询接口
- [x] 订阅查询接口
- [ ] 在线下单
- [ ] 电子面单
- [x] 单号识别

官方文档: (http://www.kdniao.com/api-track)

需要`APP ID`和`APP KEY`, 请注册申请后, 在后台查看(http://www.kdniao.com/UserCenter/Index.aspx)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --dev --prefer-dist raysoft/kdniao
```

or add

```
"raysoft/kdniao": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'components' => [
        'kdniao' => [
            'class' => 'raysoft\kdniao\Kdniao',
            'appId' => '商户ID',
            'appKey' => 'APP Key',
        ],
        // ...
    ],
    // ...
];
```

You can then access this module in your code:

```
Yii::$app->kdniao...
```

即时查询接口:
```
Yii::$app->kdniao->track('快递单号', '快递公司代码(不知道可留空)');
```

单号识别:
```
Yii::$app->kdniao->shipper('快递单号');
```
