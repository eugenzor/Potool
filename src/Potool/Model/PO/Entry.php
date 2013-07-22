<?php
namespace Potool\Model\PO;

class Entry
{
    protected $data;
    static protected $hasher;
    protected $defaultHasher = "Md5";
    function __construct($data)
    {
        $this->data = $data;
        if (isset($data['msgid'])){
            $this->data['msgid'] = str_replace("\r", '', $data['msgid']);
        }
        if (isset($data['msgstr'])){
            $this->data['msgstr'] = str_replace("\r", '', $data['msgstr']);
        }
        if (isset($data['comments'])){
            $this->data['comments'] = str_replace("\r", '', $data['comments']);
        }
        if (isset($data['fuzzy'])){
            $this->setFuzzy((int)$data['fuzzy']);
        }

        $hasherClass = "Potool\\Model\\PO\\Hasher\\" . $this->defaultHasher;
        self::$hasher = new $hasherClass;
    }

    function __get($name)
    {
        if (isset($this->data[$name])){
            return $this->data[$name];
        }
        return null;
    }

    function __set($name, $value){
        $this->data[$name] = str_replace("\r", '', $value);
    }

    protected function join()
    {
        $str =  $this->data['msgid'] . ',' . $this->data['msgstr'] . ','
            . (isset($this->data['comments'])?$this->data['comments']:'') . ','
            . $this->getFuzzy()
        ;
//        echo $str, '<br>';
        return $str;
    }

    function getHash()
    {
        return self::$hasher->get($this->join());
    }

    function verifyHash($hash)
    {
        return self::$hasher->verify($this->join(), $hash);
    }

    function hasFlags()
    {
        return isset($this->data['flags']);
    }

    function getReferences()
    {
        if (!$this->data['reference']){
            return array();
        }
        $reference = trim($this->data['reference']);
        return explode("\n", $reference);
    }

    /**
     * Get if fuzzy
     * @return bool
     */
    function getFuzzy()
    {
        if (isset($this->data['flags']) && strpos($this->data['flags'], 'fuzzy') !== false){
            return true;
        }
        return false;
    }

    /**
     * Set flags data from array
     * @param array $flags
     * @return Entry
     */
    protected function setFlagsFromArray($flags)
    {
        if (count($flags)){
            $this->data['flags'] = join(", ", $flags);
        }else{
            $this->data['flags'] = '';
        }

        return $this;
    }

    /**
     * Set fuzzy flag
     * @param bool $fuzzy
     * @return Entry
     */
    function setFuzzy($fuzzy)
    {
        $flags = array();
        if (isset($this->data['flags'])){
            $flags = explode(",", $this->data['flags']);
            foreach($flags as $i=>$flag){
                $flags[$i] = trim($flag);
                if ($flag == 'fuzzy'){
                    if ($fuzzy){
                        //already exists
                        return $this;
                    }else{
                        //exist but mustn't
                        unset($flags[$i]);

                        return $this->setFlagsFromArray($flags);
                    }
                }
            }
        }
        if ($fuzzy){
            array_unshift($flags, 'fuzzy');
            $this->setFlagsFromArray($flags);
        }

        return $this;
    }
}
