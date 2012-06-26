<?php
require_once 'AliceDesignFunction.php';
class AliceUpdate extends AliceDesignFunction {
  function call($doc_id,$doc,$optionsA=null) {
    $resultO=$this->_designdoc->callUpdate($this,$doc_id,$doc,$optionsA);
    return $resultO;
  }
}
