<?php
require_once("db_connect.php");

if (!ISSET($_SESSION['level'])) {
	$_SESSION['level'] = 0;
}

//refresh
if (ISSET($_GET['refresh'])) {
	unset($_SESSION['find_content']);
	unset($_SESSION['search']);
}



//open fancybox
function openFancybox($hash) {
	echo '
	<script type="text/javascript">
		window.jQuery(document).ready(function() {
			$.fancybox.open(\'' . $hash . '\');
		});
	</script>
	';
}
//signup
if (!ISSET($_SESSION['n_hold'])) {
	$_SESSION['n_hold'] = null;
}
if (!ISSET($_SESSION['n_hold'])) {
	$_SESSION['email_hold'] = null;
}
$msg = null;
if (ISSET($_POST['submit1'])) {
	if ($_POST['username'] != null and $_POST['pwd'] != null and $_POST['confirm_pwd'] != null and $_POST['email'] != null) {
		if ($_POST['pwd'] == $_POST['confirm_pwd']) {
			$error = false;
			$select = "SELECT `username` FROM `users`";
			$result = mysqli_query($dbc, $select) or DIE("Query error!");
			while ($row = mysqli_fetch_array($result)) {
				if (strtolower(trim($_POST['username'])) == strtolower($row['username'])) {
					$error = true;
					$msg = "<font color='red'><strong>That username is unavailable! Please choose another one.</strong></font>";
					openFancybox("#signup");
					break 1;
				}
			}
			if ($error == false) {
				$query = "INSERT INTO `blog_negrazis`.`users` (`user_id`, `username`, `pwd`, `email`, `join_date`) VALUES (NULL, '" . trim(mysqli_real_escape_string($dbc, $_POST['username'])) . "', '" . md5($_POST['pwd']) . "', '" . trim(mysqli_real_escape_string($dbc, $_POST['email'])) . "', NOW())";
				//echo $query;
				mysqli_query($dbc, $query) or DIE("Dang! Query error2, bro");
				$_SESSION['user_id'] = mysqli_insert_id($dbc);
				$_SESSION['username'] = $_POST['username'];
				//thanks
				$id = $_SESSION['user_id'];
				$message = "Hey " . $_SESSION['username'] . "! Thanks for signing up with <font class='impact' color='5053aa'>MindPost</font>. Get started and post your first blog! 💡</i>";
				$insert_notif = "INSERT INTO `blog_negrazis`.`notifications` (`user_id`, `message`) VALUES (" . $id . ", '" . mysqli_real_escape_string($dbc, $message) . "')";
				mysqli_query($dbc, $insert_notif) or DIE("could not unsert notf.");
				
				
			}
		}
		else {
			$msg = "<font color='red'><strong>Passwords do not match.</strong></font>";
			openFancybox("#signup");
			$_SESSION['n_hold'] = $_POST['username'];
			$_SESSION['email_hold'] = $_POST['email'];
		}
	
	}
	else {
		$msg = "<font color='red'><strong>Please enter all fields.</strong></font>";
		openFancybox("#signup");
		$_SESSION['n_hold'] = $_POST['username'];
		$_SESSION['email_hold'] = $_POST['email'];
	}
}

//login
$msg2 = null;
if (ISSET($_POST['submit2'])) {
	if ($_POST['username'] != null and $_POST['pwd'] != null) {
		$error = true;
		$select = "SELECT * FROM `users`";
		$result = mysqli_query($dbc, $select) or DIE("Query error!");
		while ($row = mysqli_fetch_array($result)) {
			if (strtolower(trim($_POST['username'])) == strtolower($row['username'])) {
				if (md5($_POST['pwd']) == $row['pwd']) {
					if ($row['banned'] == 0) {
						$_SESSION['user_id'] = $row['user_id'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['level'] = $row['level'];
						$error = false;
					}
					else {
						$banned = true;
					}
				}
			}
		}
		if (ISSET($banned)) {
			$msg4 = "<font color='red'><strong>This account has been banned.</strong></font>";
			openFancybox("#message");
		}
		if ($error == true) {
			$msg2 = "<font color='red'><strong>Username and password did not match.</strong></font>";
			openFancybox("#login");
			$_SESSION['use_hold'] = $_POST['username'];
		}
	}
	else {
		$msg2 = "<font color='red'><strong>Please enter all fields.</strong></font>";
		openFancybox("#login");
		$_SESSION['use_hold'] = $_POST['username'];
	}
}

//logout
if (ISSET($_GET['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("Location:index.php");
}

//redirect to make a post screen if clicked
if (ISSET($_POST['topost'])) {
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['comment_title']);
	unset($_SESSION['comment_content']);
	unset($_SESSION['find_content']);
	unset($_SESSION['search']);
	header("Location:make_post.php");
}

//go back to index
if (ISSET($_POST['back'])) {
	unset($_SESSION['passy']);
	unset($_SESSION['ha']);
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['comment_title']);
	unset($_SESSION['comment_content']);
	$_SESSION['find_content'] = null;
	unset($_SESSION['search']);
	header("Location:index.php");
}

//edit pwd
if (ISSET($_GET['edit_pwd'])) {
	unset($_SESSION['ha']);
	$_SESSION['passy'] = $_SESSION['prof_id'];
	openFancybox("#message");
}

//update pwd
if (ISSET($_POST['send_pwd'])) {
	if ($_POST['pwd_input'] != null) {
		$upt = "UPDATE `users` SET `pwd`='" . md5($_POST['pwd_input']) . "' WHERE `user_id`=" . $_SESSION['passy'];
		mysqli_query($dbc, $upt) or DIE("3eibib2gg2i");
		$admin_msg = "<font color='green'><strong>Password changed.</strong></font>";
	}
	else {
		$pwderror = "<font color='red' class='impact'>No password entered!</font>";
		openFancybox("#message");
	}
}

//edit
if (ISSET($_GET['edit_bio'])) {
	$_SESSION['ha'] = true;
	unset($_SESSION['passy']);
	$select = "SELECT `bio` FROM `users` WHERE `user_id`=" . $_SESSION['user_id'];
	$get = mysqli_query($dbc, $select) or DIE("NOOOO");
	while ($row = mysqli_fetch_array($get)) {
		$bio = $row['bio'];
	}
	//header("Location:make_post.php");
	openFancybox("#message");
}

if (ISSET($_POST['post_bio'])) {
	unset($_SESSION['ha']);

	$z = "UPDATE `users` SET `bio`='" . mysqli_real_escape_string($dbc, trim($_POST['bio'])) . "' WHERE `user_id`=" . $_SESSION['user_id'];
	//echo $z;
	mysqli_query($dbc, $z) or DIE("DAMN");
}

//like blog
if (ISSET($_GET['like_blog'])) {
	$check = "SELECT `user_id` FROM `blog_likes` WHERE `blog_id`=" . $_GET['like_blog'];
	$resu = mysqli_query($dbc, $check) or DIE("could not check for blog likes");
	$indat = false;
	while ($row = mysqli_fetch_array($resu)) {
		if ($row['user_id'] == $_SESSION['user_id']) {
			$indat = true;
			break 1;
		}
	}
	if ($indat == false) {
		$insert_like = "INSERT INTO `blog_negrazis`.`blog_likes` (`blog_like_id`, `blog_id`, `user_id`) VALUES (NULL, '" . $_GET['like_blog'] . "', '" . $_SESSION['user_id'] . "')";
		mysqli_query($dbc, $insert_like) or DIE("could not like");
		//notify like recipient
		$blog_info = "SELECT `blogs`.`title`, `users`.`user_id` FROM `blogs` INNER JOIN `users` ON `blogs`.`user_id`=`users`.`user_id` WHERE `blog_id`=" . $_GET['like_blog'];
		$rat = mysqli_query($dbc, $blog_info) or DIE("could not get blog info");
		while ($row = mysqli_fetch_array($rat)) {
			$id = $row['user_id'];
			$title = $row['title'];
		}
		$message = $_SESSION['username'] . " liked your post <i>" . $title . "</i>";
		$insert_notif = "INSERT INTO `blog_negrazis`.`notifications` (`user_id`, `message`) VALUES (" . $id . ", '" . mysqli_real_escape_string($dbc, $message) . "')";
		mysqli_query($dbc, $insert_notif) or DIE("could not unsert notf.");
	}
}

//unlike blog
if (ISSET($_GET['unlike_blog'])) {
	$check = "SELECT `user_id`, `blog_like_id` FROM `blog_likes` WHERE `blog_id`=" . $_GET['unlike_blog'];
	$resu = mysqli_query($dbc, $check) or DIE("could not check for blog likes");
	$indat = false;
	while ($row = mysqli_fetch_array($resu)) {
		if ($row['user_id'] == $_SESSION['user_id']) {
			$indat = true;
			break 1;
		}
	}
	if ($indat == true) {
		$delete_like = "DELETE FROM `blog_negrazis`.`blog_likes` WHERE `blog_likes`.`blog_like_id` =" . $row['blog_like_id'];
		mysqli_query($dbc, $delete_like) or DIE("could not delete like");
	}
}

//like comment
if (ISSET($_GET['like_comment'])) {
	$check = "SELECT `user_id` FROM `comment_likes` WHERE `blog_id`=" . $_GET['like_comment'];
	$resu = mysqli_query($dbc, $check) or DIE("could not check for comment likes");
	$indat = false;
	while ($row = mysqli_fetch_array($resu)) {
		if ($row['user_id'] == $_SESSION['user_id']) {
			$indat = true;
			break 1;
		}
	}
	if ($indat == false) {
		$insert_like = "INSERT INTO `blog_negrazis`.`comment_likes` (`comment_like_id`, `comment_id`, `user_id`) VALUES (NULL, '" . $_GET['like_comment'] . "', '" . $_SESSION['user_id'] . "')";
		mysqli_query($dbc, $insert_like) or DIE("could not like");
		//notify like recipient
		
		$blog_info = "SELECT `comments`.`title`, `users`.`user_id`, `blogs`.`title` AS `blog_title` FROM `comments` INNER JOIN `users` ON `comments`.`user_id`=`users`.`user_id` INNER JOIN `blogs` ON `comments`.`blog_id`=`blogs`.`blog_id` WHERE `comments`.`comment_id`=" . $_GET['like_comment'];
		//echo $blog_info;
		$rat = mysqli_query($dbc, $blog_info) or DIE("could not get blog info");
		while ($row = mysqli_fetch_array($rat)) {
			$id = $row['user_id'];
			$title = $row['title'];
			$blog_title = $row['blog_title'];
		}
		$message = $_SESSION['username'] . " liked your comment with title <i>" . $title . "</i> in blog <i>" . $blog_title . "</i>";
		$insert_notif = "INSERT INTO `blog_negrazis`.`notifications` (`user_id`, `message`) VALUES (" . $id . ", '" . mysqli_real_escape_string($dbc, $message) . "')";
		mysqli_query($dbc, $insert_notif) or DIE("could not unsert notf.");
	}
}

//unlike comment
if (ISSET($_GET['unlike_comment'])) {
	$check = "SELECT `user_id`, `comment_like_id` FROM `comment_likes` WHERE `comment_id`=" . $_GET['unlike_comment'];
	//echo $check;
	$resu = mysqli_query($dbc, $check) or DIE("could not check for blog likes");
	$indat = false;
	while ($row = mysqli_fetch_array($resu)) {
		if ($row['user_id'] == $_SESSION['user_id']) {
			$indat = true;
			$id = $row['comment_like_id'];
			//break 1;
		}
	}
	if ($indat == true) {
		$delete_like = "DELETE FROM `blog_negrazis`.`comment_likes` WHERE `comment_likes`.`comment_like_id` =" . $id;
		//echo $delete_like;
		mysqli_query($dbc, $delete_like) or DIE("could not delete like");
	}
}

//check blog likes
if (ISSET($_GET['check_blog_likes'])) {
	$bloggy = $_GET['check_blog_likes'];
	openFancybox("#message");
}
//check blog likes
if (ISSET($_GET['check_comment_likes'])) {
	$commy = $_GET['check_comment_likes'];
	openFancybox("#message");
}

//add category when making post
if (ISSET($_POST['add_cat'])) {
	$e = 0;
	foreach ($_POST['category'] as $cat) {
		if ($cat == "blank") $e++;
	}
	$_SESSION['numb_categories'] = $e;
	$_SESSION['numb_categories'] = $_SESSION['numb_categories'] + 1;
	$_SESSION['content'] = $_POST['editor1'];
	$_SESSION['title'] = $_POST['title'];
	$_SESSION['hold_cats'] = $_POST['category'];
	$_SESSION['hold_cats'] = array_unique($_SESSION['hold_cats']);
	$_SESSION['hold_cats'] = implode(",", $_SESSION['hold_cats']);
	if (($key = array_search("blank", $_POST['category'])) !== false) {
		unset($_POST['category'][$key]);
	}
}

//edit
if (ISSET($_GET['edit_blog'])) {
	$_SESSION['edit_blog'] = $_GET['edit_blog'];
	$select = "SELECT `title`, `content`, `category_id` FROM `blogs` WHERE `blog_id`=" . $_SESSION['edit_blog'];
	$get = mysqli_query($dbc, $select) or DIE("NOOOO");
	while ($row = mysqli_fetch_array($get)) {
		$_SESSION['title'] = $row['title'];
		$_SESSION['content'] = $row['content'];
		$_SESSION['hold_cats'] = $row['category_id'];
		$_SESSION['numb_categories'] = 0;
	}
	header("Location:make_post.php");
}

//make posts
if (ISSET($_POST['post'])) {
	$_SESSION['numb_categories'] = 1;
	if (trim($_POST['title']) != null and trim($_POST['editor1']) != null) {
		if (strpos($_POST['title'], ",") !== false) {
			$error = true;
		}
		$_POST['title'] = trim($_POST['title']);
		$_POST['editor1'] = trim($_POST['editor1']);
		if (strlen($_POST['editor1']) > 20000) {
			$error = true;
			$post_msg = "<font color='red'><strong>Your post is too long. It is " . strlen($_POST['editor1']) . " characters long, but the max length for a post is 20000 characters.</strong></font>";
			$_SESSION['content'] = $_POST['editor1'];
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['hold_cats'] = $_POST['category'];
			$_SESSION['hold_cats'] = array_unique($_SESSION['hold_cats']);
			$_SESSION['hold_cats'] = implode(",", $_SESSION['hold_cats']);
			if (($key = array_search("blank", $_POST['category'])) !== false) {
				unset($_POST['category'][$key]);
			}
		}
		if (!ISSET($error)) {
			foreach ($_POST['category'] as $key => $val) {
				if (!is_numeric($val)) {
					unset($_POST['category'][$key]);
				}
			}
			//make sure category was selected
			if (!empty($_POST['category'])) {
				$_POST['category'] = array_unique($_POST['category']);
				if (($key = array_search("blank", $_POST['category'])) !== false) {
					unset($_POST['category'][$key]);
				}
				$_POST['category'] = implode(",", $_POST['category']);
				if ($_SESSION['edit_blog'] == -1) {
					//remove duplicate categories, unset any blank categories
					
					$insert = "INSERT INTO `blog_negrazis`.`blogs` (`user_id`, `category_id`, `title`, `content`, `timestamp`) VALUES ('" . $_SESSION['user_id'] . "', '" . mysqli_real_escape_string($dbc, $_POST['category']) . "', '" . mysqli_real_escape_string($dbc, $_POST['title']) . "', '" . $_POST['editor1'] . "', NOW())";
					$result = mysqli_query($dbc, $insert) or DIE("COuld not insert blog into database.");
					//unset withstanding title and content
					if ($_SESSION['title'] != null) {
						unset($_SESSION['title']);
					}
					if ($_SESSION['content'] != null) {
						unset($_SESSION['content']);
					}
					if ($_SESSION['hold_cats'] != null) {
						unset($_SESSION['hold_cats']);
					}
					$_SESSION['blog_id'] = mysqli_insert_id($dbc);
				}
				else {
					$update = "UPDATE `blogs` SET `title`='" . $_POST['title'] . "', `content`='" . $_POST['editor1'] . "', `category_id`='" . $_POST['category'] . "', `edited`=NOW() WHERE `blog_id`=" . $_SESSION['edit_blog'];
					mysqli_query($dbc, $update) or DIE("no update");
					$_SESSION['blog_id'] = $_SESSION['edit_blog'];
				}
				$post_msg = "<font color='green'><strong>Your blog was posted!</strong></font>";
				$_SESSION['numb_categories'] = 3;
				header("Location:view_post.php");
			}
			else {
				$post_msg = "<font color='red'><strong>You need to select at least one category.</strong></font>";
				$_SESSION['content'] = $_POST['editor1'];
				$_SESSION['title'] = $_POST['title'];
				$_SESSION['hold_cats'] = $_POST['category'];
				$_SESSION['hold_cats'] = array_unique($_SESSION['hold_cats']);
				$_SESSION['hold_cats'] = implode(",", $_SESSION['hold_cats']);
				if (($key = array_search("blank", $_POST['category'])) !== false) {
					unset($_POST['category'][$key]);
				}
			}
		}
		else {
			$post_msg = "<font color='red'><strong>The title cannot contain a comma.</strong></font>";
		}
	}
	else {
		//save what user wrote in case of error
		$_SESSION['content'] = $_POST['editor1'];
		$_SESSION['title'] = $_POST['title'];
		$_SESSION['hold_cats'] = $_POST['category'];
		$_SESSION['hold_cats'] = array_unique($_SESSION['hold_cats']);
		$_SESSION['hold_cats'] = implode(",", $_SESSION['hold_cats']);
		if (($key = array_search("blank", $_POST['category'])) !== false) {
			unset($_POST['category'][$key]);
		}
		$post_msg = "<font color='red'><strong>Oops, it looks like you didn't input all information.</strong></font>";
	}
}

//make comment
$comment_msg = null;
if (ISSET($_POST['post_comment'])) {
	if ($_POST['my_comment'] != null) {
		if (strlen($_POST['my_comment']) > 500) {
			$error = true;
			$comment_msg = "<font color='red'><strong>Your comment is too long. It is " . strlen($_POST['my_comment']) . " characters long, but the max length for a comment is 500 characters.</strong></font>";
			$_SESSION['comment_content'] = $_POST['my_comment'];
			$_SESSION['comment_title'] = $_POST['comment_title'];
		}
		if (!ISSET($error)) {
			$check = "SELECT `blogs`.`blog_id`, `blogs`.`user_id`, `blogs`.`title`, `users`.`username` FROM `blogs` INNER JOIN `users` ON `blogs`.`user_id`=`users`.`user_id` WHERE `blog_id`=" . $id_blog;
			$resu = mysqli_query($dbc, $check) or DIE("could not check for blog likes");
			$indat = false;
			while ($row = mysqli_fetch_array($resu)) {
				if ($row['user_id'] == $_SESSION['user_id']) {
					$indat = true;
				}
				else {
					$user_id = $row['user_id'];
					$title = $row['title'];
				}
			}
			if ($indat == false) {
				$message = $_SESSION['username'] . " commented on your post <i>" . $title . "</i>";
				$insert_notif = "INSERT INTO `blog_negrazis`.`notifications` (`user_id`, `message`) VALUES (" . $user_id . ", '" . mysqli_real_escape_string($dbc, $message) . "')";
				mysqli_query($dbc, $insert_notif) or DIE("could not unsert notf.");
			}
			$insert = "INSERT INTO `blog_negrazis`.`comments` (`blog_id`, `user_id`, `title`, `comment`, `timestamp`) VALUES ('" . $id_blog . "', '" . $_SESSION['user_id'] . "', '" . mysqli_real_escape_string($dbc, $_POST['comment_title']) . "', '" . mysqli_real_escape_string($dbc, $_POST['my_comment']) . "', NOW())";
			mysqli_query($dbc, $insert) or DIE("COuld not comment into database.");
			//unset withstanding title and content
			unset($_SESSION['comment_title']);
			unset($_SESSION['comment_content']);
			$comment_msg = "<font color='green'><strong>Your comment was posted!</strong></font>";
		}
	}
	else {
		//save what user wrote in case of error
		$_SESSION['comment_content'] = $_POST['my_comment'];
		$_SESSION['comment_title'] = $_POST['comment_title'];
		$comment_msg = "<font color='red'><strong>Your comment was empty.</strong></font>";
	}
}

//set withstanding input data in make a post screen (in case of error or something)
if (!ISSET($_SESSION['title'])) {
	$_SESSION['title'] = null;
}
if (!ISSET($_SESSION['content'])) {
	$_SESSION['content'] = null;
}
if (!ISSET($_SESSION['comment_title'])) {
	$_SESSION['comment_title'] = null;
}
if (!ISSET($_SESSION['comment_content'])) {
	$_SESSION['comment_content'] = null;
}

if (!ISSET($_SESSION['category_id'])) {
	$_SESSION['category_id'] = 1;
}

//nav bar category choice
if (ISSET($_GET['def_category'])) {
	$_SESSION['category_id'] = $_GET['def_category'];
	if (ISSET($id_blog)) {
		unset($_SESSION['find_content']);
		unset($_SESSION['search']);
		header("Location:index.php");
	}
}

//search blog posts builder

//category search
if (ISSET($_GET['category_search'])) {
	if (trim($_GET['search']) != null) {
		$_GET['search'] = trim($_GET['search']);
		$search = mysqli_real_escape_string($dbc, $_GET['search']);
		$find_category = "SELECT `category_id`, `category_name` FROM `blog_negrazis`.`categories` WHERE `category_name` LIKE '%" . $search . "%' AND `hide`=0";
		$cats_result = mysqli_query($dbc, $find_category) or DIE("Could not find category.");
		if (mysqli_num_rows($cats_result) == 1) { //set view to searched category when 1 result matched
			while ($row = mysqli_fetch_array($cats_result)) {
				$_SESSION['category_id'] = $row['category_id'];
			}
		}
		elseif (mysqli_num_rows($cats_result) > 1) { //ask which category user wants to view containing search with multiple matches
			openFancybox("#message");
			$loop = true;
		}
		else {
			$blog_msg = "<font color='red'><strong>Could not find any categories names containing</strong></font><i> " . $_GET['search'] . "</i><br /><br />Why not try searching for something else?";
			openFancybox("#message");
		}
	}
}

//if viewing category that's not all, only show posts from that category using category id

if (!ISSET($_SESSION['find_content'])) {
	$_SESSION['find_content'] = null;
}

//content search
if (ISSET($_GET['content_search'])) {
	if (trim($_GET['search2']) != null) {
		$_GET['search2'] = trim($_GET['search2']);
		$search = trim(mysqli_real_escape_string($dbc, $_GET['search2']));
		$_SESSION['search'] = $search;
		//append onto where clause
		$_SESSION['find_content'] = "WHERE (`content` LIKE '%" . $search . "%' OR `blogs`.`title` LIKE '%" . $search . "%' OR `users`.`username` LIKE '%" . $search . "%')";
	}
}

//override where if viewing single blog



//$where = $where . " AND `categories`.`category_id`=" . $_SESSION['category_id'];

$orderby = "ORDER BY `blogs`.`blog_id` ";
//order by
if (ISSET($_GET['age'])) {
	$order = $_GET['age'];
}
else {
	$order = "DESC";
}

if ($_SESSION['find_content'] == null) {
	if ($_SESSION['category_id'] == 1) {
		$catz = " WHERE `blogs`.`hide`=0 AND `categories`.`hide`=0";
	}
	elseif (ISSET($id_blog)) {
		$catz = " AND (`blogs`.`category_id`=" . $_SESSION['category_id'] . " AND `blogs`.`hide`=0 AND `categories`.`hide`=0)";
	}
	else {
		$catz = "WHERE `blogs`.`category_id`=" . $_SESSION['category_id'] . " AND `blogs`.`hide`=0 AND `categories`.`hide`=0";
	}
}
else {
	if ($_SESSION['category_id'] == 1) {
		$catz = " AND (`blogs`.`hide`=0 AND `categories`.`hide`=0)";
	}
	else {
		$catz = " AND (`blogs`.`category_id`=" . $_SESSION['category_id'] . " AND `blogs`.`hide`=0 AND `categories`.`hide`=0)";
	}
}

if (ISSET($id_blog)) {
	$willy = " WHERE `blogs`.`blog_id`=" . $id_blog . " AND `blogs`.`hide`=0 AND `categories`.`hide`=0";
	$catz = null;
}
else {
	$willy = $_SESSION['find_content'];
}

//view user's posts
if (ISSET($view) and is_numeric($view)) {
	$catz = $catz . " AND `blogs`.`user_id`=" . $view;
}


$search_blogs = "SELECT `blogs`.`category_id` AS `blog_cats`, `blogs`.`blog_id`, `blogs`.`title`, `blogs`.`content`, `blogs`.`timestamp`, `blogs`.`edited`, `blogs`.`hide`, `users`.`user_id`, `users`.`username`, `users`.`banned`, GROUP_CONCAT(`categories`.`category_name` ORDER BY `category_name` ASC) AS `category_name`, GROUP_CONCAT(`categories`.`category_id` ORDER BY `category_name` ASC) AS `category_id`, GROUP_CONCAT(`categories`.`hide` ORDER BY `category_name` ASC) AS `categories_hide`, GROUP_CONCAT(`blog_likes`.`user_id` ORDER BY `blog_like_id` DESC) AS `likes`, GROUP_CONCAT(`comments`.`comment_id`) AS `comment_id` FROM `blog_negrazis`.`blogs` LEFT JOIN `blog_likes` ON `blogs`.`blog_id`=`blog_likes`.`blog_id` LEFT JOIN `comments` ON `blogs`.`blog_id`=`comments`.`blog_id` INNER JOIN `blog_negrazis`.`users` ON `blogs`.`user_id`=`users`.`user_id` INNER JOIN `categories` ON `blogs`.`category_id` LIKE CONCAT('%', `categories`.`category_id`, '%') " . $willy . $catz . " GROUP BY `blogs`.`blog_id` ORDER BY `blogs`.`blog_id` " . $order;
//echo $search_blogs;
$search_result = mysqli_query($dbc, $search_blogs) or DIE("Could not get blogs.");
//echo $search_blogs;
//expand extra categories
if (ISSET($_GET['expand_cats'])) {
	$cats = $_GET['expand_cats'];
	$cats = "SELECT `category_id`,`category_name` FROM `categories` WHERE '{" . $cats . "}' LIKE CONCAT ('%', `category_id`, '%')";
	$cats_result = mysqli_query($dbc, $cats) or DIE("could not fetch cats!");
	openFancybox("#message");
}

//get category info
$select_info = "SELECT `category_name`, `category_desc`, `hide` FROM `categories` WHERE `hide`=0 AND `category_id`=" . $_SESSION['category_id'];
$info_result = mysqli_query($dbc, $select_info) or DIE("Could not get category info.");
if (mysqli_num_rows($info_result) != 0) {
	while ($row = mysqli_fetch_array($info_result)) {
		$category_name = $row['category_name'];
		$category_desc = $row['category_desc'];
	}
}
else {
	openFancybox("#message");
	$_SESSION['category_id'] = 1;
	$no_cat = true;
	$select_info = "SELECT `category_name`, `category_desc` FROM `categories` WHERE `category_id`=1";
	$info_result = mysqli_query($dbc, $select_info) or DIE("Could not get category info.");
	if (mysqli_num_rows($info_result) != 0) {
		while ($row = mysqli_fetch_array($info_result)) {
			$category_name = $row['category_name'];
			$category_desc = $row['category_desc'];
		}
	}
}

//get 8 random categories for user to check out
$rand_select = "SELECT `category_id`, `category_name` FROM `categories` WHERE `hide`=0 ORDER BY RAND() LIMIT 8";
$rand_result = mysqli_query($dbc, $rand_select) or DIE("could not get random categories");

//get first blog categories (i.e. suggested ones)
$categories = "SELECT `category_id`, `category_name` FROM `categories` WHERE `hide`=0 ORDER BY `category_id` ASC LIMIT 8";
$eight_categories = mysqli_query($dbc, $categories) or DIE("could not get default categories");

//open blog
if (ISSET($_GET['open_blog'])) {
	$_SESSION['blog_id'] = $_GET['open_blog'];
	header("Location:view_post.php");
}

//unset hold info


//redirect to make a post screen if clicked
if (ISSET($_POST['topost'])) {
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['comment_title']);
	unset($_SESSION['comment_content']);
	header("Location:make_post.php");
}

//get notifs
if (ISSET($_SESSION['user_id'])) {
	$get = "SELECT `notif_id`, `user_id`, GROUP_CONCAT(`message` ORDER BY `notif_id` DESC) AS `message`, GROUP_CONCAT(`seen` ORDER BY `notif_id` DESC) AS `seen` FROM `notifications` WHERE `user_id`=" . $_SESSION['user_id'];
	//echo $get;
	$que = mysqli_query($dbc, $get) or DIE("could not do notif get query");
	while ($row = mysqli_fetch_array($que)) {
		$my_notif_messages = explode(",", $row['message']);
		$new_notifs = 0;
		$seenit = explode(",", $row['seen']);
		$saw = explode(",", $row['seen']);
	}
	foreach ($seenit as $key => $seen) {
		if ($seen != 0 or $seen == null) {
			unset($seenit[$key]);
		}
	}
	$new_notifs = count($seenit);
	if ($new_notifs > 0) {
		$extra = "<font color='red'>" . $new_notifs . " NEW</font>";
	}
	else {
		$extra = null;
	}
	$n_msg = "Notifcations " . $extra;
}

if (ISSET($_GET['check_notifs'])) {
	$n_msg = "Notifications";
	openFancybox("#message");
	$upd = "UPDATE `notifications` SET `seen`=1 WHERE `user_id`=" . $_SESSION['user_id'];
	mysqli_query($dbc, $upd) or DIE("died");
}

?>