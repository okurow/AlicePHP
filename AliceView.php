<?php
require_once 'AliceDesignFunction.php';

class AliceView extends AliceDesignFunction{
	private $_map;
	private $_reduce;

	function readFromDisk($path) {
		$verzeichnis=openDir($path);
		$map=0;
		$reduce=0;
		while ($file=readdir($verzeichnis)) {
			if($file=="map.js") {
				$this->_map=file_get_contents($path."/map.js");
				$map=1;
			}
			if($file=="reduce.js") {
				$this->_reduce=file_get_contents($path."/reduce.js");
				$reduce=1;
			}
		}
		return "map:$map, reduce:$reduce";
	}
	
	function setMap($function) {
		$this->_map=$function;
	}
	function setReduce($function) {
		$this->_reduce=$function;
	}
	
	function get() {
		$outO=new stdClass();
		if($this->_map!=null) $outO->map=$this->_map;
		if($this->_reduce!=null) $outO->reduce=$this->_reduce;
		return $outO;
	}
	
function call($optionsA=null) {
		$resultO=$this->_designdoc->callView($this,$optionsA);
		return $resultO;
	}
}