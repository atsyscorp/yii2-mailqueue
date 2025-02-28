<?php

use yii\db\Migration;

class m20250227_195635_alter_columns extends Migration
{
    const TABLE = '{{%mail_queue}}';

    public function safeUp()
    {
        $table = self::TABLE;
        $this->alterColumn($table, 'mailer_message', 'LONGTEXT');
    }

    public function safeDown()
    {
        $table = self::TABLE;
        $this->alterColumn($table, 'mailer_message', $this->text());
    }
}