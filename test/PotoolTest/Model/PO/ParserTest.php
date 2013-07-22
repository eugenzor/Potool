<?php

namespace PotoolTest\Model\PO;

use Potool\Model\PO\Parser;
use Potool\Model\PO\ArrayStore;
use PHPUnit_Framework_TestCase;
use PotoolTest\Bootstrap;


class ParserTest extends PHPUnit_Framework_TestCase
{

    function testReadWriteFile()
    {
        $config = Bootstrap::getServiceManager()->get('config');
        $tmpDir = isset($config['potool']['tmp_dir'])?$config['potool']['tmp_dir']:'/tmp';
        $file = tempnam($tmpDir, 'po_');

        $store = new ArrayStore;
        $parser = new Parser($store);
        $parser->readFile(__DIR__ . '/../../../data/Module1/language/ru_RU.po');
        $this->assertGreaterThan(0, count($store->read()));
//        $content1 = file_get_contents(__DIR__ . '/../../../data/Module1/language/ru_RU.po');
        $parser->writeFile($file);
        file_put_contents("/tmp/store.php", var_export($store->read(), true));
        $this->assertFileExists($file);
        $this->assertGreaterThan(0, filesize($file));
//        $content2 = file_get_contents($file);

        unlink($file);
//        $this->assertSame($content1, $content2);
    }

}
