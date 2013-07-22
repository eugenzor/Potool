<?php

namespace Potool\Model;

/**
 * Create pot:
 * find . -iname "*.php" -or -iname "*.phtml"|xargs xgettext -o - -L PHP --keyword=translate
 * User: eugene
 * Date: 1/30/13
 * Time: 2:48 PM
 * To change this template use File | Settings | File Templates.
 */
class Pot
{
    protected $file;

    function __construct($src, $options=null)
    {
        $tmpDir = isset($options['potool']['tmp_dir'])?$options['potool']['tmp_dir']:'/tmp';
        $this->file = tempnam($tmpDir, 'pot_');
        $command = 'find '
            .escapeshellarg($src)
            .' -iname "*.php" -or -iname "*.phtml"|xargs xgettext -o - -L PHP --keyword=translate --from-code=UTF-8 > '
            .$this->file;
        shell_exec($command);

        $content = file_get_contents($this->file);
        $content = str_replace('"Content-Type: text/plain; charset=CHARSET\n"', '"Content-Type: text/plain; charset=UTF-8\n"', $content);
        file_put_contents($this->file, $content);
    }

    function __toString()
    {
        return $this->file;
    }

    function __destruct()
    {
        unlink($this->file);
    }

    function getFile()
    {
        return $this->file;
    }
}
