<fieldset>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="username" placeholder="Username" autofocus><br />
	<input type="password" name="pwd" placeholder="Password"><br />
	<input type="submit" name="submit2" value="Login!">
</form>

<?php
echo $msg2;
?>
</fieldset>