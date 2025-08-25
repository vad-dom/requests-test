<?php

use yii\db\Migration;

class m250821_205836_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('requests', [
            'id' => $this->primaryKey()->comment('Уникальный идентификатор'),
            'name' => $this->string(100)->notNull()->comment('Имя пользователя'),
            'email' => $this->string(100)->notNull()->comment('Email пользователя'),
            'status' => $this
                ->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(1)
                ->comment('Статус (1 - Active, 2 - Resolved)'),
            'message' => $this->text()->notNull()->comment('Сообщение пользователя'),
            'comment' => $this->text()->null()->comment('Ответ ответственного лица'),
            'created_at' => $this
                ->timestamp()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Время создания заявки'),
            'updated_at' => $this
                ->timestamp()
                ->null()
                ->comment('Время ответа на заявку'),
        ]);

        $this->createIndex('idx_requests_status', 'requests', 'status');
        $this->createIndex('idx_requests_created_at', 'requests', 'created_at');
        $this->createIndex('idx_requests_updated_at', 'requests', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('requests');
    }
}
