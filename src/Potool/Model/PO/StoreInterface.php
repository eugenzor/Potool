<?php
namespace Potool\Model\PO;

interface StoreInterface {
    public function write( $message, $isHeader );
    public function read();
}
