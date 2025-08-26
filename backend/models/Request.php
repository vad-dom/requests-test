<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * @OA\Schema(
 *     schema="Request",
 *     type="object",
 *     title="Заявка",
 *     description="Модель заявки, которая хранит данные пользователя и ответ ответственного лица",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Уникальный идентификатор заявки"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Лука Модрич",
 *         description="Имя пользователя"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         example="modrich@example.com",
 *         description="Email пользователя"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         example=1,
 *         description="Статус заявки: 1 - Active, 2 - Resolved"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Прошу связаться со мной по вопросу поддержки",
 *         description="Сообщение пользователя"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         example="Ответственный специалист связался с пользователем",
 *         description="Ответ ответственного лица"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-08-21 12:45:00",
 *         description="Ввремя создания заявки"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-08-22 09:30:00",
 *         description="Время ответа на заявку"
 *     )
 * )
 */
class Request extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_RESOLVED = 2;

    public static function tableName(): string
    {
        return 'requests';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required', 'message' => 'Заполните поле'],
            [['name', 'email'], 'string', 'max' => 100, 'tooLong' => 'Максимальная длина - 100 символов'],
            [['email'], 'email', 'message' => 'Введите корректный email'],
            [['comment'], 'required', 'message' => 'Заполните поле', 'when' => function ($model) {
                return $model->status === self::STATUS_RESOLVED;
            }],
            [['comment'], 'string'],
            [['status'], 'integer', 'message' => 'Некорректный статус'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_RESOLVED], 'message' => 'Некорректный статус'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя пользователя',
            'email' => 'Email пользователя',
            'status' => 'Статус',
            'message' => 'Сообщение пользователя',
            'comment' => 'Ответ ответственного лица',
            'created_at' => 'Время создания заявки',
            'updated_at' => 'Время ответа на заявку',
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активна',
            self::STATUS_RESOLVED => 'Завершена',
        ];
    }

    public function getStatusName(): string
    {
        return self::getStatusList()[$this->status] ?? 'Неизвестно';
    }
}
