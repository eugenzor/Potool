<?php
namespace Potool\Model\PO;

class ArrayStore implements StoreInterface
{
    private $messages;
    private $entries;

    function write($message,$isHeader) {
        $this->messages[] = $message;
    }

    function read() {
        return $this->messages;
    }



    function dataToEntries()
    {
        $this->entries = array();
        if (count($this->messages)){
            foreach($this->messages as $message){
                $entry = new Entry($message);
                $this->entries[] = $entry;
            }
        }
    }

    function getEntries()
    {
        return $this->entries;
    }

    function entriesToData()
    {
        $this->messages = array();
        $properties = array(
            "msgid", "msgstr", "comments", "extracted-comments", "reference", "flags", "is_obsolete",
            "previous-untranslated-string", 'is_header'
        );
        foreach($this->entries as $entry){
            $message = array();
            foreach($properties as $property){
                if ($entry->{$property} !== null){
                    $message[$property] = $entry->{$property};
                }
            }
            if (count($message)){
                $this->messages[]= $message;
            }
        }
    }
}