<?php
namespace Potool\Model\PO\Hasher;


class Md5 implements HasherInterface
{
    function get($string)
    {
        return md5($string);
    }

    function verify($string, $hash)
    {
//        echo $this->get($string) . '===' . $hash, '<br>';
        return $this->get($string) === $hash;
    }
}
