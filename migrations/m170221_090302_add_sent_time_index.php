<?php
use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;
class m170221_090302_add_sent_time_index extends Migration
{

    const TABLE = '{{%mail_queue}}';

    public function up()
    {
        $this->createIndex('IX_sent_time', self::TABLE, 'sent_time');
    }
    public function down()
    {
        $this->dropIndex('IX_sent_time', self::TABLE);
    }
}
