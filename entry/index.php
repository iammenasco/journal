<?php
/**** START ****
Session

Ended upon logout.
****************/
session_start();

/**** START ****
Site Information

Created by Brian Menasco
This work is licensed under a Creative Commons 
Attribution-NonCommercial-ShareAlike 4.0 International License.

The purpose of my site is to allow people to keep online journals/diaries, allowing each entry to embed pictures, audio, and video. My site will allow people to have private and/or public journals where they can share entries, or even whole journals with others, or keep one for personal use. This will allow users to have collaborative journals, especially helpful for teams to share ideas or track various items together. The site can also be used by people who need to keep a log of work to share with their supervisors. This will be extremely useful for students who are required to keep logs for their internships to report back. My site will host various sources where people can find journal ideas, for those just starting off. I also want places where people can find information on journals, the importance of them.


Create blog for groups to stay connected.

My family has a blog where each member of the family can post. The family is together, but members can be invited to follow the family or family member.

Example

Brian and Brittany Menasco - Part of the Menascos - Invite the Grossmans to follow ours, not the families but the Yoders can follow the whole family.

Site Purpose
**** END ****/

/**** START ****
Notes Section

Different Views:
	Home - Not logged in
	Home - Logged in (Last opened journal (entry list and entry content))
	Templates (Use same layout, with entry list being a list of templates, and the entry content being form with the various fields for the entry)
	Account (settings page)
	List of journals
	List of families
	Alerts
	Sharing
	About
	Features
	Support
	CMS Stuff
	Login page
	Edit Entry

Information Section
**** END ****/

/**** START ****
Includes

Include the model and library.
****************/
if (is_readable('php/model/model.php')) {
	require 'php/model/model.php';
} else {
	header('location: /errordocs/500.php');
}

if (is_readable('php/library/library.php')) {
	require 'php/library/library.php';
} else {
	header('location: /errordocs/500.php');
}
/************
Includes
**** END ****/

/**** START ****
Set default values

Create session values, and include any personalization added by the user. Lastly, call the nav and footer so it can be included elsewhere.

@param $alertCount - NOT USED: INT value of unseen alerts. For future use.
@param $view - Default of the current page, used for the nav to highlight the current page.

@param $loggedIn - Changes the value of loggedIn according to the session variable.
@param $firstName - Sets the session value of firstName to a variable. Default is empty string.
@param $lastName - Sets the session value of lastName to a variable. Default is empty string.
@param $fullName - Concatenate firstName and lastName for easy use.
@param $email - Sets the session value of email to a variable. Default is empty string.
@param $color - If there is a custom color the user has saved, it will be stored.
@param $theme - If there is a custom theme the user has saved, it will be stored.
@param $userID - Sets the session value of email to a variable. Default is empty string.

@param $colorCSS - Creates a CSS link used for the view to import the specific color css.

@param $themeCSS - Creates a CSS link used for the view to import the specific theme css.

@param $nav - Calls the createNav function and sets it to a variable.
@param $footer -  Calls the createFooter function and sets it to a variable.
****************/
$alertCount = 2;
if(isset($_GET['page'])) {
	$view = $_GET['page'];
} else {
	$view = 'home';
}
if (isset($_SESSION['loggedIn']) && isset($_SESSION['loggedIn']) == TRUE) {
	$loggedIn = $_SESSION['loggedIn'];
	$firstName = $_SESSION['firstName'];
	$lastName = $_SESSION['lastName'];
	$fullName = $firstName . ' ' . $lastName;
	$email = $_SESSION['email'];
	$color = $_SESSION['color'];
	$theme = $_SESSION['theme'];
	$userID = $_SESSION['userID'];
} else {
	$loggedIn = FALSE;
	$firstName = '';
	$lastName = 'Menasco';
	$fullName = '';
	$email = '';
	$color = '';
	$theme = '';
	$userID = '';
}
if ($color != '') {
	$colorCSS = '<link rel="stylesheet" type="text/css" href="css/theme/' . $color . '.css" id="theme">';
} else {
	$colorCSS = '';
}
if ($theme != '') {
	$themeCSS = '<link rel="stylesheet" type="text/css" href="css/theme/' . $theme . '.css" id="theme">';
} else {
	$themeCSS = '';
}
$nav = createNav($loggedIn, $lastName, $alertCount, $view);
$footer = createFooter();
/************
Default values
**** END ****/


/**** START ****
POST Logic.

This if statement goes through the possible values that come through a post, and what to do with them. It includes calls for validation/sanitation of data, communication to the model, and calling functions to prepare for the view.

Contains the following post actions:
	register
	newEntry
	updateEntry
	delete
	signIN

Each statement sets the post values to variables, which are validated and sanitized. It then calls the specific function within the model, with error handling along the way.
****************/
if (isset($_POST['action'])) {
	if ($_POST['action'] == 'register') {
		$firstName = ucfirst(strtolower(testInput($_POST['firstName'])));
		$lastName = ucfirst(strtolower(testInput($_POST['lastName'])));
		$email = testInput($_POST['email']);
		$password = testInput($_POST['password']);
		$password2 = testInput($_POST['password2']);
		$title = 'Welcome to I am Menasco!';
		$content = 'This is your first post! You can edit it, change the template, or delete it. Feel free to do whatever you want with it, and continue to add more! You can (and should) create many entries and use this as an online journal. Have fun and let me know how it all works out for you!';

		// Validate the data
		if (!empty($firstName) &&
			!empty($lastName) &&
			!empty($email) &&
			filter_var($email, FILTER_VALIDATE_EMAIL) &&
			!empty($password) &&
			!empty($password2) &&
			$password == $password2) {
			$valid = true;
		} else {
			$valid = false;
		}
		// Check for errors, handle it!

		// Write data to database
		if ($valid) {
			$result = registerUser($firstName, $lastName, $email, md5($password));
		} else {
			$_SESSION['alert'] = array('title' => 'Opps!', 'message' => 'There was an error creating your account. Please enter information in all the fields.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signUp');
			exit;
		}
		// Check Results
		if ($result) {
			$result = logIn($email, md5($password));
			createSession($result);
			newEntry($result['userID'], $title, $content, NULL, NULL, NULL, 1);
			$_SESSION['alert'] = array('title' => "Welcome, $firstName!", 'message' => 'Your account has been created, and you are now logged in. Have fun!', 'status' => 'success', 'show' => true);
			header('Location: /site/?page=entries');
		} else if ($result == false) {
			$_SESSION['alert'] = array('title' => 'Opps!', 'message' => 'There was an issue creating your account, please try again.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signUp');
		} 
	} else if ($_POST['action'] == 'newEntry') {
		$title = testInput($_POST['title']);
		$content = testInput($_POST['content']);
		$url = testInput($_POST['url']);
		$start = testInput($_POST['start']);
		$end = testInput($_POST['end']);
		$templateID = testInput($_POST['templateID']);
		// Validate the data
		if (!empty($title) &&
			!empty($content) &&
			!empty($templateID)) {
			$valid = true;
		} else {
			$valid = false;
		}
		// Check for errors, handle it!
		// Write data to database
		if ($valid) {
			$result = newEntry($userID, $title, $content, $url, $start, $end, $templateID);
		} else {
			$_SESSION['alert'] = array('title' => 'Error', 'message' => 'Could not create new entry. Please try again.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=new');
			exit;
		}
		// Check Results
		if ($result) {
			$_SESSION['alert'] = array('title' => 'Entry Saved', 'message' => 'The new entry is now available for your enjoyment', 'status' => 'success', 'show' => true);
			header('Location: /site/?page=entries');
		} else {
			$_SESSION['alert'] = array('title' => 'Error', 'message' => 'Could not create new entry. Please try again.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=new');
		}
	} else if ($_POST['action'] == 'updateEntry') {
		$title = testInput($_POST['title']);
		$content = testInput($_POST['content']);
		$url = testInput($_POST['url']);
		$start = testInput($_POST['start']);
		$end = testInput($_POST['end']);
		$entryID = testInput($_POST['entryID']);
		$templateID = testInput($_POST['templateID']);
		// Validate the data
		if (!empty($title) &&
			!empty($content) &&
			!empty($entryID) &&
			!empty($templateID)) {
			$valid = true;
		} else {
			$valid = false;
		}
		// Check for errors, handle it!

		// Write data to database
		if ($valid) {
			$result = updateEntry($userID, $entryID, $title, $content, $url, $start, $end, $templateID);
		} else {
			$_SESSION['alert'] = array('title' => 'Changes not saved', 'message' => 'Something went wrong, and the changes were not saved. Please try again!', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=entries');
			exit;
		}
		// Check Results
		if ($result) {
			$_SESSION['alert'] = array('title' => 'Entry updated!', 'message' => "$title has now been changed.", 'status' => 'success', 'show' => true);
			header('Location: /site/?page=entries');
		} else {
			$_SESSION['alert'] = array('title' => 'Changes not saved', 'message' => 'Something went wrong, and the changes were not saved. Please try again!', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=entries');
		}
	} else if ($_POST['action'] == 'delete') {
		$title = ucfirst(strtolower(testInput($_POST['title'])));
		$content = testInput($_POST['content']);
		$entryID = testInput($_POST['entryID']);
		// Validate the data
		if (!empty($title) &&
			!empty($content) &&
			!empty($entryID)) {
			$valid = true;
		} else {
			$valid = false;
		}
		// Check for errors, handle it!

		// Write data to database
		if ($valid) {
			$result = deleteEntry($userID, $entryID, $title, $content);
		} else {
			$_SESSION['alert'] = array('title' => 'Entry not Deleted', 'message' => 'The entry was not deleted, please try again!', 'status' => 'warning', 'show' => true);
			header('Location: /site/?page=entries');
		}
		// Check Results
		if ($result) {
			$_SESSION['alert'] = array('title' => 'Entry Deleted', 'message' => 'There is no going back now!', 'status' => 'success', 'show' => true);
			header('Location: /site/?page=entries');
		} else {
			$_SESSION['alert'] = array('title' => 'Entry not Deleted', 'message' => 'The entry was not deleted, please try again!', 'status' => 'warning', 'show' => true);
			header('Location: /site/?page=entries');
		}
	} else if ($_POST['action'] == 'signIn') {
		$email = testInput($_POST['email']);
		$password = testInput($_POST['password']);
		// Validate the data
		if (!empty($email) &&
			!empty($password)) {
			$valid = true;
		} else {
			$valid = false;
		}
		// Check for errors, handle it!

		// Write data to database
		if ($valid) {
			$result = logIn($email, md5($password));
		} else {
			$_SESSION['alert'] = array('title' => 'Opps!', 'message' => 'There was an error logging into your account. Please enter information in all the fields.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
			exit;
		}
		// Check Results
		if ($result) {
			createSession($result);
			$_SESSION['alert'] = array('title' => 'Welcome!', 'message' => 'You have successfully logged in', 'status' => 'success', 'show' => true);
			header('Location: /site/?page=entries');
		} else {
			$_SESSION['alert'] = array('title' => 'Try Again.', 'message' => 'The email or password you entered is incorrect. Please try again!', 'status' => 'warning', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	}
}
/************
Post Logic
**** END ****/

/**** START ****
GET Logic.

This if statement will run whenever someone goes to a new page. It calls the required functions to sent the view the required information for that page. 

Contains the following view possibilities:
	signUp
	signIn
	entries
	new
	delete
	logOut
	about
	features
	support

Default is the home page.

Each possibility contains the following variables that are used directly in the view.
@variable $alert - Calls the createAlert function which sets the alert messages if any .
@unset - Destroys the alert message once it is used.
@variable $body - Used to create the various pages, and contains the markup displayed on the page.
@variable $viewText - Contains the page title that changes with each view.

Views that required logged in permission are checked and directed to the signIn page for the user to sign into.
****************/
if(isset($_GET['page'])) {
	if ($_GET['page'] == 'signUp') {
		$alert = createAlert();
		unset($_SESSION['alert']);
		$body = createSignUp($footer);
		$viewText = '| Sign Up';
	} else if ($_GET['page'] == 'signIn') {
		$alert = createAlert();
		unset($_SESSION['alert']);
		$body = createSignIn($footer);
		$viewText = '| Log In';
	} else if ($_GET['page'] == 'entries') {
		if ($loggedIn) {
			$alert = createAlert();
			unset($_SESSION['alert']);
			$avatar = getAvatar($fullName ,$email);
			$entries = listAll($userID);
			$body = '<div class="pure-g"><ul class="pure-1 entryList nav-tabs">';
			$body .= entryList($userID, $entries, $avatar);
			$body .= '</ul><div class="pure-1 entry tab-content">';
			$body .= entryContent($userID, $entries, $footer);
			$body .= '</div>';
			$viewText = '| Entries';
		} else {
			$_SESSION['alert'] = array('title' => 'Please Login.', 'message' => 'To view the previous page, you must be logged in.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	} else if ($_GET['page'] == 'new') {
		if ($loggedIn) {
			$alert = createAlert();
			unset($_SESSION['alert']);
			$templates = getTemplates();
			$body = '<div class="pure-g"><ul class="pure-1 entryList nav-tabs">';
			$body .= createNewList($userID, $templates);
			$body .= '</ul><div class="pure-1 entry tab-content">';
			$body .= createNewEntry($userID, $templates, $footer);
			$body .= '</div></div>';
			$viewText = '| Entries | Editor';
		} else {
			$_SESSION['alert'] = array('title' => 'Please Login.', 'message' => 'To view the previous page, you must be logged in.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	} else if ($_GET['page'] == 'delete') {
		if ($loggedIn) {
			$alert = createAlert();
			unset($_SESSION['alert']);
			$body = createDelete($userID, $footer);
			$viewText = '| Entries | Delete';
		} else {
			$_SESSION['alert'] = array('title' => 'Please Login.', 'message' => 'To view the previous page, you must be logged in.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	} else if ($_GET['page'] == 'logOut') {
		$_SESSION['loggedIn'] = FALSE;
		unset($_SESSION['userID']);
		unset($_SESSION['lastName']);
		unset($_SESSION['firstName']);
		unset($_SESSION['email']);
		unset($_SESSION['color']);
		unset($_SESSION['theme']);
		session_destroy();
		header('Location: /site');
	} else if ($_GET['page'] == 'about') {
		$alert = createAlert();
		unset($_SESSION['alert']);
		$body = createAbout($footer);
		$viewText = '| About';
	} else if ($_GET['page'] == 'features') {
		$alert = createAlert();
		unset($_SESSION['alert']);
		$body = createFeatures($footer);
		$viewText = '| Features';
	} else if ($_GET['page'] == 'support') {
		$alert = createAlert();
		unset($_SESSION['support']);
		$body = createSupport($footer);
		$viewText = '| Support';
	} else if ($_GET['page'] == 'settings') {
		if ($loggedIn) {
			$alert = createAlert();
			unset($_SESSION['alert']);
			$body = createSettings($footer);
			$viewText = "| $lastName's Settings";
		} else {
			$_SESSION['alert'] = array('title' => 'Please Login.', 'message' => 'To view the previous page, you must be logged in.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	} else if ($_GET['page'] == 'admin') {
		if ($loggedIn) {
			$alert = createAlert();
			unset($_SESSION['alert']);
			$body = createAdmin($footer);
			$viewText = '| Admin';
		} else {
			$_SESSION['alert'] = array('title' => 'Please Login.', 'message' => 'To view the previous page, you must be logged in.', 'status' => 'danger', 'show' => true);
			header('Location: /site/?page=signIn');
		}
	}
}
else {
	$body = createHome($footer);
	$alert = createAlert();
	$viewText = '';
}
/************
GET Logic
**** END ****/

/**** START ****
Includes

View
****************/
require 'php/view/view.php';
/************
Includes
**** END ****/
?>