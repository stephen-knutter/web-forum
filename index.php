<?php
	session_start();
	require('forum_fns.inc.php');
	require('db_connect.php');
	display_header('The Forum');
?>
<?php
	if(isset($_POST['login'])){
		$errors = array();
		$username = $_POST['username'];
		$password = $_POST['password'];
		if(!empty($username) && !empty($password)){
			$conn = db_connect();
			$query = "SELECT username, password FROM users WHERE username='".$username."' AND password=sha1('".($password)."')";
			$result = $conn->query($query);
			if($result->num_rows == 1){
				$_SESSION['admin_user'] = $username;
			} else {
				$errors['login'] = 'Username and Password could not be found';
			}
		} else {
			$errors['login'] = 'Username and Password could not be found';
		}
	}
	
	if(isset($_POST['new_post'])){
		$new = $_POST['new'];
		$conn = db_connect();
		if(strlen($new) > 2 && strlen($new) < 40){
		$query = "INSERT INTO posts values('null', 0, '".$_SESSION['admin_user']."', NOW(), '".$new."', 
		1, 1, 'null')";
		$result = $conn->query($query);
		
		$new_id = $conn->insert_id;
		
		$query_new = "INSERT INTO history values('null', '$new_id', 0, '".$new."')";
		$conn->query($query_new);
	  } else {
		$error = 'New Topic must be between 2 and 40 characters';
	  }
	}
?>
<div id="loginMobile"><img id="menuPic" src="./images/menu.png" /></div>
<div id="superWrap">
<div id="wrap">
	<div id="topHead">
		<ul id="headMenu">
			<li>Welcome to Web Talk, a Forum for Designers and Developers</li>
			<li>
				<input type="text" name="search" class="searchBox" id="txtState" placeholder="search" autocomplete="off" onclick="start();"/>
				<div id="scroll">
					<div id="suggest"></div>
				</div>
			</li>
		</ul>
	</div>
	
	<div class="topics">
		<div id="topicsHead"><p>Topics</p></div>
		<table class="topicList" cellspacing=0 cellpadding=5 style="width: 100%; font-size: 11px;">
			<?php
				$conn = db_connect();
				$query = "SELECT * FROM posts where parent=0";
				$result = $conn->query($query);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						echo "<tr>";
						echo "<td><a href='view_post.php?postid=".$row['postid']."'>" . $row['title'] . "</a></td>";
						get_views($row['postid']);
						get_count($row['postid']);
						get_topic($row['postid']);
						echo "</tr>";
					}
				}
			?>
		</table>
	</div>
	
	<div class="main_box">
		<div id="topPosts"><p>Top Posts</p></div>
			<table class="topicList" cellspacing=0 cellpadding=5 style="width: 100%; font-size: 11px;">
				<?php
					$conn = db_connect();
				$query = "SELECT * FROM history ORDER BY replies DESC";
				$result = $conn->query($query);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						echo "<tr>";
						echo "<td><a href='view_post.php?postid=".$row['postid']."'>" . $row['title'] . "</a></td>";
						get_views($row['postid']);
						get_count($row['postid']);
						get_topic($row['postid']);
						echo "</tr>";
					}
				}
				?>
			</table>
	</div>
</div>
<div id="bottom">
	<ul id="bottomMenu">
		<li id="copywright">&copy;Stephen Knutter</li>
		<li><a href="http://www.stephenknutter.com">Home</a></li>
		<li><a href="http://www.stephenknutter.com">About</a></li>
		<li><a href="http://www.stephenknutter.com">Contact</a></li>
	</ul>
</div>
<!-- SIDEBAR & FOOTER -->

<div id="sidebar">
	<?php
	if(isset($_SESSION['admin_user'])){
?>

		
	<div id="createTopic">
		<h4 id="memberLogin">Logged in as: <?php echo $_SESSION['admin_user']; ?></h4>
		<div id="searchWrap">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<input type="text" id="enter_topic" name="new" class="<?php if (@$error) echo 'error'; ?>" placeholder="Enter a New Topic" value="<?php if(@$error) echo $error; ?>"/>
				<input type="submit" class="button" id="loginButton" name="new_post" value="Add" />
			</form>
		</div>
			<form action="logout.php" method="post">
				<input type="submit" class="button" id="logoutButtonHome" value="Log Out"/>
			</form>
		<div id="brownLogout">
		</div>
	</div>
	<?php
	} else {
	?>
	<div id="login">
		<h4 id="memberLogin">Member Login:</h4>
		<?php if(!empty($errors)) echo '<span class="error">'.$errors['login'].'</span>'; ?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
		<img src="./images/banner.png" style="padding-left: 25px; margin-top: 25px;" width=150 height=150 />
		<img src="./images/animation.gif" id="animation" />
	</div>
</div>
</div>

<?php
	display_footer();
?>