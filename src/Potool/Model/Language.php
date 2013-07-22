<?php
namespace Potool\Model;
/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */
class Language
{

    protected $module;
    protected $name;
    protected $mergeCommand = 'msgmerge -U';
    protected $compileCommand = 'msgfmt';

    protected $store, $parser;

    static function factory(Module $module, $name)
    {
        $langDir = $module->getPath() . '/language';
        $fileName = $langDir . '/' . $name . '.po';
        if (is_file($fileName)){
            throw new \Exception("Language file $fileName already exists");
        }
        if (!$module->isWritable()){
            throw new \Exception("Module language folder $langDir isn't writable");
        }
        touch($fileName);
        chmod($fileName, 0666);
        return new Language($module, $name);
    }

    function __construct(Module $module, $name)
    {
        $this->setModule($module);
        $this->name = $name;
    }


    function getName()
    {
        return $this->name;
    }

    function setModule(Module $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Get language module
     * @return Module
     */
    function getModule()
    {
        return $this->module;
    }


    function getPoFile()
    {
        return $this->getModule()->getPath() . '/language/' . $this->name . '.po';
    }

    function getMoFile()
    {
        return $this->getModule()->getPath() . '/language/' . $this->name . '.mo';
    }

    /**
     * Delete module
     * @throws \Exception
     */
    function delete()
    {
        $po = $this->getPoFile();
        $mo = $this->getMoFile();
        if (is_file($po)){
            $deleted = unlink($po);
            if (!$deleted){
                throw new \Exception("Can't delete file $po");
            }
        }

        if (is_file($mo)){
            $deleted = unlink($mo);
            if (!$deleted){
                throw new \Exception("Can't delete file $mo");
            }
        }
        unset($this);
    }

    /**
     * Set merge command (for testing)
     * @param string $command
     * @return Language
     */
    function setMergeCommand($command)
    {
        $this->mergeCommand = $command;
        return $this;
    }


    /**
     * Set compile command (for testing)
     * @param string $command
     * @return Language
     */
    function setCompileCommand($command)
    {
        $this->mergeCommand = $command;
        return $this;
    }


    /**
     * Merge language po with new pot
     * @param Pot $pot
     * @return Language
     * @throws \Exception
     */
    function merge(Pot $pot)
    {
        if (is_file($this->getPoFile()) && trim($this->getPoContent())){
            $command = $this->mergeCommand . ' ' . escapeshellarg($this->getPoFile()) . ' ' . escapeshellarg($pot) . ' 2>&1';
            if (is_file($this->getPoFile().'~')){
                unlink($this->getPoFile().'~');
            }
            $out = shell_exec($command);

            if (substr_count($out, 'done')){
                return $this;
            }
            $out = trim($out);
            if ($out){
                throw new \Exception($out);
            }
        }else{
            copy($pot, $this->getPoFile());
            chmod($this->getPoFile(), 0666);
        }

        return $this;
    }


    /**
     * Compile language po to mo
     * @return Language
     * @throws \Exception
     */
    function compile()
    {
        if (is_file($this->getMoFile()) && !is_writable($this->getMoFile())){
            $languagesDir = $this->getModule()->getPath() . '/language';
            if (!is_writable($languagesDir)){
                throw new \Exception("Language dir $languagesDir is not writable");
            }
            unlink($this->getMoFile());
        }

        $command = $this->compileCommand . ' ' . $this->getPoFile() . ' -o ' . $this->getMoFile() . ' 2>&1';
        $out = trim(shell_exec($command));
        if ($out){
            throw new \Exception($out);
        }
        return $this;
    }

    /**
     * Check if has mo file
     * @return bool
     */
    function hasMo()
    {
        return is_file($this->getMoFile());
    }

    /**
     * Get Po content
     * @return string content
     * @throws \Exception
     */
    function getPoContent()
    {
        if (!is_file($this->getPoFile())){
            throw new \Exception("Can't find po file for language $this");
        }
        return file_get_contents($this->getPoFile());
    }


    /**
     * Put content to po file
     * @param $content
     * @return Language
     * @throws \Exception
     */
    function setPoContent($content)
    {
        if (!is_writable($this->getPoFile())){
            $languagesDir = $this->getModule()->getPath() . '/language';
            if (!is_writable($languagesDir)){
                throw new \Exception("Language dir $languagesDir is not writable");
            }
            unlink($this->getPoFile());
        }
        file_put_contents($this->getPoFile(), $content);
        return $this;
    }


    /**
     * Return datetime of po file
     * @return DateTime
     * @throws \Exception
     */
    function getPoDate()
    {
        $po = $this->getPoFile();
        if (!is_file($po)){
            throw new \Exception("Po $po doesn't exists");
        }
        $ts = filemtime($po);
        $date = new \DateTime('@'.$ts);
        return $date;
    }


    /**
     * Return datetime of mo file
     * @return DateTime
     * @throws \Exception
     */
    function getMoDate()
    {
        $mo = $this->getMoFile();
        if (!is_file($mo)){
            throw new \Exception("Mo $mo doesn't exists");
        }
        $ts = filemtime($mo);
        $date = new \DateTime('@'.$ts);
        return $date;
    }

    /**
     * Count untranslated items
     * @return int
     */
    function countUntranslated()
    {
        $content = $this->getPoContent();
        $content = str_replace("\r", "", $content);

        /*
         * Remove
         * msgid ""
         * msgstr ""
         * from begining
         */
        $parts = explode('msgid ""
msgstr ""', $content);
        if (count($parts > 1)){
            $content = $parts[1];
        }
        return substr_count($content, 'msgstr ""');
    }


    function __toString()
    {
        return $this->name;
    }

    function getEntries()
    {
        $this->store = new PO\ArrayStore;
        $this->parser = new PO\Parser($this->store);
        $this->parser->readFile($this->getPoFile());
        $this->store->dataToEntries();
        return $this->store->getEntries();
    }

    function updateEntries()
    {
        $this->store->entriesToData();
        $this->parser->writeFile($this->getPoFile());
        return $this;
    }
}
