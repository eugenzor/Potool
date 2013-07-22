<?php
namespace Potool\Controller;

use Potool\Model\ModuleManager;

/**
 * Show modules
 * User: eugene
 * Date: 3/21/13
 * Time: 5:45 PM
 * To change this template use File | Settings | File Templates.
 */
class ModuleController extends AbstractController
{

    function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $moduleManager = new ModuleManager($config['potool']);
        $modules = $moduleManager->getModules();
        return array('modules'=>$modules);
    }

    function permissionsAction()
    {
        return array('module'=>$this->getModuleByParams());
    }

    function upgradeAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }
        $module->upgrade();
        $this->flashMessenger()->addMessage($this->translate('Languages have been upgraded'));
        $this->redirect()->toRoute('potool', array('controller'=>'language', 'id'=>(string)$module));


        return false;
    }

    function compileAction()
    {
        $module = $this->getModuleByParams();
        if (!$module->isWritable()){
            throw new \Exception(sprintf($this->translate('Language folder of module "%s" is not writable'), $module));
        }
        $module->compile();
        $this->flashMessenger()->addMessage($this->translate('Languages have been compiled'));
        $this->redirect()->toRoute('potool', array('controller'=>'language', 'id'=>(string)$module));


        return false;
    }


}
