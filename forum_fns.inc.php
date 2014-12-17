<?php
	function display_header($title=''){
?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="./css/forum.css" />
	<link rel="stylesheet" type="text/css" href="./css/suggest.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="./js/menu.js" type="text/javascript"></script>
	<script src="./js/suggest.js" type="text/javascript"></script>
	</head>
<body>
<div id="header">
	<a href="index.php" id="webTalkLink"><h1 id="webTalk"><img src="./images/logo.png"></h1></a>
</div>
<?php
}

function display_footer(){
?>
</body>
</html>
<?php
}

function display_tree($expanded, $row=0, $start=0, $message=null, $parent=0){
	echo "<table width=760>";
	
	if($start > 0){
		$sublist = true;
	} else {
		$sublist = false;
	}
	
	$tree = new treenode($start, '', '', '', 1, true, -1, $expanded, $sublist, $message, $parent);
	
	$tree->display($row, $sublist);
	echo "</table>";
}

function check_replies($id){
	$conn = db_connect();
	$query_reply = "SELECT * FROM posts WHERE parent = '$id'";
	$result_reply = $conn->query($query_reply);
	if($result_reply->num_rows > 0){
		while($row_reply = $result_reply->fetch_assoc()){
		echo '<div class="borderReply">';
			echo "<p class='replyTime'>".$row_reply['posted']."</p>";
			echo "<p class='replyReplyMessage'><img src='images/ninja.png' width=20 height=20>".$row_reply['message']. '<span class="replyPoster">-'.$row_reply['poster'].'</span></p>';
		echo '</div>';
		}
	}
}

function format_date($time){
	$date = date('m/d/y H:i:s', $time);
	return $date;
}

function create_blurb($id){
	$conn = db_connect();
	$query_blurb = "SELECT message FROM posts WHERE parent ='" . $id . "' LIMIT 1";
	$result_blurb = $conn->query($query_blurb);
	if($result_blurb->num_rows < 1){
		$blurb = '\'...\'';
	} else {
		$row_blurb = $result_blurb->fetch_array();
		$blurb = $row_blurb[0];
		$blurb = substr($blurb, 0, 40);
		$blurb = $blurb . '...';
	}
	
	return $blurb;
}

function get_views($postid){

	$conn = db_connect();
	$query = "SELECT * FROM history WHERE postid='".$postid."'";
	$result = $conn->query($query);
	$row = $result->fetch_assoc();
	
	if($row['replies'] > 0){
		if($row['replies'] == 1){
			$view = 'View';
		} else {
			$view = 'Views';
		}
		echo "<td align='right'><span class='viewsSpan'>".$row['replies']."</span> ".$view."<img class='bubble_pic' src='images/binoculars.png' alt='Top Discussion' style='width: 10px; height: 10px; padding-left: 1px;'></td>";
	} else {
		echo "<td align='right'><span class='viewsSpan'>0</span> Views<img class='bubble_pic' src='images/binoculars.png' alt='Top Discussion' style='width: 10px; height: 10px; padding-left: 1px;'></td>";
	}
}

function thread_count($id){
	$conn = db_connect();
	$query_thread = "SELECT poster FROM posts WHERE parent = '".$id."'";
	$result_thread = $conn->query($query_thread);
	$thread_count = $result_thread->num_rows;
	if($thread_count < 1){
		$thread_count = 0;
	}
	return $thread_count;
}

function get_count($postid){
	$count = thread_count($postid);
	if($count == 1){
		$reply = 'Reply';
	} else {
		$reply = 'Replies';
	}
	echo "<td align='right'><span class='viewsSpan'>".$count."</span> ".$reply."<img class='bubble_pic' src='images/bubble.png' alt='Top Discussion' style='width: 10px; height: 10px;'></td>";
}

function get_topic($postid){
	echo "<td align='right'>".create_blurb($postid)."</td>";
}
?>