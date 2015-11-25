<?php

namespace Potool\Model;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */
class ModuleManager
{
    protected $options;
    function __construct($options)
    {
        $this->options = $options;
    }

    function getOption($name)
    {
        return isset($this->options[$name])?$this->options[$name]:null;
    }

    function getOptions()
    {
        return $this->options;
    }

    function getNames()
    {
        $dir = $this->getOption('dir');
        if (!$dir){
            $dir = realpath('module');
        }
        if (!is_dir($dir)){
            throw new \Exception("Module folder is undefined");
        }
        return Utils::getFileList($dir, array('type'=>'d'));
    }

    function getModules()
    {
        $names = $this->getNames();
        $modules = array();
        foreach($names as $name){

            $modules[]= new Module($this, $name);
        }
        return $modules;
    }

    function getModuleByName($name)
    {
        $names = $this->getNames();
        if (!in_array($name, $names)){
            throw new \Exception("Can't find module");
        }
        $module = new Module($this, $name);
        return $module;
    }
}
