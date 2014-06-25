<?php
/**** START ****
library.php Information

Created by Brian Menasco
This work is licensed under a Creative Commons 
Attribution-NonCommercial-ShareAlike 4.0 International License.

File contains various functions used by index.php

Information Section
**** END ****/

/**** START ****
Set Session variables

Sets all the vales sent from the database into session variables.

@param $userArray - Array from the database.

@return - Returns true, indicating that the session variables have been set.
****************/
function createSession($userArray) {
	$_SESSION['loggedIn'] = TRUE;
	$_SESSION['userID'] = $userArray['userID'];
	$_SESSION['lastName'] = $userArray['userLastName'];
	$_SESSION['firstName'] = $userArray['userFirstName'];
	$_SESSION['email'] = $userArray['userEmail'];
	$_SESSION['color'] = $userArray['userColor'];
	$_SESSION['theme'] = $userArray['userTheme'];
	return TRUE;
}
/************
Session Variables
**** END ****/

/**** START ****
Test Input

This is a function to run the data received through a post and sanitize inputs.

@param $data - Contains the strings from the POST requests.

@return - Return the sanitized data.
****************/
function testInput($data) {
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
/************
Test Input
**** END ****/

/**** START ****
Alert

Creates Alert HTML for information and errors. Uses four types of popups to display information, with various colors
	COLOR: VALUE
	Green: success
	Blue: info
	Yellow: warning
	Red: danger

@return - HTML of the alert to be used in the view.
****************/
function createAlert(){
	if (isset($_SESSION['alert']) && $_SESSION['alert']['show']) {
		$title = $_SESSION['alert']['title'];
		$message = $_SESSION['alert']['message'];
		$status = $_SESSION['alert']['status'];
		$alert = <<<HTML
		<div class="alertPopup">
			<div class="alert alert-{$status} alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>{$title}</strong> {$message}
			</div>
		</div>
HTML;
		return $alert;
	} else {
		return FALSE;
	}
}
/************
End Alert
**** END ****/

/**** START ****
Create nav

Creates the left navigation of the page

@param $loggedIn - True or False. Depending on the value, it will show and hide certain links, allowing logged in user more possibilities and links to their entries
@param $lastName - Used at the top of the navigation
@param $alertCount - NOT USED: Yet... Will display count of unseen alerts on entries
@param $view - Shows current view of the page used for highlighting that item in the nav.

@return - HTML of the nav to be used in the view

Also contains the active function, used to see if the current view matches that item on the Nav.

@param $view - Current view the user is looking at
@param $page - Specific page the nav item points to.

@return - If it matches, it returns the class used to highlight that item in the nav.
****************/
function createNav($loggedIn, $lastName, $alertCount, $view) {
	if ($loggedIn) {
		$userItems = '
		<li class="pure-menu-heading">Journals</li>
		<li class="' . active($view, 'entries') . '"><a title="Entries" href="?page=entries"><span class="navIcon ion-document-text"></span>Entries</a></li>
		<li class="' . active($view, 'logOut') . '"><a title="Log Out" href="?page=logOut"><span class="navIcon ion-log-out"></span>Log out</a></li>
		<li class="' . active($view, 'settings') . '"><a title="User Settings" href="?page=settings"><span class="navIcon ion-ios7-gear"></span>Settings</a></li>
		<li class="' . active($view, 'admin') . '"><a title="Site Administration" href="?page=admin"><span class="navIcon ion-settings"></span>Admin</a></li>';
	} else {
		$userItems = '
		<li class="pure-menu-heading"></li>
		<li class="' . active($view, 'signIn') . '"><a title="Log In" href="?page=signIn"><span class="navIcon ion-log-in"></span>Log in</a></li>
		<li class="' . active($view, 'signUp') . '"><a title="Create Account" href="?page=signUp"><span class="navIcon ion-person-add"></span>Sign Up</a></li>';
	}
	return '
	<a href="#menu" id="menuLink" class="menu-link">
		<span></span>
	</a>
	<div id="menu">
		<div class="pure-menu pure-menu-open">
			<a title="Home" class="pure-menu-heading" href="/site">I am <span class="name">' .  $lastName . '</span>.</a>
			<ul id="std-menu-items">
				<li class="menu-item-divided' . active($view, 'about') . '"><a title="About" href="?page=about"><span class="blah navIcon ion-ios7-information"></span>About</a></li>
				<li class="' . active($view, 'features') . '"><a title="Features" href="?page=features"><span class="navIcon ion-lightbulb"></span>Features</a></li>
				<li class="' . active($view, 'support') . '"><a title="Support" href="?page=support"><span class="navIcon ion-ios7-help"></span>Support</a></li>
				<li class="' . active($view, 'news') . '"><a title="News" href="?page=news"><span class="navIcon ion-ios7-paper"></span>News</a></li>'
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

Tie-in for Gravatar with a default image of something. I think it is my picture that is the image. I have not actually tested this yet, so who really knows if it works. Either way, you get a sweet picture of me flying.

@param $fullName - Full name of the user who created the entry.
@param $email - Email of the user who created the entry.

@return - HTML img tag used in the entries to display the avatar.
****************/
function getAvatar($fullName, $email) {
	$default = "http://www.gravatar.com/avatar/c8c1467507a042f49ab30024e6e7f6d9?s=64";
	$url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&amp;s=64";
	return <<<HTML
	<img class="entry-avatar" title="{$fullName}" alt="{$fullName}&amp;#x27;s avatar" height="64" width="64" src="{$url}">
HTML;
}
/************
Get avatar
**** END ****/

/**** START ****
Create entry list

Used to create the list of entries from that user. entryTitle is trimmed if there are more than 27 characters. entryContrny is trimmed if there are more than 75 characters for the snippet.

@param $userID - ID of the currently logged in user
@param $entries - Array of all the entries from the entries table
@param $avatar - HTML img tag of the avatar of the user for the entry.

@variable $list - Adds the New Entry on the bottom of the links.

@return - HTML used for the list of entries in the view.
****************/
function entryList($userID, $entries, $avatar) {
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
		<li class="{$active}"><a title="{$title}" href="#{$id}" data-toggle="tab">
			<div class="entry-item pure-g">
				<div class="pure-u">
					{$avatar}
				</div>
				<div class="pure-u-3-4">
					<h5 class="entry-name">{$name['userFirstName']} {$name['userLastName']}</h5>
					<h4 class="entry-subject">{$title}</h4>
					<p class="entry-desc">{$snip}</p>
				</div>
			</div>
		</a></li>
HTML;
		$i++;
	}
	$list .= <<<HTML
	<li>
		<div class="entry-item pure-g">
			<a title="Create a new Entry" href="?page=new" class="pure-button outline-inverse"><span class="entryIcon ion-plus"></span>New Entry</a>
		</div>
	</li>
HTML;
	return $list;
}
/************
Create entry List
**** END ****/

/**** START ****
Create entry content

Creates the HTML of each entry. All entries are hidden unless they contain the 'active' class, which is added through JavaScript when the list item for the entry is clicked. The date is formatted for each entry, and 'active' is included on the first entry of the list.

@param $userID - ID of the currently logged in user
@param $entries - Array of entries from the entries table.
@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of all entries.
****************/
function entryContent($userID, $entries, $footer) {
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
					<p class="entry-content-subtitle">From <a>{$name['userFirstName']} {$name['userLastName']}</a> at <span>{$time}</span>
					</p>
				</div>
				<div class="entry-content-controls pure-u-1-2">
					<a title="Create a New entry" href="?page=new" class="pure-button outline-inverse"><span class="entryIcon ion-plus"></span>New</a>
					<a title="Edit current entry" href="?page=new&amp;entry={$entry['entryID']}" class="pure-button outline-inverse"><span class="entryIcon ion-edit"></span>Edit</a>
					<a title="Delete this entry" href="?page=delete&amp;entry={$entry['entryID']}" class="pure-button outline-inverse"><span class="entryIcon ion-trash-a"></span>Delete</a>
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
New/Edit List

The list of all the available templates that can be used when creating or editing an entry.

@param $userID - ID of the currently logged in user
@param $templates - Array of templates from the templates table.

@return - HTML of the list of templates.
****************/
function createNewList($userID, $templates) {
	$list = '';
	$i = 0;
	foreach ($templates as $template) {
		$title = $template['templateTitle'];
		$snip = $template['templateDescription'];
		$id = 'template' . $template['templateID'];
		if ($i == 0) {
			$active = ' active';
		} else {
			$active = '';
		}
		$list .= <<<HTML
		<li class="{$active}"><a title="{$title}" href="#{$id}" data-toggle="tab">
			<div class="entry-item pure-g">
				<div class="pure-u-3-4">
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
New/Edit List
**** END ****/

/**** START ****
New/Edit Entry

Create a new entry or edit an existing one. Depending on the template, the view will display other things, each item needs to see if it should be included within the template. Similar to the entry view, the 'active' class is used only on the first template, but changed through JavaScript to display each available template.

Edit will be used if there is a parameter sent though the URL indicating the entry to edit.

Each input contains a field with the editable title, and a hidden input that contains the original text.

@param $userID - ID of the currently logged in user
@param $templates - Array of templates from the templates table.
@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of all templates, and the respective fields.
****************/
function createNewEntry($userID, $templates, $footer) {
	$list = '';
	$i = 0;
	if(isset($_GET['entry'])) {
		$entryID = $_GET['entry'];
		$entry = listSingle($userID, $entryID);
		$entryTitle = $entry['entryTitle'];
		$entryContent = nl2br($entry['entryContent'], FALSE);
		$displayTime = date("g:ia, F jS, Y",strtotime($entry['entryTime']));
		$entryURL = $entry['entryURL'];
		$entryStartTime = $entry['entryStartTime'];
		$entryEndTime = $entry['entryEndTime'];
		$value = 'updateEntry';
	} else {
		$entryID = '';
		$entryTitle = '';
		$entryContent = '';
		$displayTime = date("g:ia, F jS, Y", time());
		$entryURL = '';
		$entryStartTime = '';
		$entryEndTime = '';
		$value = 'newEntry';
	}
	$name = entryName($userID);
	foreach ($templates as $template) {
		if ($template['templateEntryTitle'] == 1) {
			$title = '<label for="title">Title</label><textarea id="title" rows="1" placeholder="Title" name="title">' . $entryTitle .'</textarea>';
		} else {
			$title = '<input type="hidden" name="title" value="' . $entryTitle .'">';
		}

		if ($template['templateTime'] == 1) {
			$time = $displayTime;
		} else {
			$time = '';
		}

		if ($template['templateContent'] == 1) {
			$content = '<label for="entry">Entry</label><textarea id="entry" rows="15" cols="50" placeholder="Entry" name="content">' . $entryContent . '</textarea>';
		} else {
			$content = '<input type="hidden" name="content" value="' . $entryContent . '">';
		}

		if ($template['templateURL'] == 1) {
			$url = '<label for="url">URL</label><textarea id="url" rows="1" cols="50" placeholder="URL" name="url">' . $entryURL .'</textarea>';
		} else {
			$url = '<input type="hidden" name="url" value="' . $entryURL . '">';
		}

		if ($template['templateStartTime'] == 1) {
			$start = '<label for="start">Start Time</label><textarea id="start" rows="1" cols="50" placeholder="Start Time" name="start">' . $entryStartTime .'</textarea>';
		} else {
			$start = '<input type="hidden" name="start" value="' . $entryStartTime . '">';
		}

		if ($template['templateEndTime'] == 1) {
			$end = '<label for="end">URL</label><textarea id="end" rows="1" cols="50" placeholder="End Time" name="end">' . $entryEndTime .'</textarea>';
		} else {
			$end = '<input type="hidden" name="end" value="' . $entryEndTime . '">';
		}
		// $file = $template['templateFile'];
		$templateID = 'template' . $template['templateID'];
		if ($i == 0) {
			$active = ' active';
		} else {
			$active = '';
		}
		$list .='
		<div class="entry-content tab-pane' . $active . '" id="' . $templateID . '">
			<form class="pure-form pure-form-stacked newEntry" action="." method="post">
				<input type="hidden" name="entryID" value="' . $entryID . '">
				<input type="hidden" name="templateID" value="' . $template['templateID'] . '">
				<div class="entry-content-header pure-g">
					<div class="pure-u-1-2">
						<div class="pure-control-group">
							' . $title . '
						</div>
						<p class="entry-content-subtitle">Created At <span>' . $time . '</span>
						</p>
					</div>
					<div class="entry-content-controls pure-u-1-2">
						<button title="Create New Entry" type="submit" name="action" value="' . $value . '" class="pure-button outline-inverse">Submit</button>
						<a title="Cancel and go back" href="?page=entries" class="pure-button outline-inverse">Cancel</a>
					</div>
				</div>
				<div class="entry-content-body">
					<div class="pure-control-group">
						' . $start . $end . $url . $content . '
					</div>
				</div>
			</form>
			' . $footer . '
		</div>';
		$i++;
	}
	return $list;
}
/************
New/Edit Entry
**** END ****/

/**** START ****
Delete Entry

Warning view if the user wants to delete the selected entry. entryID, tile, and content are sent through POST to the delete function. Yes or No displayed, Yes going through with the deletion, but No going back to the list of entries.

@param $userID - ID of the currently logged in user.
@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the delete page used for the view.
****************/
function createDelete($userID, $footer) {
	if(isset($_GET['entry'])) {
		$entryID = $_GET['entry'];
		$entry = listSingle($userID, $entryID);
		$title = $entry['entryTitle'];
		$content = $entry['entryContent'];
	}
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 delete">
				<div class="deleteIcon ion-nuclear"></div>
				<h1 class="home-heading">Are you sure you want to delete<br><span class="name">{$title}</span>?</h1>
				<p class="lead">This can not be undone. Please dont cry if everything blows up.</p>
				<form class="pure-form pure-form-stacked" action="." method="post">
					<input type="hidden" name="title" value="{$title}">
					<input type="hidden" name="content" value="{$content}">
					<input type="hidden" name="entryID" value="{$entryID}">
					<button title="Delete Entry" type="submit" name="action" value="delete" class="pure-button outline-inverse">Yes</button>
					<a title="Cancel Deletion" href="?page=entries" class="pure-button outline-inverse">No</a>
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

Displays the default view of the home page, with text and buttons to direct the user to use the site. Or display important information.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
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
				<h1 class="home-heading">Online <span class="name">Journal</span>.</h1>
				<p class="lead">Welcome to iammenasco.com. Feel free to create a journal, and store many entries forever! If you are lost, check out some links on the site to get started</p>
				<p class="lead"><a title="Create an Account" class="pure-button outline-inverse" href="/?page=signUp">Start!</a></p>
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
About Page

Displays the about page, with content about the site, and links to information about me.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createAbout($footer) {
	return <<<HTML
	<div class="about">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="aboutCol"><h2>About this <span class="name">site</span>.</h2>
			<p>I am <span class="name">Menasco</span> is a place for you to keep an online journal. With constant development, I am making sure that users are being presented with the best experience possible, while maintaing a high level of security.</p>
			<p>This site has been created by <a class="name" title="Brian Menasco's profile" href="http://portfolio.iammenasco.com">Brian Menasco</a>.</p>
			</div>
			<div class="aboutCol">
				<h2>About <span class="name">Menasco</span>.</h2>
				<p>As a student at Brigham Young University - Idaho, I have a deep passion for web development. It is my major, career, and hobby.</p>
				<p>I graduate April 2015, and will be available to hire then! Check out my <a class="name" href="http://portfolio.iammenasco.com" title="Portfolio">portfolio</a> to see my work as a front and back end web developer.</p>
			</div>
		</div>
		<h1>Contact <span class="name">Menasco</span>.</h1>
		<div class="pure-g">
			<a title="See my code on GitHub" class="icon" href="https://github.com/iammenasco"><div class="ion-social-github"></div></a>
			<a title="Be a stalker on FaceBook" class="icon" href="https://www.facebook.com/eldermenasco"><div class="ion-social-facebook"></div></a>
			<a title="Get updates on Twitter" class="icon" href="https://twitter.com/iammenasco"><div class="ion-social-twitter"></div></a>
			<a title="Offer me a job on LinkedIn" class="icon" href="https://www.linkedin.com/pub/brian-menasco/50/b2b/491"><div class="ion-social-linkedin"></div></a>
		</div>
		<div class="pure-u-1">
			{$footer}
		</div>
	</div>
HTML;
}
/************
About Page
**** END ****/

/**** START ****
Features Page

List of screen shots and explanations of the site, and how to use it.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createFeatures($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Under <span class="name">Construction</span>.</h1>
				<p class="lead">Patience you must have, my young padawan. Things are changing up a bit. Frequent updates are on the way, check some of them out at the <span class="name">&beta;</span>eta page.</p>
				<p class="lead"><a class="pure-button outline-inverse" href="/?page=signIn">See the future.</a></p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Features Page
**** END ****/

/**** START ****
Support Page

Displays list of QA and other helpful items for use of the site.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createSupport($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Under <span class="name">Construction</span>.</h1>
				<p class="lead">Patience you must have, my young padawan. Things are changing up a bit. Frequent updates are on the way, check some of them out at the <span class="name">&beta;</span>eta page.</p>
				<p class="lead"><a class="pure-button outline-inverse" href="/?page=signIn">See the future.</a></p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Support Page
**** END ****/

/**** START ****
Settings Page

Displays list settings the user can change and edit for their account.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createSettings($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Under <span class="name">Construction</span>.</h1>
				<p class="lead">Patience you must have, my young padawan. Things are changing up a bit. Frequent updates are on the way, check some of them out at the <span class="name">&beta;</span>eta page.</p>
				<p class="lead"><a class="pure-button outline-inverse" href="/?page=signIn">See the future.</a></p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Settings Page
**** END ****/

/**** START ****
Admin Page

Displays administrative pages for the site, including user privileges.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createAdmin($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">Under <span class="name">Construction</span>.</h1>
				<p class="lead">Patience you must have, my young padawan. Things are changing up a bit. Frequent updates are on the way, check some of them out at the <span class="name">&beta;</span>eta page.</p>
				<p class="lead"><a class="pure-button outline-inverse" href="/?page=signIn">See the future.</a></p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Admin Page
**** END ****/

/**** START ****
Sign in

Creates the sign in page, with the specific fields.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the signIn page.
****************/
function createSignIn($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<form class="pure-form pure-form-aligned signIn" action="." method="post">
			<fieldset>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input id="email" required class="form" name="email" type="email" placeholder="Email Address">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" required type="password" name="password" placeholder="Password">
				</div>
			</fieldset>
			<div class="pure-controls">
				<label for="cb" class="pure-checkbox">
					<input id="cb" type="checkbox" class="remember"> Will you remember me?
				</label>
				<button title="Sign in" type="submit" name="action" value="signIn" class="pure-button outline-inverse"><span class="entryIcon ion-icecream"></span>Go</button>
				<p><a title="Create new Account" class="name" href="./?page=signUp">Sign Up... </a></p>
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

Creates the signUp page. Items in the form are sent via POST and validated on the back end.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the signUp page.
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
					<input id="email" type="email" placeholder="Email Address" name="email">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" type="password" placeholder="Password" name="password">
				</div>
				<div class="pure-control-group">
					<label for="password2">Confirm Password</label>
					<input id="password2" type="password" placeholder="Password" name="password2">
				</div>
			</fieldset>
			<div class="pure-controls">
				<button title="Create new account and Log in" type="submit" name="action" value="register" class="pure-button outline-inverse signUp">Submit</button>
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

@return - HTML of the footer.
****************/
function createFooter() {
	return <<<HTML
	<div class="mastfoot">
		<div class="inner">
			<p>See more on my <a title="Visit my Portfolio" href="http://portfolio.iammenasco.com">Portfolio</a>, by <a title="Get updates on Twitter" href="https://twitter.com/iammenasco">@iammenasco</a>.</p>
			<a title="View my teaching presentations" href="http://beta.iammenasco.com/foreach">Teaching Presentation! </a>
			<a title="These guys make this possible" href="http://www.arvixe.com" target="_blank">Hosted By Arvixe</a>
		</div>
	</div>
HTML;
}
/************
Footer
**** END ****/
?>