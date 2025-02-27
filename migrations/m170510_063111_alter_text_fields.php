<?php

use yii\db\Schema;
use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

class m170510_063111_alter_text_fields extends Migration {

    public function safeUp() {
        $table = Yii::$app->get(MailQueue::NAME)->table;
        $this->alterColumn($table, 'mailer_message', 'LONGTEXT');
    }

    public function safeDown() {
        $table = Yii::$app->get(MailQueue::NAME)->table;
        $this->alterColumn($table, 'mailer_message', Schema::TYPE_TEXT);
    }

}
