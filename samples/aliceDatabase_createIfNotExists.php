<?php
/**
 * Demo for AlicePHP - A Library for CouchDB Environments 
 * By Oliver Kurowski
 * http://couchdbmitphp.de
 * 
 */
require_once (__DIR__)."/../AliceCouchDB.php";
require_once (__DIR__)."/../AliceDatabase.php";
$url="http://localhost:5984";
// AUTH: http://user:pass@localhost:5984

$couchdb=new AliceCouchDB($url);
$database=new AliceDatabase("sample_db",$couchdb);

if($database->exists()) {
   echo "Database " . $database->getName() ." exists.";
}else{
   $result=$database->create();
   if($result->error) {
      echo "Error: " . $result->rawresponse;
   }else{
      echo $result->rawresponse;
   }
}
?>
