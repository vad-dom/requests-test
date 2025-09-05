<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

$dotEnv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotEnv->safeLoad();

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ .'/../vendor/autoload.php';