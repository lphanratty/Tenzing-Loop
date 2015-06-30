<?php

class etNavigator{

public $userGUID;
public $defaultUserAccess;
public $arrModuleAccess;

function __construct($userGUID = "", $defaultUserAccess = 0){
	$this->userGUID = $userGUID;
	$this->defaultUserAccess = $defaultUserAccess;
	
	//Future enhancement
	if(strlen($this->userGUID) > 0){
		$this->arrModuleAccess = $this->getModuleAccess();
	}
}

function getModuleAccess(){
	global $etUser;
	//Future functionality will look up Modules access from a module access table
	//and return an array of modules that the user has access to and which level
	//of access they have for that module (View/Add/Edit/Delete).
	//For the moment we will hard code the array for displaying the menu
	
	$retval = array();
	
	if($etUser->typeID == 4){
		$this->arrModuleAccess[1] = array("MYINFO"=>"My Info",
											"LINK"=>"memberhome.php",
											array("MYPROFILE"=>"My Profile",
	}
	
	return $retval;
}

}

?>