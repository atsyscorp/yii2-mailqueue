<?php

use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

/**
 * Handles adding mailer_message to table `mail_queue`.
 */
class m161111_080914_add_mailer_message_column_to_mail_queue_table extends Migration
{
    const TABLE = '{{%mail_queue}}';
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(self::TABLE, 'mailer_message', 'text');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn(self::TABLE, 'mailer_message');
    }
}
