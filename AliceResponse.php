<?php
class AliceResponse {
	public $call;
	public $error;
	public $response;
	public $rawresponse;
	
	function __construct($call=null,$error=null,$response=null,$rawresponse=null) {
		$this->call=$call;
		$this->error=$error;
		$this->response=$response;
		$this->rawresponse=$rawresponse;
	}
}