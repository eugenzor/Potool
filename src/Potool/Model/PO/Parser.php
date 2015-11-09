<?php
namespace Potool\Model\PO;

class Parser{

    public $fileHandle;
    protected $context = array();
    public $entryStore;
    protected $line_count = 0;

    protected $match_expressions = array(
        array(
            'type' => 'comments',
            're_match' => '/(^# )|(^#$)/',
            're_capture' => '/#\s*(.*)$/',
        ),
        array(
            'type' => 'extracted-comments',
            're_match' => '/^#. /',
            're_capture' => '/#.\s+(.*)$/',
        ),
        array(
            'type' => 'reference',
            're_match' => '/^#: /',
            're_capture' => '/#:\s+(.*)$/',

        ),
        array(
            'type' => 'flags',
            're_match' => '/^#, /',
            're_capture' => '/#,\s+(.*)$/',
        ),
        array(
            'type' => 'previous-untranslated-string',
            're_match' => '/^#\| /',
            're_capture' => '/#\|\s+(.*)$/',
        ),
        array(
            'type' => 'msgid',
            're_match' => '/^msgid /',
            're_capture' => '/msgid\s+(".*")/',
        ),
        array(
            'type' => 'msgstr',
            're_match' => '/^msgstr /',
            're_capture' => '/msgstr\s+(".*")/',
        ),
        array(
            'type' => 'string',
            're_match' => '/^\s*"/',
            're_capture' => '/^\s*(".*")/',
        ),
        array(
            'type' => 'obsolete-msgid',
            're_match' => '/^#~\s+msgid /',
            're_capture' => '/#~\s+msgid\s+(".*")/',
        ),
        array(
            'type' => 'obsolete-msgstr',
            're_match' => '/^#~\s+msgstr /',
            're_capture' => '/#~\s+msgstr\s+(".*")/',
        ),
        array(
            'type' => 'obsolete-string',
            're_match' => '/^#~\s+\s*"/',
            're_capture' => '/^#~\s+(".*")/',
        ),
        array(
            'type' => 'empty',
            're_match' => '/^$/',
            're_capture' => '/^()$/'
        )

    );

    public function __construct($entryStore){
        $this->entryStore = $entryStore;
    }

    public function writePoFileToStream($fh) {
        $entries = $this->entryStore->read();
        foreach($entries as $entry) {
            fwrite( $fh, $this->convertEntryToString($entry) );
        }
    }
    public function parseEntriesFromStream($fh) {
        $this->lineNumber = 0;
        $entry_count = 0;
        $entry_lines = array();

        while( ($line = fgets($fh)) !== false ) {
            $this->lineNumber++;
            $line = $this->parseLine($line);
            if ( $line["type"] != "empty" ){
                $entry_lines[] = $line;
            }
            else {
                $entry = $this->reduceLines($entry_lines);
                $this->saveEntry( $entry, $entry_count++ );
                $entry_lines = array();
            }
        }
        if ( $entry_lines ){
            $entry = $this->reduceLines($entry_lines);
            $this->saveEntry( $entry, $entry_count++ );
        }
    }

    function readFile($file)
    {
        if (!is_file($file)){
            throw new \Exception("File is not exists");
        }
        $f = fopen($file, 'r');
        $this->parseEntriesFromStream($f);
        fclose($f);
    }

    function writeFile($file)
    {
        $f = fopen($file, 'w');
        $this->writePoFileToStream($f);
        fclose($f);
    }

    function parseLine( $line ){
        $this->line_count++;
        $line_object = array();
        foreach($this->match_expressions as $m) {
            if(preg_match($m['re_match'],$line) ) {
                preg_match($m['re_capture'],$line,$matches);
                $line_object['value'] = $matches[1];
                $line_object['type'] = $m['type'];
            }
        }
        // didn't match anything
        if(!$line_object) {
            throw new \Exception( sprintf("unrecognized line format at line: %d",$this->line_count) );
        }

        return $line_object;
    }

    public function decodeStringFormat( $str ){
        if ( substr($str, 0, 1) == '"' && substr($str, -1,1) == '"' ){
            $result = substr($str, 1, -1);
            $translations = array("\\\\"=>"\\", "\\n"=>"\n",'\\"'=>'"');
            $result = strtr($result, $translations);
        } else {
            throw new \Exception("Invalid PO string (should be surrounded by quotes)\n$str\n");
        }
        return $result;
    }
    /**
     *  translates
     *  Hello"
     *  World
     *  to
     *	 ""
     *  "Hello\"\n"
     *	 "World"
     */
    public function encodeStringFormat($message_string){
        // translate the characters to escapted versions.
        $translations = array("\n"=>"\\n",'"'=>'\\"',"\\"=>"\\\\");
        $result = strtr($message_string, $translations);

        // put the \n's at the end of the lines.
        $result = str_replace("\\n","\\n\n",$result);

        // wrap text so po files can be edited nicely.
        $lines = explode("\n",$result);
        foreach($lines as &$l) {
            $l = $this->wordwrap($l,78);
        }
        $result = implode("\n",$lines);

        // if there are mutiple lines, lets prefix everything with a ""  like the gettext tools
        if(strpos($result,"\n"))
            $result = "\n" . $result;

        // wrap each line in quotes
        $result = $this->addPrefixToLines('"',$result);
        $result = $this->addSuffixToLines('"',$result);

        return $result;

    }
    public function addPrefixToLines($prefix,$text) {
        $text = explode("\n",$text);
        foreach($text as &$line) {
            $line = $prefix . $line;
        }
        return implode("\n",$text);
    }
    public function addSuffixToLines($suffix,$text) {
        $text = explode("\n",$text);
        foreach($text as &$line) {
            $line = $line . $suffix;
        }
        return implode("\n",$text);
    }
    public function wordwrap($text,$max_len=75) {
        $result = "";
        $ll=0;
        $words = explode(" ",$text);
        foreach($words as $w) {
            $lw = mb_strlen($w,'UTF-8');
            if ( $ll + $lw + 1 < $max_len) {
                $result .= $w ." ";
                $ll += $lw + 1;
            }
            else {
                $result .= "\n" . $w . " ";
                $ll = $lw + 1;
            }
        }
        $result = substr($result,0,-1);
        return $result;
    }

    public function saveEntry( $entry, $entry_count ){
        $this->entryStore->write($entry, $entry_count == 0 );
    }
    public function reduceLines( $entry_lines ){
        $entry = array();
        $context = "";
        $is_obsolete = false;

        foreach ( $entry_lines as $line ) {
            // convert the obsolete types into normal type, and mark as obsolete;
            if (substr($line['type'],0,9) == "obsolete-") {
                $is_obsolete = true;
                $line['type'] = substr($line['type'],9);
                preg_match('/".*"/',$line['value'],$m);
                $line['value'] = $m[0];
            }

            if($line['type'] == "string") {
                if($context == "msgid" || $context == "msgstr"){
                    $entry[ $context ][] = $this->decodeStringFormat( $line['value'] );
                } else{
                    throw new \Exception("String in invalid position: " . $line["value"]);
                }
            } else {
                $context = $line["type"];
                if( $line["type"] == "msgid" || $line["type"] == "msgstr" )
                    $entry[$line["type"]][] = $this->decodeStringFormat( $line["value"] );
                else
                    $entry[$line["type"]][] = $line["value"];
            }
        }

        foreach($entry as $k=>&$v){
            if( in_array($k,array('msgid',"msgstr")) ){
                $v  = implode('',$v);
            } else{
                $v = implode("\n",$v);
            }
        }
        $entry['is_obsolete'] = $is_obsolete;

        return $entry;
    }

    public function convertEntryToString( $entry ){
        $prefixes = array(
            "comments"=>"# ",
            "extracted_comments"=>"#. ",
            "reference"=>"#: ",
            "flags"=>"#, ",
            "previous_untranslated_string"=>"#| "
        );

        $msg = "";
        foreach ( $entry as $k=>$v ){
            if($v && isset($prefixes[$k])) {
                $msg .= $this->addPrefixToLines($prefixes[$k],$v) . "\n";
            }
        }
        $msgid = 'msgid ' . $this->encodeStringFormat($entry['msgid']);
        $msgstr = 'msgstr ' . $this->encodeStringFormat($entry['msgstr']);

        if($entry['is_obsolete']) {
            $msgid =  $this->addPrefixToLines('#~ ',$msgid);
            $msgstr = $this->addPrefixToLines('#~ ',$msgstr);
        }

        $msg .= $msgid . "\n";
        $msg .= $msgstr . "\n";
        $msg .= "\n";

        return $msg;
    }


}