<fieldset>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="username" placeholder="Username" autofocus><br />
	<input type="password" name="pwd" placeholder="Password"><br />
	<input type="password" name="confirm_pwd" placeholder="Confirm password"><br />
	<input type="submit" name="submit1" value="Sign up!">
</form>

<?php
echo $msg;
?>

</fieldset>