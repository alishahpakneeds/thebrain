<?php

/*
 * setting url for web module
 * to beautify url in user's site
 * author:ubd
 */
$rules_web = array(
    /** other urls * */
    '<controller:\w+>/<id:\d+>' => '<controller>/view',
    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    '' => '/web/default/index',
    '<lang:[\w-\.]+>/home' => '/web/default/index',
    '<lang:[\w-\.]+>/<slug:[\w-\.]+>/article' => '/web/article/index',
    '<lang:[\w-\.]+>/<slug:[\w-\.]+>/blog-detail' => '/web/blog/detail',
    '<lang:[\w-\.]+>//social-login/<provider:[\w-\.]+>' => '/web/hybrid/login',
    '<lang:[\w-\.]+>/blog' => '/web/blog/index',
    '<lang:[\w-\.]+>/faq' => '/web/faq/index',
    '<lang:[\w-\.]+>/dashboard' => '/web/user/dashboard',
    '<lang:[\w-\.]+>/my-messages' => '/web/user/messages',
    '<lang:[\w-\.]+>/my-offers' => '/web/userdata/myoffers',
    '<lang:[\w-\.]+>/my-orders' => '/web/userdata/myorders',
    '<lang:[\w-\.]+>/my-settings' => '/web/userdata/settings',
    '<lang:[\w-\.]+>/<type:[\w-\.]+>/my-payments' => '/web/userdata/payment',
    '<lang:[\w-\.]+>/<storeurl:[\w-\.]+>/store/<id:[\w-\.]+>' => '/web/userdata/store',
    '<lang:[\w-\.]+>/my-payments' => '/web/userdata/payment',
    '<lang:[\w-\.]+>/ratings' => '/web/userdata/ratings',
    '<lang:[\w-\.]+>/view/profile/user' => '/web/user/profileview',
    '<lang:[\w-\.]+>/edit/profile/user' => '/web/user/profile',
    '<lang:[\w-\.]+>/offers/category/<category:[\w-\.]+>/' => '/web/offers/category',
    '<lang:[\w-\.]+>/offers/search-result' => '/web/offers/search',
    '<lang:[\w-\.]+>/offers/detail/<slug:[\w-\.]+>' => '/web/offers/detail',
    '<lang:[\w-\.]+>/offers/post/<action:[\w-\.]+>/<slug:[\w-\.]+>' => '/web/offers/post',
    '<lang:[\w-\.]+>/offers/post/<action:[\w-\.]+>' => '/web/offers/post',
    '<lang:[\w-\.]+>/offers/delete-offer/<id:[\w-\.]+>/' => '/web/offers/deleteOffer',
    '<lang:[\w-\.]+>/offers/change/status/<id:[\w-\.]+>/' => '/web/offers/changeStatus',
);
?>
