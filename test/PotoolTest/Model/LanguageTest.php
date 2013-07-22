<?php

namespace PotoolTest\Model;
use Potool\Model\ModuleManager;
use Potool\Model\Module;
use Potool\Model\Language;
use PotoolTest\Bootstrap;


use PHPUnit_Framework_TestCase;
/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 3/13/13
 * Time: 6:29 PM
 * To change this template use File | Settings | File Templates.
 */
class LanguageTest extends PHPUnit_Framework_TestCase
{
    /* @var ModuleManager $moduleManager */
    protected $moduleManager;

    function setUp()
    {
        $config = Bootstrap::getServiceManager()->get('config');
        $this->moduleManager = new ModuleManager($config['potool']);
    }

    protected function getClearedModule2()
    {
        $module = $this->moduleManager->getModuleByName('Module2');
        $module->setPath(__DIR__ . '/../../data/Module2/');
        if($module->isLanguageExists('ru_RU')){
            $language = $module->getLanguageByName('ru_RU');
            $language->delete();
        }
//        exit;
        return $module;
    }

    function testAddCompileDeleteLanguage()
    {
        $module = $this->getClearedModule2();
        $language = $module->createLanguage('ru_RU');
        $this->assertSame('ru_RU', (string)$language);
        $this->assertTrue($language instanceof Language);
        $po = $language->getPoFile();
        $mo = $language->getMoFile();
        $this->assertFileExists($po);
        $this->assertFileNotExists($mo);
        $pot = $module->createPot();
        $language->merge($pot);
        $this->assertFileNotExists($mo);
        $this->assertFalse($language->hasMo());
        $content = file_get_contents($po);
        $this->assertSame($content, $language->getPoContent());

        $content = str_replace('msgstr ""', 'msgstr "test"', $content);
        $language->setPoContent($content);
        $this->assertSame($content, $language->getPoContent());

        $language->compile();
        $this->assertFileExists($mo);
        $this->assertTrue($language->hasMo());
        $language->delete();
        $this->assertFalse($module->isLanguageExists('ru_RU'));
        $this->assertFileNotExists($po);
        $this->assertFileNotExists($mo);
    }

    function testCheckDate()
    {
        $module = $this->moduleManager->getModuleByName('Module1');
        $module->setPath(__DIR__ . '/../../data/Module1');
        $ru = $module->getLanguageByName('ru_RU');
        $ru->compile();
        $this->assertTrue($ru->getPoDate() instanceof \DateTime);
        $this->assertTrue($ru->getMoDate() instanceof \DateTime);
    }

    function testUntranslatedCount()
    {
        $module = $this->getClearedModule2();
        $ru = $module->createLanguage('ru_RU');
        $module->upgrade();
        $this->assertSame(3, $ru->countUntranslated());
        $content = $ru->getPoContent();
        $parts = explode('msgstr ""', $content);
        $last = array_pop($parts);
        $cnt = count($parts);
        $parts[$cnt-1] .= 'msgstr "test"' . $last;
        $content = join('msgstr ""', $parts);
        $ru->setPoContent($content);
        $this->assertSame(2, $ru->countUntranslated());
    }





}
