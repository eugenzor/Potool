<?php
namespace Potool\Model\PO\Hasher;

interface HasherInterface
{
    public function get($string);
    public function verify($string, $hash);
}
