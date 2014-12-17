<?php
	require_once('error_handler.php');
	
	class Suggest{
		private $mMysqli;
	
		function __construct(){
			$this->mMysqli = new mysqli('localhost', 'cl54-forum-5uz', 'HiUn3EoN', 'cl54-forum-5uz');
		}
		
		function __destruct(){
			$this->mMysqli->close();
		}
		
		public function getSuggestions($keyword){
			$patterns = array('/\s+/', '/"+/', '/%+/');
			$replace = array('');
			$keyword = preg_replace($patterns, $replace, $keyword);
			if($keyword != ''){
				$query = 'SELECT * FROM history WHERE title LIKE "'.$keyword.'%"';
			} else {
				$query = 'SELECT title FROM history WHERE title=""';
			}
			$result = $this->mMysqli->query($query);
			
			$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			$output .= '<response>';
			if($result->num_rows){
				while($row = $result->fetch_assoc()){
					$output .= '<name postid="'.$row['postid'].'">' .$row['title']. '</name>';
				}
				$result->close();
			}
			$output .= '</response>';
			
			return $output;
		}
	}
?>
































