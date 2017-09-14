<fieldset>
<?php

if (ISSET($_POST['email']) and $_POST['email'] != null) {
	$_SESSION['email_hold'] = $_POST['email'];
}
else {
	$_SESSION['email_hold'] = null;
}
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="username" placeholder="Username" autofocus class="cool4" maxlength="33" value="<?php echo $_SESSION['n_hold']; ?>"><br />
	<input type="email" name="email" placeholder="Email" class="cool4" maxlength="33" value="<?php echo $_SESSION['email_hold']; ?>"><br />
	<input type="password" name="pwd" placeholder="Password" class="cool4" maxlength="50"><br />
	<input type="password" name="confirm_pwd" placeholder="Confirm password" class="cool4" maxlength="33"><br />
	<input type="submit" name="submit1" value="Sign up!" class="cool2">
</form>

<?php
echo $msg;
?>

</fieldset>