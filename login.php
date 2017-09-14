<?php
if (ISSET($_POST['username']) and $_POST['username'] != null) {
	$_SESSION['use_hold'] = $_POST['username'];
}
else {
	$_SESSION['use_hold'] = null;
}
?>
<fieldset>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="username" placeholder="Username" autofocus class="cool4" maxlength="33" value="<?php echo $_SESSION['use_hold']; ?>"><br />
	<input type="password" name="pwd" placeholder="Password" class="cool4" maxlength="33"><br />
	<input type="submit" name="submit2" value="Login!" class="cool2">
</form>

<?php
echo $msg2;
?>
</fieldset>