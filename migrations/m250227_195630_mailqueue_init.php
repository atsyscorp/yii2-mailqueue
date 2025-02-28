<?php

use yii\db\Migration;

/**
 * Initializes the db table for MailQueue
 */
class m250227_195630_mailqueue_init extends Migration
{
    const TABLE = '{{%mail_queue}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'from' => $this->text(),
            'to' => $this->text(),
            'cc' => $this->text(),
            'bcc' => $this->text(),
            'subject' => $this->string(),
            'html_body' => $this->text(),
            'text_body' => $this->text(),
            'reply_to' => $this->text(),
            'charset' => $this->string(),
            'created_at' => $this->dateTime()->notNull(),
            'attempts' => $this->integer(),
            'last_attempt_time' => $this->dateTime()->defaultValue(null),
            'sent_time' => $this->dateTime()->defaultValue(null),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable(self::TABLE);
    }
}