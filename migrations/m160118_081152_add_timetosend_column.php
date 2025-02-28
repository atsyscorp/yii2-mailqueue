<?php

use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

class m160118_081152_add_timetosend_column extends Migration
{
    const TABLE = '{{%mail_queue}}';

    public function up()
    {
        $this->addColumn(self::TABLE, 'time_to_send', $this->dateTime()->notNull());
        $this->createIndex('IX_time_to_send', self::TABLE, 'time_to_send');
    }

    public function down()
    {
        $this->dropIndex('IX_time_to_send', self::TABLE);
        $this->dropColumn(self::TABLE, 'time_to_send');
    }
}
