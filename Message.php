<?php

/**
 * Message.php
 * @author ATSYS https://atsys.co
 */

namespace atsys\mailqueue;

use Yii;
use atsys\mailqueue\models\Queue;

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
        if($time_to_send == 'now') {
            $time_to_send = time();
        }

        $item = new Queue();
        $item->subject          = $this->getSubject();
        $item->attempts         = 0;
        $item->mailer_message   = base64_encode(serialize($this));
        $item->time_to_send     = date('Y-m-d H:i:s', $time_to_send);

        return $item->save();
    }
}
