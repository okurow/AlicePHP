<?php
/**
 * Demo for AlicePHP - A Library for CouchDB Environments 
 * Create, read, update and delete a document 
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

if(!$database->exists()) {
   $result=$database->create();
    if($result->error) {
      throw new Exception( "Error: " . $result->rawresponse );
   }
}

$doc_id="1";
// create, if not exists
if(!$database->docExists($doc_id)) {
   $doc=new stdClass();
   $doc->name="Big Jim";
   $doc->parents=array("Barbie","Ken");
   $result=$database->call($doc_id,"PUT",$doc);
   if($result->error) {
      echo "Error: " . $result->rawresponse;
   }else{
      echo $result->rawresponse;
   }
}

//read Data:
$read_doc=$database->call($doc_id)->response; 
echo "<hr>doc:<br>";
echo json_encode($read_doc);
echo "<hr>";

// update data:
$read_doc->age=40;
$result=$database->call($doc_id,"PUT",$read_doc);
if($result->error) {
   echo "Error: " . $result->rawresponse;
}else{
   echo $result->rawresponse;
}

//read Data again:
$upd_doc=$database->call($doc_id)->response; 
echo "<hr>updated doc:<br>";
echo json_encode($upd_doc);
echo "<hr>";

//delete doc:
$result=$database->docDelete($doc_id,$upd_doc->_rev);
if($result->error) {
   echo "Error: " . $result->rawresponse;
}else{
   echo $result->rawresponse;
}



?>
