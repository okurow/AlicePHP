<?php
  require_once 'AliceResponse.php';
  class AliceCouchDB {
    private $_url;  
    
    function __construct($url) {
      $this->_url=$url;
    }
    
    function call ($befehl="", $method="GET", $datenO=null) {
      $ch=curl_init($this->_url."/".$befehl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //String-Attribute in utf8 umwandeln
      if($datenO!=null && $method!="ATTACH" && $method!="MULTI") {
      	foreach (get_object_vars($datenO) as $key=>$value) {
      		if(is_string($value)){
      			$datenO->$key=utf8_encode($value);
      		}
      	}
      }
      
     switch(strtoupper($method)) {
       case "GET":
       // curl ist standardmäßig auf GET
       break;
     case "PUT":
       $datenJSON=json_encode($datenO);
       $fp=tmpfile();
       fwrite ($fp, $datenJSON);
       fseek ($fp,0);         // "zurückspulen"
       curl_setopt($ch, CURLOPT_PUT, true);
       curl_setopt($ch, CURLOPT_INFILE, $fp);
       curl_setopt($ch, CURLOPT_INFILESIZE, strlen($datenJSON));
       break;
     case "POST":
       $datenJSON=json_encode($datenO);
       $fp=tmpfile();
       fwrite ($fp,$datenJSON);
       fseek ($fp,0);         // "zurückspulen"
       $headers=array("Content-Type: application/json","Content-length: ".strlen($datenJSON));
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_INFILE, $fp);
       curl_setopt($ch, CURLOPT_INFILESIZE, strlen($datenJSON));
       break;
     case "DELETE":
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
         break;
     case "ATTACH":
       $daten=$datenO->data;
       $fp=tmpfile();
       fwrite ($fp,$daten);
       fseek ($fp,0);         // "zurückspulen"
       $headers=array("Content-Type: ".$datenO->content_type);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_PUT, true);
       curl_setopt($ch, CURLOPT_INFILE, $fp);
       curl_setopt($ch, CURLOPT_INFILESIZE, strlen($daten));
      break;
    
     
     
     
     
     }
      
      
      
      $resultJSON=curl_exec($ch);
	  if($resultJSON===false) {
		throw new Exception ("Fehler bei Verbindung mit der CouchDB:".curl_error($ch));
	  }
      curl_close($ch);
    
      
       $resultO=json_decode($resultJSON);
    
       $error=false; 
       if(@property_exists($resultO,"error")) {
         $error=true;
       }
       $responseO=new AliceResponse($this->_url."/".$befehl,$error,$resultO,$resultJSON);
     
     return $responseO;
   }
   function getVersion() {
   	 $resultO=$this->call();
     if($resultO->error) {
     	throw new Exception("Fehler: ".$resultO->response->error);
     }
     return $resultO->response->version;
   }
   	

   function getAllDbs() {
     $resultO=$this->call("_all_dbs");
     if($resultO->error) {
     	throw new Exception("Fehler: ".$resultO->response->error);
     }
     return $resultO->response;
   }
   
   function docExists($doc_id) {
     $resultO=$this->call("".$doc_id);
     if(!$resultO->error) {
       return $resultO->result->_rev;
     }else{
       return false;
     }
   }
 }
