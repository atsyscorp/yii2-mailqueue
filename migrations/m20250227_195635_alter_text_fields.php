<?php

use yii\db\Schema;
use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

class m20250227_195635_alter_text_fields extends Migration {

    const TABLE = '{{%mail_queue}}';
    
    public function safeUp() {
        $table = self::TABLE;
        $this->alterColumn($table, 'mailer_message', 'LONGTEXT');
    }

    public function safeDown() {
        $table = self::TABLE;
        $this->alterColumn($table, 'mailer_message', Schema::TYPE_TEXT);
    }

}
