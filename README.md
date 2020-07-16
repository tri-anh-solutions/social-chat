Tri Anh Social Chat
===================
Tri Anh Social Chat Tools

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tas/social "*"
```

or add

```
"tas/social": "*"
```

to the require section of your `composer.json` file.


```bash
php yii migrate --migration-path=@tas/social/migrations
```

add config module

```php
'modules'      => [
    'social' => [
        'class' => 'tas\social\Module',
    ],
],
```


Allow Transfer and Unlock Permission
  -
  * Add 2 permission : SocialUnlockChat,SocialTransferChat
  * Assign Permission to user or role
  * User Role Admin always allow 