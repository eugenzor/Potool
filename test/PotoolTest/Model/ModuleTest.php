<?php

namespace PotoolTest\Model;
use Potool\Model\ModuleManager;
use Potool\Model\Module;
use Potool\Model\Language;
use Potool\Model\Pot;
use PotoolTest\Bootstrap;


use PHPUnit_Framework_TestCase;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 3/13/13
 * Time: 6:16 PM
 * To change this template use File | Settings | File Templates.
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    /* @var ModuleManager $moduleManager */
    protected $moduleManager;
    protected $testModulesRoot;

    function setUp()
    {
        $config = Bootstrap::getServiceManager()->get('config');
        $this->moduleManager = new ModuleManager($config['potool']);
        $this->testModulesRoot = __DIR__ . '/../../data';
    }

    function testIsLanguageExists()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $module->setPath($this->testModulesRoot . '/Module1');
        $this->assertTrue($module->isLanguageExists('ru_RU'));
        $this->assertFalse($module->isLanguageExists('Unexist_language'));
    }

    function testGetLanguage()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $module->setPath($this->testModulesRoot . '/Module1');
        $lang = $module->getLanguageByName('ru_RU');
        $this->assertTrue($lang instanceof Language);
    }

    function testCreatePot()
    {
        $module = $this->moduleManager->getModuleByName('Module2');
        $module->setPath($this->testModulesRoot . '/Module2');
        $pot = $module->createPot();
        $this->assertTrue($pot instanceof Pot);
        $file = $pot->getFile();
        $this->assertFileExists($file);
    }

    function testCompile()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $module->setPath($this->testModulesRoot . '/Module1');
        $languages = $module->getLanguages();
        foreach($languages as $language){
            $mo = $language->getMoFile();
            if (is_file($mo)){
                unlink($mo);
            }
        }
        $module->compile();
        foreach($languages as $language)
        {
            $mo = $language->getMoFile();
            $this->assertFileExists($mo);
        }
    }

    function testUpdate()
    {
        $module = $this->moduleManager->getModuleByName('Module2');
        $module->setPath($this->testModulesRoot . '/Module2');
        $languages = $module->getLanguages();
        foreach($languages as $language){
            $language->delete();
        }
        $this->assertSame(0, count($module->getLanguages()));
        $ru = $module->createLanguage('ru_RU');
        $ua = $module->createLanguage('ua_UA');

        $this->assertSame(0, filesize($ru->getPoFile()));
        $this->assertSame(0, filesize($ua->getPoFile()));

        $module->upgrade();
        $this->assertGreaterThan(0, filesize($ru->getPoFile()));
        $this->assertGreaterThan(0, filesize($ua->getPoFile()));

    }

    function testWritable()
    {
        $module = $this->moduleManager->getModuleByName('Module2');
        $module->isLanguageDirWritable();
    }

    function testAdditionalFile()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $module->setPath($this->testModulesRoot . '/Module1');
        $phrases = $module->readAdditionalFile();
        $this->assertSame(array('January', 'February"s', "March's"), $phrases);

        $module = $this->moduleManager->getModuleByName('Module3');
        $module->setPath($this->testModulesRoot . '/Module3');
        $module->writeAdditionalFile(array());
        $this->assertSame(array(), $module->readAdditionalFile());
        $module->writeAdditionalFile(array('January', 'February"s', "March's", "April
May"));
        $this->assertSame(array('January', 'February"s', "March's", "April
May"), $module->readAdditionalFile());
    }

}
