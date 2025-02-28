<?php

use yii\db\Migration;

class m20250227_195632_add_columns extends Migration
{
    const TABLE = '{{%mail_queue}}';
    const INDEX_TIME_TO_SEND = 'IX_time_to_send';
    const INDEX_SENT_TIME = 'IX_sent_time';

    public function up()
    {
        $this->addColumn(self::TABLE, 'time_to_send', $this->dateTime()->notNull());
        $this->addColumn(self::TABLE, 'mailer_message', $this->text());
        $this->createIndex(self::INDEX_TIME_TO_SEND, self::TABLE, 'time_to_send');
        $this->createIndex(self::INDEX_SENT_TIME, self::TABLE, 'sent_time');
    }

    public function down()
    {
        // Eliminar índices
        $this->dropIndex(self::INDEX_TIME_TO_SEND, self::TABLE);
        $this->dropIndex(self::INDEX_SENT_TIME, self::TABLE);

        // Eliminar columnas
        $this->dropColumn(self::TABLE, 'time_to_send');
        $this->dropColumn(self::TABLE, 'mailer_message');
    }
}