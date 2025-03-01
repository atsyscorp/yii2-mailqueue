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

        $from = $this->getFrom() ? array_keys($this->getFrom())[0] : null;
        $to = $this->getTo() ? array_keys($this->getTo())[0] : null;
        $cc = $this->getCc() ? array_keys($this->getCc())[0] : null;
        $bcc = $this->getBcc() ? array_keys($this->getBcc())[0] : null;
        $replyTo = $this->getReplyTo() ? array_keys($this->getReplyTo())[0] : null;
        $subject = $this->getSubject();
        $textBody = method_exists($this, 'getTextBody') ? $this->getTextBody() : null;
        $htmlBody = method_exists($this, 'getHtmlBody') ? $this->getHtmlBody() : null;

        if (empty($from) || empty($to)) {
            throw new \yii\base\InvalidConfigException('Fields "from" and "to" are required.');
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
        $item->created_at = time();
        $item->attempts = 0;
        $item->time_to_send = date('Y-m-d H:i:s', $time_to_send);
        $item->mailer_message = base64_encode(serialize($this));

        if(!$item->save()) {
            throw new \yii\base\InvalidConfigException( json_encode($item->getErrors()) );
        } else {
            return true;
        }
    }
}