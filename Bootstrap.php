<?php
/**
 * Mail Command Controller
 * 
 * @author ATSYS <arkitechsystems@gmail.com>
 */

namespace atsyscorp\mailqueue;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 * 
 * @package atsyscorp\mailqueue;
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
            $app->controllerMap['mailqueue'] = 'atsyscorp\mailqueue\commands\MailQueueController';
        }
        
    }
}