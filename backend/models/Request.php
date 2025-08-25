<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
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
            [['status'], 'integer'],
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
