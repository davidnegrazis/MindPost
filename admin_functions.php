<?php
require_once("db_connect.php");
$admin_msg = null;

//html open tags cleanup courtesy of stackoverflow 
function closeDanglingTags($html) {
    if (strpos($html, '<') || strpos($html, '>')) {
        // There are definitiley HTML tags
        $wrapped = false;
        if (strpos(trim($html), '<') !== 0) {
            // The HTML starts with a text node. Wrap it in an element with an id to prevent the software wrapping it with a <p>
            //  that we know nothing about and cannot safely retrieve
            $html = cHE::getDivHtml($html, null, 'closedanglingtagswrapper');
            $wrapped = true;
        }
        $doc = new DOMDocument();
        $doc->encoding = 'utf-8';
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        if ($doc->firstChild) {
            // Test whether the firstchild is definitely a DOMDocumentType
            if ($doc->firstChild instanceof DOMDocumentType) {
                // Remove the added doctype
                $doc->removeChild($doc->firstChild);
            }
        }
        if ($wrapped) {
            // The contents originally started with a text node and was wrapped in a div#plasmappclibtextwrap. Take the contents
            //  out of that div
            $node = $doc->getElementById('closedanglingtagswrapper');
            $children = $node->childNodes;  // The contents of the div. Equivalent to $('selector').children()
            $doc = new DOMDocument();   // Create a new document to add the contents to, equiv. to "var doc = $('<html></html>');"
            foreach ($children as $childnode) {
                $doc->appendChild($doc->importNode($childnode, true)); // E.g. doc.append()
            }
        }
        // Remove the added html,body tags
        return trim(str_replace(array('<html><body>', '</body></html>'), '', html_entity_decode($doc->saveHTML())));
    } else {
        return $html;
    }
}

//unpublish
if (ISSET($_GET['toggle'])) {
	if (ISSET($_GET['select'])) {
		if ($_SESSION['admin_category'] == "posts") {
			foreach ($_GET['select'] as $id) {
				if ($id != null) {
					$get_status = "SELECT `hide` FROM `blogs` WHERE `blog_id`=" . $id;
					$res = mysqli_query($dbc, $get_status) or DIE("faield to get status");
					while ($row = mysqli_fetch_array($res)) {
						$status = $row['hide'];
					}
					if ($status == 0) {
						$hide = 1;
					}
					else {
						$hide = 0;
					}
					$update = "UPDATE `blogs` SET `hide`=" . $hide . " WHERE `blog_id`=" . $id;
					mysqli_query($dbc, $update) or DIE("coudl nt update status");
				}
			}
			unset($_GET);
		}
		elseif ($_SESSION['admin_category'] == "categories") {
			foreach ($_GET['select'] as $id) {
				if ($id != null) {
					$get_status = "SELECT `hide` FROM `categories` WHERE `category_id`=" . $id;
					//echo $get_status;
					$res = mysqli_query($dbc, $get_status) or DIE("faield to get status");
					while ($row = mysqli_fetch_array($res)) {
						$status = $row['hide'];
					}
					if ($status == 0) {
						$hide = 1;
					}
					else {
						$hide = 0;
					}
					$update = "UPDATE `categories` SET `hide`=" . $hide . " WHERE `category_id`=" . $id;
					mysqli_query($dbc, $update) or DIE("coudl nt update status");
				}
			}
			unset($_GET);
		}
		elseif ($_SESSION['admin_category'] == "comments") {
			foreach ($_GET['select'] as $id) {
				if ($id != null) {
					$get_status = "SELECT `hide` FROM `comments` WHERE `comment_id`=" . $id;
					//echo $get_status;
					$res = mysqli_query($dbc, $get_status) or DIE("faield to get status");
					while ($row = mysqli_fetch_array($res)) {
						$status = $row['hide'];
					}
					if ($status == 0) {
						$hide = 1;
					}
					else {
						$hide = 0;
					}
					$update = "UPDATE `comments` SET `hide`=" . $hide . " WHERE `comment_id`=" . $id;
					//echo $update;
					mysqli_query($dbc, $update) or DIE("coudl nt update status");
				}
			}
			unset($_GET);
		}
		elseif ($_SESSION['admin_category'] == "users") {
			foreach ($_GET['select'] as $id) {
				if ($id != null) {
					$get_status = "SELECT `banned` FROM `users` WHERE `user_id`=" . $id;
					//echo $get_status;
					$res = mysqli_query($dbc, $get_status) or DIE("faield to get status");
					while ($row = mysqli_fetch_array($res)) {
						$status = $row['banned'];
					}
					if ($status == 0) {
						$hide = 1;
					}
					else {
						$hide = 0;
					}
					$update = "UPDATE `users` SET `banned`=" . $hide . " WHERE `user_id`=" . $id;
					//echo $update;
					mysqli_query($dbc, $update) or DIE("coudl nt update status");
				}
			}
			unset($_GET);
		}
	}
	else {
		$admin_msg = "<font color='red' class='impact'>No update action taken.</font>";
	}
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

//set admin's management section
if (ISSET($_GET['admin_category'])) {
	$_SESSION['admin_category'] = $_GET['admin_category'];
	$_GET['refresh'] = "lol";
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['hold_cats']);
	$_SESSION['edit_blog'] = -1;
	unset($_SESSION['writing']);
	unset($_SESSION['where']);
}

if (ISSET($_GET['refresh'])) {
	$_SESSION['where'] = null;
	$_GET['def_category'] = 1;
	unset($_SESSION['searchfor']);
}

//logout
if (ISSET($_GET['logout'])) {
	unset($_SESSION);
	session_destroy();
	header("Location:index.php");
}

//redirect to make a post screen if clicked
if (ISSET($_GET['topost'])) {
	//header("Location:make_post.php");
	$_SESSION['writing'] = true;
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['hold_cats']);
	unset($_SESSION['blogs_id']);
	
	openFancybox("#message");
}
else {
	//unset($_SESSION['writing']);
}

//go back to index
if (ISSET($_POST['back'])) {
	unset($_SESSION['title']);
	unset($_SESSION['content']);
	unset($_SESSION['comment_title']);
	unset($_SESSION['comment_content']);
	unset($_SESSION['blogs_id']);
	$_SESSION['where'] = null;
	$_GET['def_category'] = 1;
	
	header("Location:index.php");
	exit();
}

//add category when making post
if (ISSET($_POST['add_cat'])) {
	openFancybox("#message");
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
	unset($_SESSION['writing']);
	unset($_SESSION['blogs_id']);
	$_SESSION['edit_blog'] = $_GET['edit_blog'];
	$select = "SELECT `title`, `content`, `category_id` FROM `blogs` WHERE `blog_id`=" . $_SESSION['edit_blog'];
	$get = mysqli_query($dbc, $select) or DIE("NOOOO");
	while ($row = mysqli_fetch_array($get)) {
		$_SESSION['title'] = $row['title'];
		$_SESSION['content'] = $row['content'];
		$_SESSION['hold_cats'] = $row['category_id'];
		$_SESSION['numb_categories'] = 1;
	}
	//header("Location:make_post.php");
	openFancybox("#message");
	$edit = true;
}

//make posts
if (ISSET($_POST['post'])) {
	$_SESSION['numb_categories'] = 1;
	if (trim($_POST['title']) != null and trim($_POST['editor1']) != null) {
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
					
					$insert = "INSERT INTO `blog_negrazis`.`blogs` (`user_id`, `category_id`, `title`, `content`, `timestamp`) VALUES ('" . $_SESSION['user_id'] . "', '" . $_POST['category'] . "', '" . $_POST['title'] . "', '" . $_POST['editor1'] . "', NOW())";
					$result = mysqli_query($dbc, $insert) or DIE("COuld not insert blog into database.");
					$admin_msg = "<font color='green'><strong>Blog posted.</strong></font>";
					//unset withstanding title and content
					if ($_SESSION['title'] != null) {
						unset($_SESSION['title']);
					}
					if ($_SESSION['content'] != null) {
						unset($_SESSION['content']);
					}
					if (ISSET($_SESSION['hold_cats']) and $_SESSION['hold_cats'] != null) {
						unset($_SESSION['hold_cats']);
					}
					unset($_SESSION['writing']);
					$_SESSION['blog_id'] = mysqli_insert_id($dbc);
				}
				else {
					$update = "UPDATE `blogs` SET `title`='" . $_POST['title'] . "', `content`='" . $_POST['editor1'] . "', `category_id`='" . $_POST['category'] . "', `edited`=NOW() WHERE `blog_id`=" . $_SESSION['edit_blog'];
					mysqli_query($dbc, $update) or DIE("no update");
					$admin_msg = "<font color='green'><strong>Blog updated.</strong></font>";
					unset($_SESSION['title']);
					unset($_SESSION['content']);
					unset($_SESSION['hold_cats']);
				}
				$_SESSION['numb_categories'] = 3;
			}
			else {
				openFancybox("#message");
				$edit = true;
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
	}
	else {
		openFancybox("#message");
		$edit = true;
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

if ($_SESSION['admin_category'] == "posts") {
	if (ISSET($_GET['add_comment'])) {
		$_SESSION['blogs_id'] = $_GET['add_comment'];
		unset($_SESSION['writing']);
		$_SESSION['edit_blog'] = -1;
		openFancybox("#message");
	}

	//make comment
	$comment_msg = null;
	if (ISSET($_POST['post_comment'])) {
		if (ISSET($_SESSION['blogs_id'])) {
			$ids_blog = $_SESSION['blogs_id'];
		}
		if ($_POST['my_comment'] != null) {
			if (strlen($_POST['my_comment']) > 500) {
				$error = true;
				$comment_msg = "<font color='red'><strong>Your comment is too long. It is " . strlen($_POST['my_comment']) . " characters long, but the max length for a comment is 500 characters.</strong></font>";
				openFancybox("#message");
				$_SESSION['comment_content'] = $_POST['my_comment'];
				$_SESSION['comment_title'] = $_POST['comment_title'];
			}
			if (!ISSET($error) and ISSET($ids_blog)) {
				$check = "SELECT `blogs`.`blog_id`, `blogs`.`user_id`, `blogs`.`title`, `users`.`username` FROM `blogs` INNER JOIN `users` ON `blogs`.`user_id`=`users`.`user_id` WHERE `blog_id`=" . $ids_blog;
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
				$insert = "INSERT INTO `blog_negrazis`.`comments` (`blog_id`, `user_id`, `title`, `comment`, `timestamp`) VALUES ('" . $ids_blog . "', '" . $_SESSION['user_id'] . "', '" . mysqli_real_escape_string($dbc, $_POST['comment_title']) . "', '" . mysqli_real_escape_string($dbc, $_POST['my_comment']) . "', NOW())";
				mysqli_query($dbc, $insert) or DIE("COuld not comment into database.");
				//unset withstanding title and content
				unset($_SESSION['comment_title']);
				unset($_SESSION['comment_content']);
				unset($_SESSION['blogs_id']);
				$admin_msg = "<font color='green'><strong>Your comment was posted!</strong></font>";
			}
		}
		else {
			//save what user wrote in case of error
			openFancybox("#message");
			$_SESSION['comment_content'] = $_POST['my_comment'];
			$_SESSION['comment_title'] = $_POST['comment_title'];
			$comment_msg = "<font color='red'><strong>Your comment was empty.</strong></font>";
		}
	}
}

if ($_SESSION['admin_category'] == "categories") {
	if (ISSET($_GET['new_category'])) {
		unset($_SESSION['comment_title']);
		unset($_SESSION['comment_content']);
		$_SESSION['new_category'] = $_GET['new_category'];
		unset($_SESSION['cat_id']);
		openFancybox("#message");
	}
	if (ISSET($_GET['edit_category'])) {
		unset($_SESSION['new_category']);
		$_SESSION['cat_id'] = $_GET['edit_category'];
		openFancybox("#message");
		$select = "SELECT `category_name`, `category_desc` FROM `categories` WHERE `category_id`=" . $_SESSION['cat_id'];
		$get = mysqli_query($dbc, $select) or DIE("NOOOO");
		while ($row = mysqli_fetch_array($get)) {
			$_SESSION['comment_title'] = $row['category_name'];
			$_SESSION['comment_content'] = $row['category_desc'];
		}
	}

	//make new category
	$comment_msg = null;
	if (ISSET($_POST['post_comment'])) {
		if (ISSET($_SESSION['cat_id'])) {
			$ids_cat = $_SESSION['cat_id'];
		}
		if ($_POST['my_comment'] != null and $_POST['comment_title'] != null) {
			if (strlen($_POST['my_comment']) > 100) {
				$noob = true;
				$comment_msg = "<font color='red'><strong>Your description is too long. It is " . strlen($_POST['my_comment']) . " characters long, but the max length for a description is 100 characters.</strong></font>";
				openFancybox("#message");
				$_SESSION['comment_content'] = $_POST['my_comment'];
				$_SESSION['comment_title'] = $_POST['comment_title'];
			}
			if (!ISSET($noob)) {
				if (strpos($_POST['comment_title'], " ") !== false) {
					$noob = true;
					$comment_msg = "<font color='red'><strong>The title cannot have a space in it.</strong></font>";
					openFancybox("#message");
					$_SESSION['comment_content'] = $_POST['my_comment'];
					$_SESSION['comment_title'] = $_POST['comment_title'];
				}
			}
			//check for duplicate title
			$get_catz = "SELECT `category_name`, `category_id` FROM `categories`";
			$rez = mysqli_query($dbc, $get_catz) or DIE("could not get catz");
			while ($row = mysqli_fetch_array($rez) and !ISSET($noob)) {
				if (trim(strtolower($_POST['comment_title'])) == strtolower($row['category_name'])) {
					if (!ISSET($_SESSION['cat_id'])) {
						$noob = true;
						openFancybox("#message");
						$_SESSION['comment_content'] = $_POST['my_comment'];
						$_SESSION['comment_title'] = $_POST['comment_title'];
						$comment_msg = "<font color='red'><strong>That category name is already in use.</strong></font>";
						break 1;
					}
					elseif ($row['category_id'] != $_SESSION['cat_id']) {
						$noob = true;
						openFancybox("#message");
						$_SESSION['comment_content'] = $_POST['my_comment'];
						$_SESSION['comment_title'] = $_POST['comment_title'];
						$comment_msg = "<font color='red'><strong>That category name is already in use.</strong></font>";
						break 1;
					}
				}
			}
			if (!ISSET($noob) and !ISSET($ids_cat)) {
				$insert = "INSERT INTO `blog_negrazis`.`categories` (`category_name`, `category_desc`) VALUES ('" . mysqli_real_escape_string($dbc, $_POST['comment_title']) . "', '" . mysqli_real_escape_string($dbc, $_POST['my_comment']) . "')";
				mysqli_query($dbc, $insert) or DIE("COuld not category into database.");
				//unset withstanding title and content
				unset($_SESSION['comment_title']);
				unset($_SESSION['comment_content']);
				unset($_SESSION['new_category']);
				$admin_msg = "<font color='green'><strong>Your category was posted!</strong></font>";
			}
			elseif (!ISSET($noob) and ISSET($ids_cat)) {
				$up = "UPDATE `categories` SET `category_name`='" . mysqli_real_escape_string($dbc, $_POST['comment_title']) . "', `category_desc`='" . mysqli_real_escape_string($dbc, $_POST['my_comment']) . "' WHERE `category_id`=" . $ids_cat;
				mysqli_query($dbc, $up) or DIE("did not update cat");
				unset($_SESSION['cat_id']);
				$admin_msg = "<font color='green'><strong>The category was updated!</strong></font>";
			}
		}
		else {
			//save what user wrote in case of error
			openFancybox("#message");
			$_SESSION['comment_content'] = $_POST['my_comment'];
			$_SESSION['comment_title'] = $_POST['comment_title'];
			$comment_msg = "<font color='red'><strong>Your description and/or title was/were empty.</strong></font>";
		}
	}
}

if ($_SESSION['admin_category'] == "comments") {
	if (ISSET($_GET['edit_comment'])) {
		$ids_cat = $_GET['edit_comment'];
		$_SESSION['cat_id'] = $_GET['edit_comment'];
		openFancybox("#message");
		$select = "SELECT `title`, `comment` FROM `comments` WHERE `comment_id`=" . $_GET['edit_comment'];
		$get = mysqli_query($dbc, $select) or DIE("NOOOO");
		while ($row = mysqli_fetch_array($get)) {
			$_SESSION['comment_title'] = $row['title'];
			$_SESSION['comment_content'] = $row['comment'];
		}
	}
	//make new category
	$comment_msg = null;
	if (ISSET($_POST['post_comment'])) {
		if (ISSET($_SESSION['cat_id'])) {
			$ids_cat = $_SESSION['cat_id'];
		}
		if ($_POST['my_comment'] != null) {
			if (strlen($_POST['my_comment']) > 2000) {
				$noob = true;
				$comment_msg = "<font color='red'><strong>Your description is too long. It is " . strlen($_POST['my_comment']) . " characters long, but the max length for a description is 2000 characters.</strong></font>";
				openFancybox("#message");
				$_SESSION['comment_content'] = $_POST['my_comment'];
				$_SESSION['comment_title'] = $_POST['comment_title'];
			}
			if (!ISSET($noob) and ISSET($ids_cat)) {
				$up = "UPDATE `comments` SET `title`='" . mysqli_real_escape_string($dbc, $_POST['comment_title']) . "', `comment`='" . mysqli_real_escape_string($dbc, $_POST['my_comment']) . "', `edited`=NOW() WHERE `comment_id`=" . $ids_cat;
				mysqli_query($dbc, $up) or DIE("did not update cat");
				unset($_SESSION['cat_id']);
				$admin_msg = "<font color='green'><strong>The comment was updated!</strong></font>";
			}
		}
		else {
			//save what user wrote in case of error
			openFancybox("#message");
			$_SESSION['comment_content'] = $_POST['my_comment'];
			$_SESSION['comment_title'] = $_POST['comment_title'];
			$comment_msg = "<font color='red'><strong>The comment was blank.</strong></font>";
		}
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
	//if ($_SESSION['level'] != 1) {
		//header("Location:index.php");
	//}
}

//search blog posts builder

//compatible with blog management
//category search
if (ISSET($_GET['category_search']) and $_GET['category_search'] != null and $_SESSION['admin_category'] == "posts") {
	unset($_SESSION['writing']);
	unset($_SESSION['blogs_id']);
	$_SESSION['edit_blog'] = -1;
	$_GET['search'] = $_GET['category_search'];
	if (trim($_GET['search']) != null) {
		$_GET['search'] = trim($_GET['search']);
		$search = mysqli_real_escape_string($dbc, $_GET['search']);
		$find_category = "SELECT `category_id`, `category_name` FROM `blog_negrazis`.`categories` WHERE `category_name` LIKE '%" . $search . "%'";
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
if ($_SESSION['admin_category'] == "posts") {
	if ($_SESSION['category_id'] != 1) {
		$_SESSION['where'] = "WHERE `blogs`.`category_id` LIKE '%" . $_SESSION['category_id'] . "%'";
	}
	else {
		//$_SESSION['where'] = null;
	}
}

//content search
if (ISSET($_GET['content_search'])) {
	if (trim($_GET['search2']) != null) {
		$_GET['search2'] = trim($_GET['search2']);
		$search = trim(mysqli_real_escape_string($dbc, $_GET['search2']));
		//append onto where clause
		if ($_SESSION['admin_category'] == "posts") { //search for blogs
			$_SESSION['where'] = "WHERE `content` LIKE '%" . $search . "%' OR `title` LIKE '%" . $search . "%' OR `blog_id` LIKE '%" . $search . "%' OR `username` LIKE '%" . $search . "%'";
			//$_SESSION['where'] = $_SESSION['where'] . $find_content;
		}
		elseif ($_SESSION['admin_category'] == "categories") { //searching for categories
			$_SESSION['where'] = "WHERE `category_id` LIKE '%" . $search . "%' OR `category_name` LIKE '%" . $search . "%' OR `category_desc` LIKE '%" . $search . "%'";
		}
		elseif ($_SESSION['admin_category'] == "comments") {
			$_SESSION['where'] = "WHERE `comment_id` LIKE '%" . $search . "%' OR `comments`.`title` LIKE '%" . $search . "%' OR `comment` LIKE '%" . $search . "%' OR `username` LIKE '%" . $search . "%' OR `comments`.`blog_id` LIKE '%" . $search . "%'";
		}
		elseif ($_SESSION['admin_category'] == "users") {
			$_SESSION['where'] = "WHERE `user_id` LIKE '%" . $search . "%' OR `username` LIKE '%" . $search . "%' OR `email` LIKE '%" . $search . "%'";
		}
	}
}

if (ISSET($id_blog) and $_SESSION['admin_category'] == "posts") {
	$_SESSION['where'] = "WHERE `blogs`.`blog_id`=" . $id_blog;
}

//$_SESSION['where'] = $_SESSION['where'] . " AND `categories`.`category_id`=" . $_SESSION['category_id'];

$orderby = "ORDER BY `blogs`.`blog_id` ";
//order by
if ($_SESSION['admin_category'] == "posts") {
	if (ISSET($_GET['age'])) {
		$order = $_GET['age'];
	}
	else {
		$order = "DESC";
	}
}
elseif ($_SESSION['admin_category'] == "categories") {
	if (ISSET($_GET['age'])) {
		$order = $_GET['age'];
	}
	else {
		$order = "ASC";
	}
}
elseif ($_SESSION['admin_category'] == "comments") {
	if (ISSET($_GET['age'])) {
		$order = $_GET['age'];
	}
	else {
		$order = "DESC";
	}
}
elseif ($_SESSION['admin_category'] == "users") {
	if (ISSET($_GET['age'])) {
		$order = $_GET['age'];
	}
	else {
		$order = "DESC";
	}
}

//edit pwd
if (ISSET($_GET['edit_pwd'])) {
	$_SESSION['passy'] = $_GET['edit_pwd'];
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

if ($_SESSION['admin_category'] == "posts") {
	if (!ISSET($_SESSION['where'])) {
		$_SESSION['where'] = null;
	}
	$search_blogs = "SELECT `blogs`.`category_id` AS `blog_cats`, `blogs`.`blog_id`, `blogs`.`title`, `blogs`.`content`, `blogs`.`timestamp`, `blogs`.`hide`, `users`.`user_id`, `users`.`username`, `users`.`banned`, GROUP_CONCAT(`categories`.`category_name` ORDER BY `category_name` ASC) AS `category_name`, GROUP_CONCAT(`categories`.`category_id` ORDER BY `category_name` ASC) AS `category_id` FROM `blog_negrazis`.`blogs` INNER JOIN `blog_negrazis`.`users` ON `blogs`.`user_id`=`users`.`user_id` INNER JOIN `categories` ON `blogs`.`category_id` LIKE CONCAT('%', `categories`.`category_id`, '%') " . $_SESSION['where'] . " GROUP BY `blogs`.`blog_id` ORDER BY `blogs`.`blog_id` " . $order;
}
elseif ($_SESSION['admin_category'] == "categories") {
	if (!ISSET($_SESSION['where'])) {
		$_SESSION['where'] = null;
	}
	$search_blogs = "SELECT * FROM `categories` " . $_SESSION['where'] . " ORDER BY `category_id` " . $order;
}
elseif ($_SESSION['admin_category'] == "comments") {
	$search_blogs = "SELECT `comments`.`comment_id`, `comments`.`title`, `comments`.`comment`, `comments`.`hide`, `blogs`.`blog_id`, `users`.`user_id`, `users`.`username`, `users`.`banned` FROM `comments` INNER JOIN `blogs` ON `comments`.`blog_id`=`blogs`.`blog_id` INNER JOIN `users` ON `comments`.`user_id`=`users`.`user_id` " . $_SESSION['where'] . " ORDER BY `comment_id` " . $order;
}
elseif ($_SESSION['admin_category'] == "users") {
	if (!ISSET($_SESSION['where'])) {
		$_SESSION['where'] = null;
	}
	$search_blogs = "SELECT * FROM `users` " . $_SESSION['where'] . " ORDER BY `user_id` " . $order;
}
//echo $search_blogs;
$search_result = mysqli_query($dbc, $search_blogs) or DIE("Could not get search result.");

//expand extra categories
if (ISSET($_GET['expand_cats'])) {
	$cats = $_GET['expand_cats'];
	$cats = "SELECT `category_id`,`category_name` FROM `categories` WHERE '{" . $cats . "}' LIKE CONCAT ('%', `category_id`, '%')";
	$cats_result = mysqli_query($dbc, $cats) or DIE("could not fetch cats!");
	openFancybox("#message");
}

//get category info
$select_info = "SELECT `category_name`, `category_desc` FROM `categories` WHERE `category_id`=" . $_SESSION['category_id'];
$info_result = mysqli_query($dbc, $select_info) or DIE("Could not get category info.");
while ($row = mysqli_fetch_array($info_result)) {
	$category_name = $row['category_name'];
	$category_desc = $row['category_desc'];
}


//open blog
if (ISSET($_GET['open_blog'])) {
	$_SESSION['blog_id'] = $_GET['open_blog'];
	header("Location:view_post.php");
}


//store search
if (ISSET($_GET['search2']) and $_GET['search2'] != null) {
	$_SESSION['searchfor'] = $_GET['search2'];
}
elseif (ISSET($_GET['category_search']) and $_GET['category_search'] == null) {
	$_SESSION['searchfor'] = null;
}
?>