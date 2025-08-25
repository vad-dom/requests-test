<?php

namespace tests\unit\controllers;

use app\controllers\RequestsController;
use app\models\Request;
use Yii;
use yii\web\Request as WebRequest;
use yii\web\Response;
use Codeception\Test\Unit;

class RequestsUpdateTest extends Unit
{
    public function testUpdateWithoutAuth()
    {
        $controller = new RequestsController('requests', Yii::$app);

        Yii::$app->set('request', new WebRequest());
        Yii::$app->set('response', new Response());

        $this->expectException(\yii\web\UnauthorizedHttpException::class);

        $controller->actionUpdate(4);
    }
}