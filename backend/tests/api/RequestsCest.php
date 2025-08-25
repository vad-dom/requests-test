<?php

class RequestsCest
{
    /**
     * Тест: обновление заявки с корректным токеном
     */
    public function updateRequestWithAuth(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer 100-token');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPUT('/requests/2', [
            'status' => 2,
            'comment' => 'Ваша проблема решена',
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'message' => 'Заявка успешно обновлена и письмо отправлено'
        ]);
    }

    /**
     * Тест: обновление заявки с неверным токеном
     */
    public function updateRequestWithInvalidAuth(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer WRONG_TOKEN');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        $I->sendPUT('/requests/2', [
            'status' => 2,
            'comment' => 'Ваша проблема решена',
        ]);

        $I->seeResponseCodeIs(401);
    }

    /**
     * Тест: обновление заявки без токена
     */
    public function updateRequestWithoutAuth(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPUT('/requests/2', [
            'status' => 2,
            'comment' => 'Ваша проблема решена',
        ]);

        $I->seeResponseCodeIs(401);
    }
}

