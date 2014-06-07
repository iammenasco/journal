<?php
// Created by Brian Menasco
// This work is licensed under a Creative Commons 
// Attribution-NonCommercial-ShareAlike 4.0 International License.
/**** START ****
Site Purpose


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



Notes Section
**** END ****/

/**** START ****
Test Content

The following content is used as dummy content to test the functions found on this page.
****************/
$alertCount = 2;
$loggedIn = false;
function testInput($data) {
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
/************
Test Content
**** END ****/

if (is_readable('model/model.php')) {
	require 'model/model.php';
} else {
	header('location: /errordocs/500.php');
}
if(isset($_GET['page'])) {
	$view = $_GET['page'];
} else {
	$view = 'home';
}

$user = currentUser('1');
$firstName = $user[0]['userFirstName'];
$lastName = $user[0]['userLastName'];
$fullName = $firstName . ' ' . $lastName;
$email = $user[0]['userEmail'];
$color = $user[0]['userColor'];
$userID = $user[0]['userID'];

$nav = createNav($loggedIn, $lastName, $alertCount, $view);
$footer = createFooter();

if (isset($_POST['action'])) {
	if ($_POST['action'] == 'register') {
		$firstName = testInput($_POST['firstName']);
		$lastName = testInput($_POST['lastName']);
		$email = testInput($_POST['email']);
		$password = testInput($_POST['password']);
		$password2 = testInput($_POST['password2']);
		// Validate the data

		// Check for errors, handle it!

		// Write data to database
		$insertResult = registerUser($firstName, $lastName, $email, md5($password));
		// Check Results
		if ($insertResult) {
			
		}
	} else if ($_POST['action'] == 'newEntry') {
		$title = testInput($_POST['title']);
		$content = testInput($_POST['content']);
		// Validate the data

		// Check for errors, handle it!

		// Write data to database
		$insertResult = newEntry($userID, $title, $content);
		// Check Results
		if ($insertResult) {
			header('Location: /site/?page=entries');
		}
	} else if ($_POST['action'] == 'updateEntry') {
		$title = testInput($_POST['title']);
		$content = testInput($_POST['content']);
		$entryID = testInput($_POST['entryID']);
		// Validate the data

		// Check for errors, handle it!

		// Write data to database
		$insertResult = updateEntry($userID, $entryID, $title, $content);
		// Check Results
		if ($insertResult) {
			header('Location: site/?page=entries');
		}
	} else if ($_POST['action'] == 'delete') {
		$title = testInput($_POST['title']);
		$content = testInput($_POST['content']);
		$entryID = testInput($_POST['entryID']);
		// Validate the data

		// Check for errors, handle it!

		// Write data to database
		$insertResult = deleteEntry($userID, $entryID, $title, $content);
		// Check Results
		if ($insertResult) {
			header('Location: /site/?page=entries');
		}
	}
}
if(isset($_GET['page'])) {
	if ($_GET['page'] == 'signUp') {
		$body = createSignUp($footer);
	} else if ($_GET['page'] == 'signIn') {
		$body = createSignIn($footer);
	} else if ($_GET['page'] == 'entries') {
		$avatar = getAvatar($fullName ,$email);
		$entries = listAll($userID);
		$body = '<div class="pure-g">';
		$body .= '<ul class="pure-1 entryList nav-tabs">';
		$body .= entryList($userID, $entries, $avatar);
		$body .= '</ul><div class="pure-1 entry tab-content">';
		$body .= entryContent($userID, $entries, $footer);
		$body .= '</div>';
	} else if ($_GET['page'] == 'new') {
		$body = createEntry($userID, $footer);
	} else if ($_GET['page'] == 'delete')
		$body = createDelete($userID, $footer);
} else {
	$body = createHome($footer);
}

/**** START ****
Create nav

Home - Current Journal
About - About site w/ link to contact information
Features - Screen Shots
Support - QA/Contact information
---------
Log in

New Entry - New basic entry (Allow options of changing templates for time log etc)
Share - Invite others to read it
Alerts - If there are new comments on the post
Families - List of public/private shared journals
---------
Account
Edit Journal - Private/Public 
Admin - CMS Stuff
Log out - Keep at the bottom?
Theme - Dropdown color selector/changing thing
****************/
function createNav($loggedIn, $lastName, $alertCount, $view) {
	if ($loggedIn) {
		$userItems = '
			<li class="pure-menu-heading">Journals</li>
			<li class="' . active($view, 'new') . '"><a href="#">New Entry</a></li>
			<li class="' . active($view, 'alerts') . '"><a href="#">Alerts <span class="name entry-count">{$alertCount}</span></a></li>
			<li class="' . active($view, 'families') . '"><a href="#">Families</a></li>
			<li class="' . active($view, 'share') . '"><a href="#">Share</a></li>
			<li class="pure-menu-heading">Account</li>
			<li class="' . active($view, 'switch') . '"><a href="#">Switch Journal</a></li>
			<li class="' . active($view, 'editJournal') . '"><a href="#">Edit Journal</a></li>
			<li class="pure-menu-heading"></li>
			<li class="' . active($view, 'logOut') . '"><a class="pure-menu-heading" href="?view=home">Log out</a></li>
			<li class="' . active($view, 'admin') . '"><a href="#">Admin</a></li>';
} else {
	$userItems = '
	<li class="pure-menu-heading"></li>
	<li class="' . active($view, 'signIn') . '"><a href="?page=signIn">Log in</a></li>
	<li class="' . active($view, 'signUp') . '"><a href="?page=signUp">Sign Up</a></li>';
}
	return '
<a href="#menu" id="menuLink" class="menu-link">
	<span></span>
</a>
<div id="menu">
	<div class="pure-menu pure-menu-open">
		<a class="pure-menu-heading" href="/site">I am <span class="name">' .  $lastName . '</span>.</a>
		<ul id="std-menu-items">
			<li class="menu-item-divided' . active($view, 'entries') . '"><a href="?page=entries">Entries</a></li>
			<li class="' . active($view, 'about') . '"><a href="#">About</a></li>
			<li class="' . active($view, 'features') . '"><a href="#">Features</a></li>
			<li class="' . active($view, 'support') . '"><a href="#">Support</a></li>'
			. $userItems .
			'<li>
				<select class="menu-select" onChange="loadCSS(this.value);">
					<option selected="selected" disabled="disabled">Theme</option>
					<option value="blue">Blue</option>
					<option value="lime">Green</option>
					<option value="orange">Orange</option>
					<option value="pink">Pink</option>
					<option value="purple">Purple</option>
					<option value="red">Red</option>
					<option value="white">White</option>
					<option value="yellow">Yellow</option>
				</select>
			</li>
		</ul>
	</div>
</div>';
}

function active($view, $page) {
	if($view == $page) {
		return ' pure-menu-selected';
	} else {
	return '';
}
}
/************
Create nav
**** END ****/

/**** START ****
Get avatar

Use Gravatar. Maybe just as an option. Users can opt-out, but use this service as default
****************/
function getAvatar($fullName, $email) {
	$default = "http://www.gravatar.com/avatar/c8c1467507a042f49ab30024e6e7f6d9?s=64";
	$url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=64";
	return <<<HTML
	<img class="entry-avatar" alt="{$fullName}&#x27;s avatar" height="64" width="64" src="{$url}">
HTML;
}
/************
Get avatar
**** END ****/

/**** START ****
Create entry list

Populate a list of 10 most current entries with "Show More" button, or Lazy Load all in Desktop View
****************/

function entryList($user, $entries, $avatar) {
	$list = '';
	$i = 0;
	foreach ($entries as $entry) {
		if (strlen($entry['entryTitle']) > 27) {
			$title = substr($entry['entryTitle'], 0, 27) . '...';
		} else {
			$title = $entry['entryTitle'];
		}
		if (strlen($entry['entryContent']) > 75) {
			$snip = substr($entry['entryContent'], 0, 75) . '...';
		} else {
			$snip = $entry['entryContent'];
		}
		$id = 'entry' . $entry['entryID'];
		if ($i == 0) {
			$active = ' active';
		} else {
			$active = '';
		}
		$name = entryName($entry['entryCreatedBy']);
		$list .= <<<HTML
	<li class="{$active}"><a href="#{$id}" data-toggle="tab">
		<div class="entry-item pure-g">
			<div class="pure-u">
				{$avatar}
			</div>
			<div class="pure-u-3-4">
				<h5 class="entry-name">{$name[0]['userFirstName']} {$name[0]['userLastName']}</h5>
				<h4 class="entry-subject">{$title}</h4>
				<p class="entry-desc">{$snip}</p>
			</div>
		</div>
	</a></li>
HTML;
		$i++;
	}
	return $list;
}
/************
Create entry List
**** END ****/

/**** START ****
Create entry content

Possibilities for new entry templates(When the user clicks new)
	Default - Journal. Just text. Maybe a picture.
	Single photo uploading - One picture, see if I can get the location, time when the image was created... crap like that.
	Study Journal - Book/Class field, notes
	Travel Journal
	Gratitude Journal
	Time Log - Form of start time, end time, auto date, and description. Maybe location?
	Bug Reports - Instead of undread - Mark as in progress or resolved or new
		Fields can include location of bug (like a URL or something) screen shot, replication steps or whatever.

****************/
function entryContent($user, $entries, $footer) {
	$list = '';
	$i = 0;
	foreach ($entries as $entry) {
		$subject = $entry['entryTitle'];
		$time = date("g:ia, F jS, Y",strtotime($entry['entryTime']));
		$content = $entry['entryContent'];
		$id = 'entry' . $entry['entryID'];
		if ($i == 0) {
			$active = ' active';
		} else {
			$active = '';
		}
		$name = entryName($entry['entryCreatedBy']);
		$list .= <<<HTML
	<div class="entry-content tab-pane {$active}" id="{$id}">
		<div class="entry-content-header pure-g">
			<div class="pure-u-1-2">
				<h1 class="entry-content-title">{$subject}</h1>
				<p class="entry-content-subtitle">From <a>{$name[0]['userFirstName']} {$name[0]['userLastName']}</a> at <span>{$time}</span>
				</p>
			</div>
			<div class="entry-content-controls pure-u-1-2">
				<a href="?page=new" class="pure-button outline-inverse">New</a>
				<a href="?page=new&entry={$entry['entryID']}" class="pure-button outline-inverse">Edit</a>
				<a href="?page=delete&entry={$entry['entryID']}" class="pure-button outline-inverse">Delete</a>
			</div>
		</div>
		<div class="entry-content-body">{$content}</div>
		{$footer}
	</div>
HTML;
		$i++;
	}
return $list;
}
/************
Create entry content
**** END ****/

/**** START ****
New/Edit Entry

This function will do one of two things. First, for creating a new entry, it will currently just show a title box, and a text area. The timestamp, userID, and entryID will all be created behind the scenes during insert.

TODO:
Use a similar view as the Entries List. The List will be selectable templates, and the main area will be where you make your changes and edits to the form.

****************/
function createEntry ($user, $footer) {
	if(isset($_GET['entry'])) {
		$id = $_GET['entry'];
		$entry = listSingle($user, $id);
		$title = $entry[0]['entryTitle'];
		$content = $entry[0]['entryContent'];
		$value = 'updateEntry';
	} else {
		$id = '';
		$title = '';
		$content = '';
		$value = 'newEntry';
	}
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<form class="pure-form pure-form-stacked newEntry" action="." method="post">
			<fieldset>
				<div class="pure-control-group">
					<label for="title">Title</label>
					<textarea id="title" rows="1" cols="50" placeholder="Title" name="title">{$title}</textarea>
				</div>
				<div class="pure-control-group">
					<label for="entry">Entry</label>
					<textarea id="content" rows="15" cols="50" placeholder="Content" name="content">{$content}</textarea>
				</div>
			</fieldset>
			<div class="pure-controls">
			<input type="hidden" name="entryID" value="{$id}">
				<button type="submit" name="action" value="{$value}" class="pure-button outline-inverse">Submit</button>
			</div>
		</form>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
New/Edit Entry
**** END ****/

/**** START ****
Delete Entry
****************/
function createDelete($user, $footer) {
	if(isset($_GET['entry'])) {
		$id = $_GET['entry'];
		$entry = listSingle($user, $id);
		$title = $entry[0]['entryTitle'];
		$content = $entry[0]['entryContent'];
	}
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Are you sure you want to delete<br><span class="name">{$title}?</span>.</h1>
				<p class="lead">This can not be undone. Please dont cry if everything blows up.</p>
				<form class="pure-form pure-form-stacked" action="." method="post">
				<input type="hidden" name="title" value="{$title}">
				<input type="hidden" name="content" value="{$content}">
				<input type="hidden" name="entryID" value="{$id}">
				<button type="submit" name="action" value="delete" class="pure-button outline-inverse">Yes</button>
				<a href="?page=entries" class="pure-button outline-inverse">No</a>
				</form>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Delete Entry
**** END ****/

/**** START ****
Home Page
****************/
function createHome($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Under <span class="name">Construction</span>.</h1>
				<p class="lead">Patience you must have, my young padawan. Things are changing up a bit. Frequent updates are on the way, check some of them out at the <span class="name">β</span>eta page.</p>
				<p class="lead"><a class="pure-button outline-inverse" href="http://beta.iammenasco.com">See the future.</a></p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Home Page
**** END ****/

/**** START ****
Sign in
****************/
function createSignIn($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<form class="pure-form pure-form-aligned signIn">
			<fieldset>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input id="email" placeholder="Email Address">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" type="password" placeholder="Password">
				</div>
			</fieldset>
			<div class="pure-controls">
				<label for="cb" class="pure-checkbox">
					<input id="cb" type="checkbox" class="remember"> Will you remember me?
				</label>
				<a href="./?page=signUp" class="pure-button outline-inverse signUp">Sign Up
				</a>
				<button type="submit" class="pure-button outline-inverse">Submit</button>
			</div>
		</form>
		<div class="pure-u-1">
			{$footer}
		</div>
	</div>
HTML;
}
/************
Sign in
**** END ****/

/**** START ****
Sign up
****************/
function createSignUp($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<form class="pure-form pure-form-aligned signIn" action="." method="post">
			<fieldset>
				<div class="pure-control-group">
					<label for="firstName">First Name</label>
					<input id="firstName" placeholder="First Name" name="firstName">
				</div>
				<div class="pure-control-group">
					<label for="lastName">Last Name</label>
					<input id="lastName"  placeholder="Last Name" name="lastName">
				</div>
			</fieldset>
			<fieldset>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input id="email" placeholder="Email Address" name="email">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" type="password" placeholder="Password" name="password">
				</div>
				<div class="pure-control-group">
					<label for="password2">Repeat</label>
					<input id="password2" type="password" placeholder="Password" name="password2">
				</div>
			</fieldset>
			<div class="pure-controls">
				<button type="submit" name="action" value="register" class="pure-button outline-inverse signUp">Submit</button>
			</div>
		</form>
		<div class="pure-u-1">
			{$footer}
		</div>
	</div>
HTML;
}
/************
Sign up
**** END ****/

/**** START ****
Footer
****************/
function createFooter() {
	return <<<HTML
	<div class="mastfoot">
		<div class="inner">
			<p>See more on my <a href="http://portfolio.iammenasco.com">Portfolio</a>, by <a href="https://twitter.com/iammenasco">@iammenasco</a>.</p>
			<a href="http://beta.iammenasco.com/foreach">Teaching Presentation! </a>
			<a href="http://www.arvixe.com" target="_blank">Hosted By Arvixe</a>
		</div>
	</div>
HTML;
}
/************
Footer
**** END ****/

/**** START ****
Testing view
****************/

/************
Testing view
**** END ****/

/**** START ****
Includes
****************/
include 'view.php';
/************
Includes
**** END ****/
?>