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
class Message extends \yii\symfonymailer\Message implements \Serializable
{
    protected $htmlBody;
    protected $textBody;

    public $email;

    public function __construct()
    {
        parent::__construct();
        $this->email = $this->getSymfonyEmail();
    }

    public function serialize()
    {

        //print_r($this); die;
        $_textBody = '';
        $_htmlBody = '';
        if($this->email) {
            $_textBody = $this->email->getTextBody();
            $_htmlBody = $this->email->getHtmlBody();
        }

        return serialize([
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'cc' => $this->getCc(),
            'bcc' => $this->getBcc(),
            'subject' => $this->getSubject(),
            'textBody' => $_textBody,
            'htmlBody' => $_htmlBody,
            'charset' => $this->getCharset(),
        ]);
    }

    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->setFrom($data['from']);
        $this->setTo($data['to']);
        $this->setCc($data['cc']);
        $this->setBcc($data['bcc']);
        $this->setSubject($data['subject']);
        $this->setTextBody($data['textBody']);
        $this->setHtmlBody($data['htmlBody']);
        $this->setCharset($data['charset']);
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
        $textBody = $this->email->getTextBody();
        $htmlBody = $this->email->getHtmlBody();

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