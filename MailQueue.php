<?php

/**
 * MailQueue.php
 * @author Saranga Abeykoon http://atsyscorp.com
 */

namespace atsyscorp\mailqueue;

use Yii;
use atsyscorp\mailqueue\models\Queue;

class MailQueue extends \yii\symfonymailer\Mailer
{

	/**
	 * @var string message default class name.
	 */
	public $messageClass = 'atsyscorp\mailqueue\Message';

	/**
	 * @var string the name of the database table to store the mail queue.
	 */
	public $table = '{{%mail_queue}}';

	/**
	 * @var integer the default value for the number of mails to be sent out per processing round.
	 */
	public $mailsPerRound = 10;

	/**
	 * @var integer maximum number of attempts to try sending an email out.
	 */
	public $maxAttempts = 3;
	
	
	/**
	 * @var boolean Purges messages from queue after sending, if you consider keep the queue just setting in false
	 */
	public $autoPurge = false;

	/**
	 * Initializes the MailQueue component.
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * Sends out the messages in email queue and update the database.
	 *
	 * @return boolean true if all messages are successfully sent out
	 */
	public function process()
	{
		if (Yii::$app->db->getTableSchema($this->table) == null) {
			throw new \yii\base\InvalidConfigException('"' . $this->table . '" not found in database. Make sure the db migration is properly done and the table is created.');
		}
		
		$success = true;

		$items = Queue::find()->where(['and', ['sent_time' => NULL], ['<', 'attempts', $this->maxAttempts], ['<=', 'time_to_send', date('Y-m-d H:i:s')]])->orderBy(['created_at' => SORT_ASC])->limit($this->mailsPerRound);

		foreach ($items->each() as $item) {
		    if ($message = $item->toMessage()) {
				$attributes = ['attempts', 'last_attempt_time'];
				if ($this->send($message)) {
					$item->sent_time = new \yii\db\Expression('NOW()');
					$attributes[] = 'sent_time';
				} else {
					$success = false;
				}

				$item->attempts++;
				$item->last_attempt_time = new \yii\db\Expression('NOW()');

				$item->updateAttributes($attributes);
		    }
		}
	
		// Purge messages now?
		if ($this->autoPurge) {
			$this->purge();
		}

		return $success;
	}
	
	
	/**
	 * Deletes sent messages from queue.
	 *
	 * @return int Number of rows deleted
	 */
	
	public function purge()
	{
		return Queue::deleteAll('sent_time IS NOT NULL');
	}
}
