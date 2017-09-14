<!--<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="submit" name="logout" value="Logout" style="background-color: ad300d">
</form>-->
<ul class="nav navbar-nav">
<?php
echo "<li><p><font color='ad300d'>Welcome, <strong><a href='profile.php?user_prof=" . $_SESSION['user_id'] . "'>" . htmlspecialchars($_SESSION['username']) . "</a></strong></font></p></li>
<li><a href='destroy_session.php' style='font-size: 200%; font-weight: bold'>Logout</a></li>";
?>
<!--
<li>
<form class="cool">
	<input type="submit" name="logout" value="Logout" style="float:right">
</form>
</li>
-->
</ul>