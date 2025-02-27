<?php

/**
 * Mail Command Controller
 */

namespace atsyscorp\mailqueue\commands;

use yii\console\Controller;

/**
 * This command processes the mail queue
 */
class MailQueueController extends Controller
{
    
    public $defaultAction = 'process';
      
    /**
     * This command processes the mail queue     
     */
    public function actionProcess()
    {
        \Yii::$app->mailqueue->process();
    }
}
