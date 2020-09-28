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
        'createTicketUrl' => '/feedback/create?id_customer={{id_customer}}&id_source={{source}}&conversation_detail_ids={{msg_ids}}',
    ],
],
```


Allow Transfer and Unlock Permission
  -
  * Add 2 permission : SocialUnlockChat,SocialTransferChat,SocialRevokeChat
  * Assign Permission to user or role
  * User Role Admin always allow 
  
  
Params: 
  - 
   * {{id_customer}} :  customer id
   * {{source}} : number 1: FACEBOOK, 2 : VIBER, 3: Zalo, 4: Live Helper Chat
   * {{msg_ids}}: selected messages,  format : 1,2,3 