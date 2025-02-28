<?php

use yii\db\Migration;
use atsyscorp\mailqueue\MailQueue;

/**
 * Handles adding mailer_message to table `mail_queue`.
 */
class m170217_124201_drop_obsolete_columns_from_mail_queue_table extends Migration
{

    const TABLE = '{{%mail_queue}}';
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn(self::TABLE, 'from');
		$this->dropColumn(self::TABLE, 'to');
		$this->dropColumn(self::TABLE, 'cc');
		$this->dropColumn(self::TABLE, 'bcc');
		$this->dropColumn(self::TABLE, 'html_body');
		$this->dropColumn(self::TABLE, 'text_body');
		$this->dropColumn(self::TABLE, 'reply_to');
		$this->dropColumn(self::TABLE, 'charset');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn(self::TABLE, 'from', 'text');
		$this->addColumn(self::TABLE, 'to', 'text');
		$this->addColumn(self::TABLE, 'cc', 'text');
		$this->addColumn(self::TABLE, 'bcc', 'text');
		$this->addColumn(self::TABLE, 'html_body', 'text');
		$this->addColumn(self::TABLE, 'text_body', 'text');
		$this->addColumn(self::TABLE, 'reply_to', 'text');
		$this->addColumn(self::TABLE, 'charset', 'string');
    }
}
