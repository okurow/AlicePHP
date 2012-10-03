<?php
/**
 * Demo for AlicePHP - A Library for CouchDB Environments 
 * By Oliver Kurowski
 * http://couchdbmitphp.de
 * 
 */
require_once (__DIR__)."/../AliceCouchDB.php";
$url="http://localhost:5984";
// AUTH: http://user:pass@localhost:5984

$couchdb=new AliceCouchDB($url);
$result=$couchdb->call("_all_dbs");

if($result->error) {
   echo "Error:" . $result->rawresponse; // result as JSON-Text
}else{
   $allDbsA=$result->response; // result as JSON-Object (here: Array)
   foreach ($allDbsA as $db) {
      echo "<li>" . $db;
   }
}
?>
