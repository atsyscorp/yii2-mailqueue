<?php

namespace atsyscorp\mailqueue;

use Yii;
use atsyscorp\mailqueue\models\Queue;

/**
 * Extends `yii\symfonymailer\Message` to enable queuing.
 *
 * @see https://www.yiiframework.com/extension/yiisoft/yii2-symfonymailer
 */
class Message extends \yii\symfonymailer\Message implements \Serializable
{

    public function serialize()
    {
        // Serializa manualmente las propiedades que necesitas
        return serialize([
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'cc' => $this->getCc(),
            'bcc' => $this->getBcc(),
            'subject' => $this->getSubject(),
            'textBody' => method_exists($this, 'getTextBody') ? $this->getTextBody() : null,
            'htmlBody' => method_exists($this, 'getHtmlBody') ? $this->getHtmlBody() : null,
        ]);
    }

    public function unserialize($data)
    {
        // Deserializa manualmente las propiedades
        $data = unserialize($data);
        $this->setFrom($data['from']);
        $this->setTo($data['to']);
        $this->setCc($data['cc']);
        $this->setBcc($data['bcc']);
        $this->setSubject($data['subject']);
        $this->setTextBody($data['textBody']);
        $this->setHtmlBody($data['htmlBody']);
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

        // Obtener las direcciones de correo
        $from = $this->getFrom() ? key($this->getFrom()) : null;
        $to = $this->getTo() ? key($this->getTo()) : null;
        $cc = $this->getCc() ? key($this->getCc()) : null;
        $bcc = $this->getBcc() ? key($this->getBcc()) : null;
        $replyTo = $this->getReplyTo() ? key($this->getReplyTo()) : null;

        // Obtener el asunto y los cuerpos del correo
        $subject = $this->getSubject();
        $textBody = method_exists($this, 'getTextBody') ? $this->getTextBody() : null;
        $htmlBody = method_exists($this, 'getHtmlBody') ? $this->getHtmlBody() : null;

        // Validar campos obligatorios
        if (empty($from) || empty($to)) {
            throw new \yii\base\InvalidConfigException('Fields "from" and "to" are required.');
        }

        // Crear un nuevo registro en la cola de correos
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
        $item->attempts = 0;
        $item->time_to_send = date('Y-m-d H:i:s', $time_to_send);
        $item->mailer_message = base64_encode(serialize($this));

        // Guardar el registro
        if (!$item->save()) {
            throw new \yii\base\InvalidConfigException(json_encode($item->getErrors()));
        } else {
            return true;
        }
    }
}