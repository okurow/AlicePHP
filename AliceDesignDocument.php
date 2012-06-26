<?php
class AliceDesignDocument {
	private $_name;
	private $_database;
	private $_viewA;	
	private $_showA;
	private $_listA;
	private $_validateDocUpdate;
	private $_updateA;
	private $_filtersA;
	
	function __construct($name,$database) {
		$this->_name=$name;
		$this->_database=$database;
	}
	
	function getName() {
		return $this->_name;
	}
	function existsOnCouch() {
		$resultO=$this->call("");
		if($resultO->error) {
			return false;
		}else{
			return true;
		}
	}
	
	function createOnCouch() {
		$resultO=$this->call("","PUT",$this);
		return $resultO;
	}
	
	function deleteFromCouch() {
		$resultO=$this->call("?rev=".$this->getRev(),"DELETE");
		return $resultO;
	}
	
	function getRev() {
		$resultO=$this->call("");
		if($resultO->error) {
			return null;
		}else{
			return $resultO->response->_rev;
		}
	}
	
	function writeToCouch() {
		$rev=$this->getRev();
		$thisDoc=$this->get();
		if($rev!=null) $thisDoc->_rev=$rev;
		$resultO=$this->call("","PUT",$thisDoc);
		return $resultO;
		
	}
	
	function getFromCouch() {
		$resultO=$this->call("");
		$designO=$resultO->response;
		if($designO->views!=null) {
			$viewA=get_object_vars($designO->views);
			foreach($viewA as $key=>$value) {
				$t_view=new AliceView($key);
				if($value->map!=null) {
					$t_view->setMap($value->map);
				}
				if($value->reduce!=null) {
					$t_view->setReduce($value->reduce);
				}
				$this->addView($t_view);
			}
		}
		if($designO->shows!=null) {
			$showA=get_object_vars($designO->shows);
			foreach($showA as $key=>$value) {
				$t_show=new AliceShow($key);
				$t_show->setFunction($value);
				$this->addShow($t_show);
			}
		}
		if($designO->lists!=null) {
			$listA=get_object_vars($designO->lists);
			foreach($listA as $key=>$value) {
				$t_list=new AliceList($key);
				$t_list->setFunction($value);
				$this->addShow($t_list);
			}
		}
	
		if($designO->validate_doc_update!=null) {
			$t_validate_doc_update=new AliceValidateDocUpdate("validate_doc_update");
			$t_validate_doc_update->setFunction($designO->validate_doc_update);
			$this->addValidateDocUpdate($t_validate_doc_update);
 		}

  		if($designO->updates!=null) {
  			$updatesA=get_object_vars($designO->updates);
  			foreach ($updatesA as $key=>$value) {
  				$t_update=new AliceUpdate($key);
  				$t_update->setFunction($value);
				$this->addUpdate($t_update);
  			}
  		}

  		if($designO->filters!=null) {
  			$filtersA=get_object_vars($designO->filters);
  			foreach ($filtersA as $key=>$value) {
  				$t_filter=new AliceFilter($key);
  				$t_filter->setFunction($value);
				$this->addFilter($t_filter);
  			}
  		}		

		return $designA;
	}
	
	function call ($befehl="",$method="GET",$datenO=null) {
		$resultO=$this->_database->call("_design/".$this->_name."/".$befehl,$method,$datenO);
		return $resultO;		
	}
	
function get() {
  $outO=new stdClass();
  $outO->_id=$this->_name;
  if(count($this->_viewA)>0) {
    $outO->views=new stdClass();
    for ($i=0;$i<count($this->_viewA);$i++) {
      $outO->views->{$this->_viewA[$i]->getName()}=
      $this->_viewA[$i]->get();		
    }
  }
  if(count($this->_showA)>0) {
    $outO->shows=new stdClass();
    for ($i=0;$i<count($this->_showA);$i++) {
      $outO->shows->{$this->_showA[$i]->getName()}=
      $this->_showA[$i]->get();		
    }
  }
  if(count($this->_listA)>0) {
    $outO->lists=new stdClass();
    for ($i=0;$i<count($this->_listA);$i++) {
      $outO->lists->{$this->_listA[$i]->getName()}=
      $this->_listA[$i]->get();		
    }
  }
  if($this->_validateDocUpdate!=null) {
    $outO->validate_doc_update=$this->_validateDocUpdate->get();	
  }
  
  if(count($this->_updateA)>0) {
    $outO->updates=new stdClass();
	for ($i=0;$i<count($this->_updateA);$i++) {
	  $outO->updates->{$this->_updateA[$i]->getName()}=$this->_updateA[$i]->get();		
	}
  }
  
  if(count($this->_filtersA)>0) {
    $outO->filters=new stdClass();
	for ($i=0;$i<count($this->_filtersA);$i++) {
	  $outO->filters->{$this->_filtersA[$i]->getName()}=$this->_filtersA[$i]->get();		
	}
  }		
		
  return $outO;
}	
		


//----- Kapitel 10 Erweiterungnen
function viewExists(AliceView $viewO) {
		$found=false;
		for ($i=0;$i<count($this->_viewA);$i++) {
			if($this->_viewA[$i]->getName()==$viewO->getName()) $found=true;			
		}
		return $found;
	}
	
	
	
	function addView(AliceView $viewO) {
		if($this->viewExists($viewO)) {
			throw new Exception ("View '".$viewO->getName()."' bereits angelegt.");
		}else{
			$this->_viewA[]=$viewO;
			$viewO->setDesigndoc($this);
		}
	}
	function removeView(AliceView $viewO) {
		$removed=falsE;
		for ($i=0;$i<count($this->_viewA);$i++) {
			if($this->_viewA[$i]->getName()==$viewO->getName()) {
				unset ($this->_viewA[$i]);	
				$removed=true;
				$viewO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callView(AliceView $viewO,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_view/".$viewO->getName().$parameterS);
		return $resultO;
	}
	
//-------Kapitel 11 Erweiterungen
	
	function showExists(AliceShow $showO) {
	  $found=false;
	  for ($i=0;$i<count($this->_showA);$i++) {
	    if($this->_showA[$i]->getName()==$showO->getName()) $found=true;	
	  }
	  return $found;
	}
	function addShow(AliceShow $showO) {
	  if($this->showExists($showO)) {
	    throw new Exception ("Show '".$showO->getName()
	    ."' bereits angelegt.");
	  }else{
	    $this->_showA[]=$showO;
	    $showO->setDesigndoc($this);
	  }
	}
	function removeShow(AliceShow $showO) {
	  $removed=false;
	  for ($i=0;$i<count($this->_showA);$i++) {
	    if($this->_showA[$i]->getName()==$showO->getName()) {
	      unset ($this->_showA[$i]);	
	      $removed=true;
	      $showO->setDesigndoc(null);
	    }		
	  }
	  return $removed;
	}
	function callShow($showO,$doc_id,$optionsA=null) {
	  $parameterS="";
	  if(count($optionsA)>0) {
	    foreach ($optionsA as $key=>$value) {
	      $parameterS.="&".$key."=".$value;
	    }
	    $parameterS="?".substr($parameterS,1);
	  }
	  $resultO=$this->call("/_show/".$showO->getName()
	  ."/".$doc_id.$parameterS);
	  return $resultO;
	}
	

function listExists(AliceList $listO) {
		$found=false;
		for ($i=0;$i<count($this->_listA);$i++) {
			if($this->_list[$i]->getName()==$listO->getName()) $found=true;			
		}
		return $found;
	}
	
	
	function addList(AliceList $listO) {
		if($this->listExists($listO)) {
			throw new Exception ("List '".$listO->getName()."' bereits angelegt.");
		}else{
			$this->_listA[]=$listO;
			$listO->setDesigndoc($this);
		}
	}
	function removeList(AliceList $listO) {
		$removed=false;
		for ($i=0;$i<count($this->_listA);$i++) {
			if($this->_listA[$i]->getName()==$listO->getName()) {
				unset ($this->_listA[$i]);	
				$removed=true;
				$listO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callList(AliceList $listO,AliceView $viewO,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_list/".$listO->getName()."/".$viewO->getName().$parameterS);
		return $resultO;
	}
	
//---- Kapitel 12 Erweiterungen

function validateDocUpdateExists(AliceValidateDocUpdate $validateO) {
  $found=false;
  if($this->_validateDocUpdate!=null) {
    if($this->_validateDocUpdate->getName()==$validateO->getName()){
     $found=true;			
    }
    return $found;
  }
}
function addValidateDocUpdate(AliceValidateDocUpdate $validateO) {
  $this->_validateDocUpdate=$validateO;
  $validateO->setDesigndoc($this);
}
function removeValidateDocUpdate() {
  $this->_validateDocUpdate->setDesigndoc(null);
  unset ($this->_validateDocUpdate);	
}
	


function updateExists(AliceUpdate $updateO) {
		$found=false;
		for ($i=0;$i<count($this->_updateA);$i++) {
			if($this->_updateA[$i]->getName()==$updateO->getName()) $found=true;			
		}
		return $found;
	}
		
	function addUpdate(AliceUpdate $updateO) {
		if($this->updateExists($updateO)) {
			throw new Exception ("Update '".$updateO->getName()."' bereits angelegt.");
		}else{
			$this->_updateA[]=$updateO;
			$updateO->setDesigndoc($this);
		}
	}
	
	function removeUpdate(AliceUpdate $updateO) {
		$removed=false;
		for ($i=0;$i<count($this->_updateA);$i++) {
			if($this->_updateA[$i]->getName()==$updateO->getName()) {
				unset ($this->_updateA[$i]);	
				$removed=true;
				$updateO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callUpdate($updateO,$doc_id,$doc,$optionsA=null) {
		  $parameterS="";
		  if(count($optionsA)>0) {
		    foreach ($optionsA as $key=>$value) {
		      $parameterS.="&".$key."=".$value;
		    }
		    $parameterS="?".substr($parameterS,1);
		  }
		  $resultO=$this->call("/_update/".$updateO->getName()."/"
		  .$doc_id.$parameterS,"PUT",$doc);
		 
		  $t_responseO=json_decode(utf8_encode($resultO->rawresponse));
		  if($t_responseO!=null) {
		  	$resultO->response=$t_responseO;
		  }
		  return $resultO;
} 
	
	
//-----Kapitel 14 Erweiterungen

function filterExists(AliceFilter $filterO) {
		$found=false;
		for ($i=0;$i<count($this->_filtersA);$i++) {
			if($this->_filtersA[$i]->getName()==$filterO->getName()) $found=true;			
		}
		return $found;
	}
		
	function addFilter(AliceFilter $filterO) {
		if($this->filterExists($filterO)) {
			throw new Exception ("Filter '".$filterO->getName()."' bereits angelegt.");
		}else{
			$this->_filtersA[]=$filterO;
			$filterO->setDesigndoc($this);
		}
	}
	
	function removeFilter(AliceFilter $filterO) {
		$removed=false;
		for ($i=0;$i<count($this->_filtersA);$i++) {
			if($this->_filtersA[$i]->getName()==$filterO->getName()) {
				unset ($this->_filtersA[$i]);	
				$removed=true;
				$filterO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	
	

	/*
	
	function getDatabase() {
		return $this->_database;
	}
	
	
	
	
	function viewExists(AView $viewO) {
		$found=false;
		for ($i=0;$i<count($this->_viewA);$i++) {
			if($this->_viewA[$i]->getName()==$viewO->getName()) $found=true;			
		}
		return $found;
	}
	
	
	
	function addView(AView $viewO) {
		if($this->viewExists($viewO)) {
			throw new Exception ("View '".$viewO->getName()."' bereits angelegt.");
		}else{
			$this->_viewA[]=$viewO;
			$viewO->setDesigndoc($this);
		}
	}
	function removeView(AView $viewO) {
		$removed=falsE;
		for ($i=0;$i<count($this->_viewA);$i++) {
			if($this->_viewA[$i]->getName()==$viewO->getName()) {
				unset ($this->_viewA[$i]);	
				$removed=true;
				$viewO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callView($viewO,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_view/".$viewO->getName().$parameterS);
		return $resultO;
	}
	
	function showExists(AShow $showO) {
		$found=false;
		for ($i=0;$i<count($this->_showA);$i++) {
			if($this->_showA[$i]->getName()==$showO->getName()) $found=true;			
		}
		return $found;
	}
		
	function addShow(AShow $showO) {
		if($this->showExists($showO)) {
			throw new Exception ("Show '".$showO->getName()."' bereits angelegt.");
		}else{
			$this->_showA[]=$showO;
			$showO->setDesigndoc($this);
		}
	}
	function removeShow(AShow $showO) {
		$removed=false;
		for ($i=0;$i<count($this->_showA);$i++) {
			if($this->_showA[$i]->getName()==$showO->getName()) {
				unset ($this->_showA[$i]);	
				$removed=true;
				$showO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callShow($showO,$doc_id,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_show/".$showO->getName()."/".$doc_id.$parameterS);
		return $resultO;
	}
	
	function listExists(AList $listO) {
		$found=false;
		for ($i=0;$i<count($this->_listA);$i++) {
			if($this->_list[$i]->getName()==$listO->getName()) $found=true;			
		}
		return $found;
	}
	
	
	function addList(AList $listO) {
		if($this->listExists($listO)) {
			throw new Exception ("List '".$listO->getName()."' bereits angelegt.");
		}else{
			$this->_listA[]=$listO;
			$listO->setDesigndoc($this);
		}
	}
	function removeList(AList $listO) {
		$removed=false;
		for ($i=0;$i<count($this->_listA);$i++) {
			if($this->_listA[$i]->getName()==$listO->getName()) {
				unset ($this->_listA[$i]);	
				$removed=true;
				$listO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callList(AList $listO,AView $viewO,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_list/".$listO->getName()."/".$viewO->getName().$parameterS);
		return $resultO;
	}
	
	
	

	
	
	function get() {
		$outO=new stdClass();
		$outO->_id=$this->_name;
		$outO->views=new stdClass();
		for ($i=0;$i<count($this->_viewA);$i++) {
			$outO->views->{$this->_viewA[$i]->getName()}=$this->_viewA[$i]->get();		
		}
		$outO->shows=new stdClass();
		for ($i=0;$i<count($this->_showA);$i++) {
			$outO->shows->{$this->_showA[$i]->getName()}=$this->_showA[$i]->get();		
		}
		$outO->lists=new stdClass();
		for ($i=0;$i<count($this->_listA);$i++) {
			$outO->lists->{$this->_listA[$i]->getName()}=$this->_listA[$i]->get();		
		}
		if($this->_validateDocUpdate!=null) {
			$outO->validate_doc_update=$this->_validateDocUpdate->get();		
		}
		return $outO;
	}
	
	
	function validateDocUpdateExists(AValidateDocUpdate $validateO) {
		$found=false;
		if($this->_validateDocUpdate!=null) {
			if($this->_validateDocUpdate->getName()==$validateO->getName()) $found=true;			
			return $found;
		}
	}
	
	function addValidateDocUpdate(AValidateDocUpdate $validateO) {
		$this->_validateDocUpdate=$validateO;
		$validateO->setDesigndoc($this);
		
	}
	
	function removeValidateDocUpdate() {
		$this->_validateDocUpdate->setDesigndoc(null);
		unset ($this->_validateDocUpdate);	
	}
	
	//---------------- Block Update -----------------
	function updateExists(AUpdate $updateO) {
		$found=false;
		for ($i=0;$i<count($this->_updateA);$i++) {
			if($this->_updateA[$i]->getName()==$updateO->getName()) $found=true;			
		}
		return $found;
	}
		
	function addUpdate(AUpdate $updateO) {
		if($this->updateExists($updateO)) {
			throw new Exception ("Update '".$updateO->getName()."' bereits angelegt.");
		}else{
			$this->_updateA[]=$updateO;
			$updateO->setDesigndoc($this);
		}
	}
	
	function removeUpdate(AUpdate $updateO) {
		$removed=false;
		for ($i=0;$i<count($this->_updateA);$i++) {
			if($this->_updateA[$i]->getName()==$updateO->getName()) {
				unset ($this->_updateA[$i]);	
				$removed=true;
				$updateO->setDesigndoc(null);
			}		
		}
		return $removed;
	}
	
	function callUpdate($updateO,$doc,$optionsA=null) {
		$parameterS="";
		if(count($optionsA)>0) {
			foreach ($optionsA as $key=>$value) {
				$parameterS.="&".$key."=".$value;
			}
			$parameterS="?".substr($parameterS,1);
		}
		$resultO=$this->call("/_update/".$updateO->getName()."/".$doc_id.$parameterS,"PUT",$doc);
		return $resultO;
	}
	*/
	
	
}