<?php
	session_start();
	require('forum_fns.inc.php');
	require('db_connect.php');
	display_header('The Forum');
?>

<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$username = $_POST['username'];
		$pass1 = $_POST['pass1'];
		$pass2 = $_POST['pass2'];
		
		if(!empty($username) && !empty($pass1) && !empty($pass2)){
			$conn = db_connect();
			$query = "SELECT username, password FROM users WHERE username='".$username."'";
			$result = $conn->query($query);
			if($result->num_rows > 0){
				$errors['username'] = '<span class="error">Username already exists</span>';
			}
			
			if(!(strlen($username) > 6) && !(strlen($username) < 16)){
				$errors['username'] = '<span class="error">Username must be between 6 & 16 characters</span>';	
			}
			
			if(!(strlen($pass1) > 6) && !(strlen($pass1) < 16)){
				$errors['pass'] = '<span class="error">Username must be between 6 & 16 characters</span>';
			}
			
			if($pass1 !== $pass2){
				$errors['pass'] = '<span class="error">Passwords do not match</span>';
			}
		} else {
			$errors['both'] = '<span class="error">Please fill out form completely</span>';
		}
		
		if(empty($errors)){
			$conn = db_connect();
			$query = "INSERT INTO users values ('null', '".$username."', sha1('".$pass1."'))";
			if($result = $conn->query($query)){
				$_SESSION['admin_user'] = $username;
				header('Location: index.php');
			} else {
				$errors['both'] = '<span class="error">Internal Error: Please try again</span>';
			}
		}
	}
?>

<div id="signUp">
		<h4 id="memberLogin">Sign Up:</h4>
		<?php if (isset($errors['both'])) echo $errors['both']; ?>
		<?php if (isset($errors['username'])) echo $errors['username']; ?>
		<?php if (isset($errors['pass'])) echo $errors['pass']; ?>
	<div id="signUpForm">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" value="<?php if(isset($errors['username'])) echo $username; ?>" /><br/>
			<label for="pass1">Password:</label>
			<input type="password" name="pass1" id="pass1" /><br/>
			<label for="pass2">Verify Password:</label>
			<input type="password" name="pass2" id="pass2" /><br/>
			<input type="submit" name="sign-up" id="loginButton" value="Sign Up" />
		</form>
	</div>
</div>

<?php
	display_footer();
?>