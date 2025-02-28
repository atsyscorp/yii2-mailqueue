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
     */
    public function queue($time_to_send = 'now')
    {
        if ($time_to_send == 'now') {
            $time_to_send = time();
        }

        // Obtener los datos del correo
        $from = $this->getFrom()[0]->getAddress(); // Remitente
        $to = $this->getTo()[0]->getAddress(); // Destinatario
        $cc = $this->getCc() ? $this->getCc()[0]->getAddress() : null; // Copia carbón (si existe)
        $bcc = $this->getBcc() ? $this->getBcc()[0]->getAddress() : null; // Copia carbón oculta (si existe)
        $replyTo = $this->getReplyTo() ? $this->getReplyTo()[0]->getAddress() : null; // Dirección de respuesta (si existe)
        $subject = $this->getSubject(); // Asunto
        $textBody = $this->getTextBody(); // Cuerpo en texto plano
        $htmlBody = $this->getHtmlBody(); // Cuerpo en HTML

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
        $item->charset = 'UTF-8'; // Juego de caracteres
        $item->created_at = date('Y-m-d H:i:s'); // Fecha de creación
        $item->attempts = 0; // Número de intentos
        $item->time_to_send = date('Y-m-d H:i:s', $time_to_send); // Momento de envío
        $item->mailer_message = base64_encode(serialize($this)); // Serializar el objeto Message

        // Guardar el registro
        return $item->save();
    }
}