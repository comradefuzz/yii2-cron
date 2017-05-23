Yii2 cron extension
===================
Allows to define and execute cronjobs in Yii2 application

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist comradefuzz/yii2-cron "*"
```

or add

```
"comradefuzz/yii2-cron": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Add cron console controller to your config and define your jobs  :

```php
    'controllerMap' => [
            'cron' => [
                'class' => 'comradefuzz
                \yii2cron\CronController',
                'crontab' => [
                    ['* * * * *',  'yii app/my-useful-action'],
                    ['* * * * *',  'echo "ONE MORE"'],
                    ['*/5 * * * *',  'echo "Not so fast"'],
                ],
                'log' => true, // Enables logging (default `false`)
                'logCategory' => 'crontab', // Log category (default `crontab`)
            ],
        ]
```
        
        
Then configure your server crontab:

```bash
* * * * * /path/to/yii cron > /dev/null 2>&1
```
