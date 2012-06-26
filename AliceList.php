<?php
require_once 'AliceDesignFunction.php';
class AliceList extends AliceDesignFunction {
  function call(Aliceview $viewO,$optionsA=null) {
    $resultO=$this->_designdoc->callList($this,$viewO,$optionsA);
    return $resultO;
  }
}

