<?php
/**
 * Mail Command Controller
 * 
 * @author ATSYS <arkitechsystems@gmail.com>
 */

namespace atsys\mailqueue;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * 
 * @package atsys\mailqueue;
 * @author ATSYS <arkitechsystems@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * Will add the mailqueue  command to the controller map 
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
      
        if ($app instanceof \yii\console\Application) {
            $app->controllerMap['mailqueue'] = 'atsys\mailqueue\commands\MailQueueController';
        }
        
    }
}