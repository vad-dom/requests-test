<?php

namespace tests\unit\controllers;

use app\controllers\RequestsController;
use Yii;
use yii\web\Request;
use yii\web\Response;
use Codeception\Test\Unit;

class RequestsControllerTest extends Unit
{
    public function testIndexReturnsJson()
    {
        $controller = new RequestsController('requests', Yii::$app);

        // Мокаем запрос
        Yii::$app->set('request', new Request());
        Yii::$app->set('response', new Response());

        $result = $controller->actionIndex();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('requests', $result);
        $this->assertArrayHasKey('pagination', $result);
    }
}