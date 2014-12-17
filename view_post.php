<?php
	session_start();
	require('forum_fns.inc.php');
	require('treeclass.php');
	require('db_connect.php');
	$image_path = './images';
	$location = $_SERVER['REQUEST_URI'];
	
	display_header('View Post');
	if(!(@$postid = $_GET['postid'])){
		@$postid = $_POST['postid'];
	}
	
	if(isset($_POST['login'])){
		$errors = array();
		$username = $_POST['username'];
		$password = $_POST['password'];
		$location = $_POST['location'];
		if(!empty($username) && !empty($password)){
			$conn = db_connect();
			$postid = $_GET['postid'];
			$query = "SELECT username, password FROM users WHERE username='".$username."' AND password=sha1('".($password)."')";
			$result = $conn->query($query);
			if($result->num_rows == 1){
				$_SESSION['admin_user'] = $username;
				header('Location: '.$location);
			} else {
				header('Location: '.$location);
				$errors['login'] = 'Username and Password could not be found';
			}
		} else {
			header('Location: '.$location);
			$errors['login'] = 'Username and Password could not be found';
		}
	}
	
	
	if(isset($_POST['add_thread'])){
		$location = $_POST['location'];
		if(isset($_SESSION['admin_user'])){
			$conn = db_connect();
			$message = $conn->real_escape_string($_POST['thread_update']);
			$postid = $conn->real_escape_string($_POST['postid']);
			$title = 'RE:';
			$query = "INSERT INTO posts values('null', '$postid', '".$_SESSION['admin_user']."', NOW(), '".$title."', 1, 1, '".$message."')";
			$result = $conn->query($query);
			if($result){
				header('Location: ' . $location);
			}
		} else {
			header('Location: ' . $location);
			echo '<p class="errorPosts">Please sign up or login to submit</p>';
		}
	}
	
	if(isset($_POST['reply'])){
		$location = $_POST['location'];
	if(isset($_SESSION['admin_user'])){
		$conn = db_connect();
		$message = $conn->real_escape_string($_POST['reply_message']);
		$id = $_POST['id'];
		$title = 'RE:RE:';
		$query_reply = "INSERT INTO posts values('null', '$id', '".$_SESSION['admin_user']."', NOW(), '".$title."', 1, 1, '".$message."')";
		$result_reply = $conn->query($query_reply);
		if($result_reply){
			header('Location: ' . $location);
		}
	} else {
		header('Location: ' . $location);
		echo '<p class="errorPosts">Please sign up or login to submit</p>';
	}
	}

?>
<div id="loginMobile"><img id="menuPic" src="./images/menu.png" /></div>
<div id="superWrap">
<div id="sidebar">
	<?php
	if(isset($_SESSION['admin_user'])){
?>

		
	<div id="createTopic">
		<h4 id="memberLogin">Logged in as: <?php echo $_SESSION['admin_user']; ?></h4>
			<form action="logout.php" method="post">
				<input type="submit" class="button" id="logoutButton" value="Log Out"/>
			</form>
		<div id="brown">
		</div>
	</div>
	<?php
	} else {
	?>
	<div id="login">
		<h4 id="memberLogin">Member Login:</h4>
		<?php if(!empty($errors)) echo '<span class="error">'.$errors['login'].'</span>'; ?>
		<form action="<?php echo $_SERVER['PHP_SELF'] .'?postid=' . $postid; ?>" method="post">
			<input type="hidden" name="location" value="<?php echo $location; ?>">
			<input type="text" name="username" class="loginInput" placeholder="Username" />
			<input type="password" name="password" class="loginInput" placeholder="Password" /><br/>
			<input type="submit" id="loginButton" class="signUpButton" name="login" value="Login" />
		</form>
		<div id="forgot">
			<a href="#">Forgot Password?</a><br/>
			<a href="sign_up.php">Not a Member?</a>
		</div>
	</div>
	<?php
	}
	?>
	
	<div id="adds">
		<h4 id="memberLogin">Partners:</h4>
		<img src="images/banner.png" style="padding-left: 25px; margin-top: 25px;" width=150 height=150 />
		<img src="images/animation.gif" id="animation" />
	</div>
</div>


<div id="viewWrap">

<?php echo @$login_location; ?>
<?php
	$conn = db_connect();
	$query = "SELECT * FROM posts where postid='$postid'";
	$result = $conn->query($query);
	$row = $result->fetch_array();
	if(!isset($_SESSION['admin_user'])) echo '<div id="stripeSpace"><p id="mustLogIn">&#8592;Log In or Sign Up to Submit</p></div>';
	
	echo '<h4 id="question">'.$row['title'].'</h4>';
	if(isset($postid)){
	/*UPDATE HISTORY TABLE*/
	$query_history = "SELECT postid FROM history WHERE postid='$postid'";
		$result_history = $conn->query($query_history);
		if($result_history->num_rows == 0){
			$query_new = "INSERT INTO history values('null', '$postid', 1, '".$row['title']."')";
		} else {
			$query_new = "UPDATE history SET replies=(replies+1) WHERE postid='$postid'";
		}
		$result_new = $conn->query($query_new);
	}
	
	
	$query_responses = "SELECT * FROM posts WHERE parent = $postid order by posted";
	$result_responses = $conn->query($query_responses);
	if(@$result_responses->num_rows > 0){
		
		while($row = $result_responses->fetch_assoc()){
			echo '<div class="border">';
			echo '<tr><td bgcolor= ';
			echo "'#ffffff'>";
			
			echo "<form action='view_post.php' method='post'>";
					echo '<input type="hidden" name="location" value="'.$location.'">';
			/*echo '<p class="reply_head">'.$row['title'].  ' ' . $row['poster'] . '-' . $row['posted'] .'</p>';*/
					echo '<p class="postTime">'.$row['posted'].'</p>';
					echo '<div class="posterBasic">';
					echo '<p class="poster"><img src="images/face.jpg" width="32" height="32" />'.$row['poster'].'</p>';
					echo '</div>';
					echo "<p class='replyMessage'>".$row['message'].'</p>';
					check_replies($row['postid']);
					echo '<p class="replyReply">Reply to '.$row['poster'].'&#10150;</p>';
					echo '<textarea class="replyBox" name="reply_message"></textarea><br/>';
					echo '<input type="hidden" name="postid" value="'.$postid.'">';
					echo '<input type="hidden" name="id" value="'.$row['postid'].'">';
					echo '<input class="submitButtonReply" type="submit" name="reply" value="Reply">';
					echo '</form>';
					echo '</td></tr>';
					echo '</div>';
		}
		
		
	}
	echo '<div id="replyWrap">';
	echo '<form action="view_post.php" method="post">';
	echo '<p class="replyToThread">Add To Thread</p>';
	echo '<input type="hidden" name="location" value="'.$location.'">';
	echo '<input type="hidden" name="postid" value="'.$postid.'">';
	echo '<textarea class="replyBoxThread" name="thread_update"></textarea><br/>';
	echo '<input id="loginButton" class="submitButtonThread" type="submit" name="add_thread" value="Submit Post">';
	echo '</form>';
	echo '</div>';
?>
	</div>
	<div id="bottom">
	<ul id="bottomMenu">
		<li id="copywright">&copy;Stephen Knutter</li>
		<li><a href="http://www.stephenknutter.com">Home</a></li>
		<li><a href="http://www.stephenknutter.com">About</a></li>
		<li><a href="http://www.stephenknutter.com">Contact</a></li>
	</ul>
</div>

<?php
	display_footer();
?>
</div>































