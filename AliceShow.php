<?php
require_once 'AliceDesignFunction.php';
class AliceShow extends AliceDesignFunction
{
  function call($doc_id,$optionsA=null) {
    $resultO=$this->_designdoc->callShow($this,$doc_id,$optionsA);
   return $resultO;
 }
} 
