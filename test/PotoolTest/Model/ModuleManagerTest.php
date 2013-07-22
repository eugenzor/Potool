<?php

namespace PotoolTest\Model;
use Potool\Model\ModuleManager;
use Potool\Model\Module;
use PotoolTest\Bootstrap;


use PHPUnit_Framework_TestCase;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 3:40 PM
 * To change this template use File | Settings | File Templates.
 */
class ModuleManagerTest extends PHPUnit_Framework_TestCase
{
    /* @var ModuleManager $moduleManager */
    protected $moduleManager;


    function setUp()
    {
        $config = Bootstrap::getServiceManager()->get('config');
        $this->moduleManager = new ModuleManager($config['potool']);
    }

    function testGetModuleNames()
    {
        $this->assertSame(array('Module1', 'Module2', 'Module3'), $this->moduleManager->getNames());
    }

    function testGetModules()
    {
        $modules = $this->moduleManager->getModules();
        foreach($modules as $module){

            $this->assertTrue($module instanceof Module);
            $this->assertContains('Module', (string)$module);
        }

    }

    function testGetModuleByName()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $this->assertTrue($module instanceof Module);
        $this->assertSame('Module1', (string)$module);
    }

    /**
     * @expectedException \Exception
     */
    function testGetNonExistModule()
    {
        $this->moduleManager->getModuleByName('NonExistModule');
    }
}
