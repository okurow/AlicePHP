<?php
class AliceDatabase {
  private $_couchdb;
  private $_name;
  function __construct($name, AliceCouchDB $couchdb) {
    $this->_name=$name;
    $this->_couchdb=$couchdb;
  }
  function getName() {
    return $this->_name;
  }
  function exists() {
    $resultO=$this->call("");
    if(!$resultO->error) {
      return true;
    }else{
      return false;
    }
  }
  function create() {
    $resultO=$this->call("","PUT");
    return $resultO;
  }
  function delete() {
    $resultO=$this->call("","DELETE");
    return $resultO;
  }
  function call ($befehl="",$method="GET",$datenO=null) {
    $resultO=$this->_couchdb->call($this->_name."/".$befehl,
    $method,$datenO);
    return $resultO;		
  }
  function docExists($docId="") {
    $resultO=$this->call("".$docId);
    if(!$resultO->error) {
      return $resultO->response->_rev;
    }else{
      return false;
    }
  }
  function docDelete($doc_id="",$rev="") {
    $resultO=$this->call("".$doc_id."?rev=".$rev,"DELETE");
    return $resultO;   	
  }  
  
  
}

