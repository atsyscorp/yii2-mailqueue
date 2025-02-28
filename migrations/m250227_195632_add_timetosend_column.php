<?php

use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

class m250227_195632_add_timetosend_column extends Migration
{
    const TABLE = '{{%mail_queue}}';

    public function up()
    {
        $this->addColumn(self::TABLE, 'time_to_send', $this->dateTime()->notNull());
        $this->addColumn(self::TABLE, 'mailer_message', 'text');
        $this->createIndex('IX_time_to_send', self::TABLE, 'time_to_send');
        $this->createIndex('IX_sent_time', self::TABLE, 'sent_time');
    }

    public function down()
    {
        $this->dropIndex('IX_time_to_send', self::TABLE);
        $this->dropIndex('IX_sent_time', self::TABLE);
        $this->dropColumn(self::TABLE, 'time_to_send');
        $this->dropColumn(self::TABLE, 'mailer_message');
    }
}
