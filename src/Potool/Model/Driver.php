<?php

namespace Potool\Model;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 1:29 PM
 * To change this template use File | Settings | File Templates.
 */
class Driver
{

    protected $options;
    protected $module;

    function __construct($options)
    {
        $this->options = $options;
    }

    function getOption($name){
        return isset($this->options[$name])?$this->options[$name]:null;
    }

    function getModules()
    {

    }

    function setModule($module)
    {
        if (in_array($module, $this->getModules())){
            throw new \Exception("Can't find module");
        }
        $this->module = $module;
        return $this;
    }

    function getLauguages()
    {
        if (!$this->module){
            throw new \Exception("Module is undefined");
        }
    }

    function search(){

    }
}
