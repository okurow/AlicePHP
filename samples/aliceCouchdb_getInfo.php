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
$result=$couchdb->call();

if($result->error) {
   echo "Error:" . $result->rawresponse;
}else{
   echo $result->rawresponse;
}
?>
