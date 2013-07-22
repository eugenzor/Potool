<?php

namespace PotoolTest\Model;
use Potool\Model\Utils;


use PHPUnit_Framework_TestCase;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 3:40 PM
 * To change this template use File | Settings | File Templates.
 */
class UtilsTest extends PHPUnit_Framework_TestCase
{


    function testFileList()
    {
        $dir = realpath(__DIR__ . '/../../data');
        $this->assertSame(array('Module1', 'Module2', 'Module3'), Utils::getFileList($dir, array('type'=>'d')));
        $this->assertSame(array(), Utils::getFileList($dir, array('type'=>'f')));
    }
}
