<?php

namespace atsyscorp\mailqueue;

use Yii;
use atsyscorp\mailqueue\models\Queue;
use Symfony\Component\Mime\Email;

/**
 * Extends `yii\symfonymailer\Message` to enable queuing.
 *
 * @see https://www.yiiframework.com/extension/yiisoft/yii2-symfonymailer
 */
class Message extends \yii\symfonymailer\Message
{
    protected $htmlBody;
    protected $textBody;
    public $symfonyEmail;
    private Email $email;

    public function __construct($config = [])
    {
        $this->email = new Email();
        parent::__construct($config);
    }

    public function __sleep(): array
    {
        return ['email', 'charset'];
    }

    public function __serialize(): array
    {
        $this->symfonyEmail = $this->getSymfonyEmail();
        return [
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'cc' => $this->getCc(),
            'bcc' => $this->getBcc(),
            'subject' => $this->getSubject(),
            'textBody' => $this->symfonyEmail ? $this->symfonyEmail->getTextBody() : '',
            'htmlBody' => $this->symfonyEmail ? $this->symfonyEmail->getHtmlBody() : '',
            'charset' => $this->getCharset(),
        ];
    }

    public function __unserialize(array $data): void
    {
        // FORZAR REINICIALIZACIÓN DEL OBJETO
        $this->email = new \Symfony\Component\Mime\Email();

        Yii::debug('Email object initialized: ' . get_class($this->email), __METHOD__);

        if (!empty($data['subject'])) {
            Yii::debug('Setting subject: ' . $data['subject'], __METHOD__);
            $this->email->subject((string) $data['subject']);
        }

        if (!empty($data['textBody'])) {
            $this->email->text((string) $data['textBody']);
        }

        if (!empty($data['htmlBody'])) {
            $this->email->html((string) $data['htmlBody']);
        }

        if (isset($data['from'])) {
            Yii::debug('Setting from: ' . json_encode($data['from']), __METHOD__);
            try {
                $this->setFrom($data['from']);
            } catch (\Throwable $e) {
                Yii::error('Error setting "from": ' . $e->getMessage(), __METHOD__);
            }
        }
    }

    /**
     * Enqueue the message storing it in database.
     *
     * @param timestamp $time_to_send
     * @return boolean true on success, false otherwise
     * @throws \yii\base\InvalidConfigException Fields "from" or "to" if are required.
     */
    public function queue($time_to_send = 'now')
    {

        $this->symfonyEmail = $this->getSymfonyEmail();

        if ($time_to_send == 'now') {
            $time_to_send = time();
        }

        // Get all email addresses
        $from = $this->getFrom() ? key($this->getFrom()) : null;
        $to = $this->getTo() ? key($this->getTo()) : null;
        $cc = $this->getCc() ? key($this->getCc()) : null;
        $bcc = $this->getBcc() ? key($this->getBcc()) : null;
        $replyTo = $this->getReplyTo() ? key($this->getReplyTo()) : null;

        // Get subject and HTML & text body
        $subject = $this->getSubject();
        $textBody = $this->symfonyEmail->getTextBody();
        $htmlBody = $this->symfonyEmail->getHtmlBody();

        // Check required fields
        if (empty($from) || empty($to)) {
            throw new \yii\base\InvalidConfigException('Fields "from" and "to" are required.');
        }

        // Insert to queue table
        $item = new Queue();
        $item->from = $from;
        $item->to = $to;
        $item->cc = $cc;
        $item->bcc = $bcc;
        $item->reply_to = $replyTo;
        $item->subject = $subject;
        $item->text_body = $textBody;
        $item->html_body = $htmlBody;
        $item->charset = 'UTF-8';
        $item->created_at = date('Y-m-d H:i:s');
        $item->attempts = 0;
        $item->time_to_send = date('Y-m-d H:i:s', $time_to_send);
        $item->mailer_message = base64_encode(serialize($this));

        // Save record
        if (!$item->save()) {
            throw new \yii\base\InvalidConfigException(json_encode($item->getErrors()));
        } else {
            return true;
        }
    }
}