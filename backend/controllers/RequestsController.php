<?php

namespace app\controllers;

use app\models\Request;
use Throwable;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\rest\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Requests API",
 *         version="1.0.0",
 *         description="API для работы с заявками"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT",
 *             description="Используйте токен: 100-token или 101-token"
 *         )
 *     )
 * )
 */
class RequestsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Max-Age' => 3600,
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['update', 'delete'],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/requests",
     *     summary="Получить список заявок",
     *     description="Возвращает список всех заявок с возможностью фильтрации, сортировки и пагинации.",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Фильтр по статусу",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Список заявок")
     * )
     */
    public function actionIndex(): array
    {
        $request = Yii::$app->request;
        $query = (new Query())
            ->select(['id', 'name', 'email', 'status', 'created_at', 'updated_at'])
            ->from(Request::tableName())
            ->filterWhere(['status' => $request->get('status') ?: null])
            ->andFilterWhere(['>=', 'created_at', $request->get('created_from') ?: null])
            ->andFilterWhere(['<=', 'created_at', $request->get('created_to') ?: null])
            ->andFilterWhere(['>=', 'updated_at', $request->get('updated_from') ?: null])
            ->andFilterWhere(['<=', 'updated_at', $request->get('updated_to') ?: null]);
        if ($sort = $request->get('sort')) {
            $order = $request->get('order', 'asc');
            $query->orderBy([$sort => $order === 'asc' ? SORT_ASC : SORT_DESC]);
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSizeLimit' => [1, 50],
            'defaultPageSize' => 10,
        ]);
        if ($pagination->getPage() > $pagination->getPageCount() - 1) {
            $pagination->setPage($pagination->getPageCount() - 1);
        }

        $requests = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return [
            'requests' => $requests,
            'pagination' => [
                'totalCount' => $pagination->totalCount,
                'pageSize' => $pagination->pageSize,
                'pageCount' => $pagination->getPageCount(),
                'page' => $pagination->getPage() + 1,
            ],
        ];
    }

    /**
     * @OA\Get(
     *     path="/requests/{id}",
     *     summary="Просмотр заявки по ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заявки",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Заявка найдена"),
     *     @OA\Response(response=404, description="Заявка не найдена")
     * )
     * @throws NotFoundHttpException
     */
    public function actionView($id): array
    {
        $fields = Yii::$app->request->get('fields');
        $select = $fields ? explode(',', $fields) : ['*'];
        $request = Request::find()
            ->select($select)
            ->where(['id' => $id])
            ->one();
        if (!$request) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        return $request->toArray();
    }

    /**
     * @OA\Post(
     *     path="/requests",
     *     summary="Создать новую заявку",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "email", "message"},
     *                 @OA\Property(property="name", type="string", example="Иван"),
     *                 @OA\Property(property="email", type="string", example="ivan@example.com"),
     *                 @OA\Property(property="message", type="string", example="Помогите, не работает сайт")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заявка создана"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @throws Exception
     */
    public function actionCreate(): array
    {
        $request = new Request();
        $data = Yii::$app->request->post();
        if ($request->load($data, '') && $request->validate()) {
            $request->save();
            return [
                'success' => true,
                'id' => $request->id,
            ];
        }
        return [
            'success' => false,
            'errors' => $request->getErrors(),
        ];
    }

    /**
     * @OA\Put(
     *     path="/requests/{id}",
     *     summary="Обновить заявку и отправить email",
     *     description="Обновляет статус и комментарий к заявке, затем отправляет письмо пользователю",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заявки",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"status","comment"},
     *                 @OA\Property(property="status", type="integer", example=2, description="Статус заявки"),
     *                 @OA\Property(property="comment", type="string", example="Ваша проблема решена")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заявка обновлена и письмо отправлено"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заявка не найдена"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизован"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации"
     *     )
     * )
     * @throws NotFoundHttpException|Exception
     */
    public function actionUpdate($id): array
    {
        $request = Request::findOne($id);
        if (!$request) {
            throw new NotFoundHttpException('Заявка не найдена');
        }

        $data = Yii::$app->request->post();

        if (!$request->load($data, '') || !$request->save()) {
            return [
                'success' => false,
                'errors' => $request->getErrors(),
            ];
        }

        Yii::$app->mailer->compose()
            ->setTo($request->email)
            ->setFrom('noreply@example.com')
            ->setSubject('Ответ на вашу заявку')
            ->setTextBody("Здравствуйте, {$request->name}!\n\n{$request->comment}")
            ->send();

        return [
            'success' => true,
            'message' => 'Заявка успешно обновлена и письмо отправлено',
        ];
    }

    /**
     * @OA\Delete(
     *     path="/requests/{id}",
     *     summary="Удалить заявку",
     *     security={{"bearerAuth": {}}},
     *     description="Удаляет заявку из базы данных",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Заявка удалена"),
     *     @OA\Response(response=401, description="Не авторизован"),
     *     @OA\Response(response=404, description="Заявка не найдена")
     * )
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): array
    {
        $request = Request::findOne(['id' => $id]);
        if (!$request) {
            throw new NotFoundHttpException('Заявка не найдена');
        }
        try {
            return ['success' => (bool)$request->delete()];
        } catch (StaleObjectException $e) {
            return ['success' => false];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
