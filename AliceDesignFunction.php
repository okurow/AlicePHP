<?php
class AliceDesignFunction
{
	protected $_designdoc;
	protected $_function;
	
	function __construct($name) {
		$this->_name=$name;
	}
	function getName() {
		return $this->_name;
	}
	
	function readFromDisk($path) {
		$this->_function=utf8_encode(file_get_contents($path));
		return "function:".$this->_function;
	}
	function setFunction($function) {
		$this->_function=$function;
		
	}
	function setDesigndoc(AliceDesignDocument $designdoc) {
		$this->_designdoc=$designdoc;
	}
	
	function get() {
		return $this->_function;
	}
}