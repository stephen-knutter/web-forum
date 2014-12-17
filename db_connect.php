<?php
function db_connect(){
	$result = new mysqli('localhost', '', '', '');
	if(!$result){
		return false;
	}
	return $result;
}
?>