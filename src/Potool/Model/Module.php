<?php

namespace Potool\Model;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */
class Module
{

    protected $name;
    protected $defaultLanguage='en_US';
    protected $moduleManager;
    protected $path;

    protected $additionalFileStartDelimiter = '<?php echo $this->translate("';
    protected $additionalFileEndDelimiter = "\")?>\n";


    function __construct(ModuleManager $moduleManager, $name)
    {
        $this->moduleManager = $moduleManager;
        $this->name = $name;
    }


    /**
     * @return Pot pot
     */
    function createPot()
    {
        return new Pot($this->getPath(), $this->getManager()->getOptions());
    }

    /**
     * Get all language names in module
     * @return array
     */
    function getLanguageNames()
    {
        $files = Utils::getFileList($this->getPath() . '/language', array('type'=>'f', 'ext'=>'po'));
        if (!$files){
            return array();
        }
        $languageNames = array();
        foreach($files as $file){
            $parts = explode(".", $file);
            array_pop($parts);
            $languageName = join(".", $parts);
            if ($languageName){
                $languageNames[]=$languageName;
            }
        }
        return $languageNames;
    }

    function getName()
    {
        return $this->name;
    }

    /**
     * Get all module languages
     * @return array $languages
     */
    function getLanguages()
    {
        $languages = array();
        $languageNames = $this->getLanguageNames();
        foreach($languageNames as $name){
            $language = new Language($this, $name);
            $languages[]=$language;
        }
        return $languages;
    }

    /**
     * Get module path (for testing)
     * @param $path
     * @return Module
     */
    function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    function getPath()
    {
        if (!$this->path){
            $this->path = realpath(__DIR__. '/../../../../' . $this->getName());
        }
        return $this->path;
    }

    function __toString(){
        return $this->name;
    }

    function getLanguageByName($name)
    {
        if (!$this->isLanguageExists($name)){
            throw new \Exception("Module $this hasn't language $name");
        }
        $language = new Language($this, $name);
        return $language;

    }

    function isLanguageExists($name)
    {
        $names = $this->getLanguageNames();
        return in_array($name, $names);
    }

    function createLanguage($name)
    {
        return Language::factory($this, $name);
    }

    /**
     * @return ModuleManager
     */
    function getManager()
    {
        return $this->moduleManager;
    }

    /**
     * Compile all po files of module to mo
     * @return Module
     */
    function compile()
    {
        $languages = $this->getLanguages();
        foreach($languages as $language)
        {
            $language->compile();
        }
        return $this;
    }


    /**
     * Looking for new translations in module and update po files
     * @return Module
     */
    function upgrade()
    {
        $pot = $this->createPot();
        $languages = $this->getLanguages();

        if (!count($languages)){
            $languages = array(new Language($this, $this->defaultLanguage));
        }

        foreach($languages as $language){
            $language->merge($pot);
        }
        return $this;
    }

    /**
     * Check if language dir is writable
     * @return bool
     */
    function isWritable()
    {
        $langDir = $this->getLanguageDir();
        if (!is_dir($langDir)){
            return false;
        }
        return is_writable($langDir);
    }

    function getLanguageDir()
    {
        return  $this->getPath() . '/language';
    }

    protected function getAdditionalFileName()
    {
        return $this->getLanguageDir() . '/potool.phtml';
    }

    /**
     * Get phrases from additional file
     * @return array
     */
    function readAdditionalFile()
    {
        $file = $this->getAdditionalFileName();
        if (!is_file($file)){
            return array();
        }
        $content = file_get_contents($file);
        $expression = '/'
            . preg_quote($this->additionalFileStartDelimiter) . '(.+?)'
            . preg_quote($this->additionalFileEndDelimiter) . '/si';
        $found = preg_match_all($expression, $content, $matches);
        if (!$found){
            return array();
        }
        $phrases = array();
        foreach($matches[1] as $match){
            $phrases []= str_replace('\"', '"', $match);
        }
        return $phrases;
    }



    /**
     * Save phrases to additional file
     * @param array $phrases
     * @return Module
     * @throws \Exception
     */
    function writeAdditionalFile($phrases)
    {
        if (!count($phrases)){
            return $this;
        }
        $file = $this->getAdditionalFileName();
        $content = '';
        foreach($phrases as $phrase){
            $phrase = str_replace('"', '\"', $phrase);
            $content .= $this->additionalFileStartDelimiter . $phrase . $this->additionalFileEndDelimiter;
        }
        if (is_file($file)){
            @unlink($file);
        }
        $written = @file_put_contents($file, $content);
        if (!$written){
            throw new \Exception("Can not write to file " . $file);
        }
        return $this;
    }

    /**
     * Add message id to additional file
     * @param string $key
     * @return Module
     * @throws \Exception
     */
    function addMessageKey($key)
    {
        $phrases = $this->readAdditionalFile();
        if (in_array($key, $phrases)){
            throw new \Exception("Message key $key already exists");
        }
        $languages = $this->getLanguages();
        foreach($languages as $language){
            $entries = $language->getEntries();
            foreach($entries as $entry){
                if($key == $entry->msgid){
                    throw new \Exception("Message key $key already exists");
                }
            }
            break;
        }

        $phrases []= $key;
        $this->writeAdditionalFile($phrases);
        return $this;
    }


}
