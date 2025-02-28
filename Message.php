<?php

namespace atsyscorp\mailqueue;

use Yii;
use atsyscorp\mailqueue\models\Queue;

/**
 * Extends `yii\symfonymailer\Message` to enable queuing.
 *
 * @see https://www.yiiframework.com/extension/yiisoft/yii2-symfonymailer
 */
class Message extends \yii\symfonymailer\Message
{
    /**
     * Enqueue the message storing it in database.
     *
     * @param timestamp $time_to_send
     * @return boolean true on success, false otherwise
     * @throws \yii\base\InvalidConfigException Fields "from" or "to" if are undefined.
     */
    public function queue($time_to_send = 'now')
    {
        if ($time_to_send == 'now') {
            $time_to_send = time();
        }

        $from = $this->getFrom() ? $this->getFrom()[0]->getAddress() : null;
        $to = $this->getTo() ? $this->getTo()[0]->getAddress() : null;
        $cc = $this->getCc() ? $this->getCc()[0]->getAddress() : null;
        $bcc = $this->getBcc() ? $this->getBcc()[0]->getAddress() : null;
        $replyTo = $this->getReplyTo() ? $this->getReplyTo()[0]->getAddress() : null;
        $subject = $this->getSubject();
        $textBody = $this->getTextBody();
        $htmlBody = $this->getHtmlBody();

        if (empty($from) || empty($to)) {
            throw new \yii\base\InvalidConfigException('Los campos "from" y "to" son obligatorios.');
        }

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

        return $item->save();
    }
}