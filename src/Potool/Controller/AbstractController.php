<?php
namespace Potool\Controller;
use Potool\Model\ModuleManager;

abstract class AbstractController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * Get module by id from params
     * @return \Potool\Model\Module
     */
    protected function getModuleByParams()
    {
        $config = $this->getServiceLocator()->get('config');
        $moduleManager = new ModuleManager($config['potool']);
        $module = $moduleManager->getModuleByName($this->params()->fromRoute('id'));
        return $module;
    }

    protected function translate($message, $textDomain = 'default', $locale = null)
    {
        return $this->getServiceLocator()->get('viewhelpermanager')->get('translate')->getTranslator()->translate($message, $textDomain, $locale);
    }
}
