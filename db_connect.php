<?php
function db_connect(){
	$result = new mysqli('localhost', 'cl54-forum-5uz', 'HiUn3EoN', 'cl54-forum-5uz');
	if(!$result){
		return false;
	}
	return $result;
}
?>