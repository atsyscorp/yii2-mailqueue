<?php

use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

/**
 * Handles adding mailer_message to table `mail_queue`.
 */
class m161111_080914_add_mailer_message_column_to_mail_queue_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(Yii::$app->get(MailQueue::NAME)->table, 'mailer_message', 'text');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn(Yii::$app->get(MailQueue::NAME)->table, 'mailer_message');
    }
}
