<?php
require_once 'AliceDatabase.php';
class AliceUpdater
{


	static function delinsert($doc,AliceDatabase $database)
	{
		if($rev=$database->docExists($doc->_id)) {
			$database->docDelete($doc->_id,$rev);
		}
		return ($database->call("".$doc->_id,"PUT",$doc));
	}
	
	static function update($doc,AliceDatabase $database)
	{
		if($rev=$database->docExists($doc->_id)) {
			$doc->_rev=$rev;
		}
		return ($database->call("".$doc->_id,"PUT",$doc));
	}




}