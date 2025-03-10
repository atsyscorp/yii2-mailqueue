<?php

namespace atsyscorp\mailqueue\models;

use Yii;
use yii\db\ActiveRecord;
use Symfony\Component\Mime\Email;
use yii\symfonymailer\Message;

/**
 * This is the model class for table "{{%mail_queue}}".
 *
 * @property string $subject
 * @property integer $created_at
 * @property integer $attempts
 * @property integer $last_attempt_time
 * @property integer $sent_time
 * @property string $time_to_send
 * @property string $mailer_message
 */
class Queue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_queue}}';
    }

    /**
     * @inheritdoc
     */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['last_attempt_time'],
				],
				'value' => new \yii\db\Expression('NOW()'),
			],
		];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attempts', 'last_attempt_time', 'sent_time'], 'integer'],
            [['time_to_send', 'mailer_message'], 'required'],
            [['subject','created_at'], 'safe'],
        ];
    }

	public function toMessage()
    {
        $message = unserialize(base64_decode($this->mailer_message));

        // Si no es una instancia de Message, error
        if (!$message instanceof Message) {
            throw new \RuntimeException("El mensaje deserializado no es una instancia válida de Message");
        }

        // Asegurar que $email esté inicializado
        if (!isset($message->email) || !$message->email instanceof Email) {
            $message->email = new Email();
        }

        return $message;
    }

}
