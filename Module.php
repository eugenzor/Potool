<?php

namespace Potool;


/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/29/13
 * Time: 6:10 PM
 * To change this template use File | Settings | File Templates.
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'flashMessage' => function($sm) {

                    $flashmessenger = $sm->getServiceLocator()
                        ->get('ControllerPluginManager')
                        ->get('flashmessenger');

                    $message    = new View\Helper\FlashMessages( ) ;
                    $message->setFlashMessenger( $flashmessenger );

                    return $message ;
                }
            ),
        );
    }
}
