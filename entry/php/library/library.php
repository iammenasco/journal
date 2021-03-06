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
	$_SESSION['admin'] = $userArray['userAdmin'];
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
function testInput($data, $type) {
	switch ($type) {
		case 'string':
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			$data = filter_var(trim($data), FILTER_SANITIZE_STRING);
			break;
		case 'email':
			$data = filter_var(filter_var(trim($data), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
			break;
		case 'int':
			$data = filter_var(filter_var(trim($data), FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
			break;
		case 'float':
			$data = filter_var(trim($data), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$data = filter_var($data, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
			break;
		default:
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			break;
	}
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
function createNav($loggedIn, $lastName, $alertCount, $view, $admin) {
	$cmsNav = checkNav($view);
	if ($loggedIn) {
		$userItems = '
		<li class="pure-menu-heading">Journals</li>
		<li class="' . active($view, 'entries') . '"><a title="Entries" href="?page=entries"><span class="navIcon ion-document-text"></span>Entries</a></li>
		<li class="' . active($view, 'logOut') . '"><a title="Log Out" href="?page=logOut"><span class="navIcon ion-log-out"></span>Log out</a></li>
		<li class="' . active($view, 'settings') . '"><a title="User Settings" href="?page=settings"><span class="navIcon ion-ios7-gear"></span>Settings</a></li>';
		if ($admin) {
			$userItems .= '<li class="' . active($view, 'admin') . '"><a title="Site Administration" href="?page=admin"><span class="navIcon ion-settings"></span>Admin</a></li>';
		}
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
			<a title="Home" class="pure-menu-heading" href="/">I am <span class="name">' .  $lastName . '</span>.</a>
			<ul id="std-menu-items">
				<li class="menu-item-divided' . active($view, 'about') . '"><a title="About" href="?page=about"><span class="navIcon ion-ios7-information"></span>About</a></li>
				<li class="' . active($view, 'features') . '"><a title="Features" href="?page=features"><span class="navIcon ion-lightbulb"></span>Features</a></li>
				<li class="' . active($view, 'support') . '"><a title="Support" href="?page=support"><span class="navIcon ion-ios7-help"></span>Support</a></li>
				<li class="' . active($view, 'news') . '"><a title="News" href="?page=news"><span class="navIcon ion-ios7-paper"></span>News</a></li>'
				. $cmsNav . $userItems .
				'<li>
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
Check nav

Adds the user created CMS pages to the Nav. It first runs a function to see if the page is currently active, and then renders the Link, with the correct title, class, and url.

@param $view - The current view of the page. Used to send to the active() function, for highlighting active pages on the nav.

@return - Returns <li>s of all the active Nav links.
****************/
function checkNav($view) {
	$links = '';
	$items = getCMSNav();
	foreach ($items as $item) {
		$title = $item['pageNav'];
		$class = $item['pageClass'];
		$url = $item['pageURL'];
		$links .= '<li class="' . active($view, $url) . '"><a title="' . $title . '" href="?page=' . $url . '"><span class="navIcon ' . $class . '"></span>' . $title . '</a></li>';
	}
	return $links;
}
/************
Check nav
**** END ****/

/**** START ****
Create CMS page

For pages that were created within the CMS, this function will take the requested page from the DB, and put everything in the right spot for the page.

@param $page - Array of the page, and all its content.
@param $footer - HTML of the Footer to be added at the bottom of the page.

@return - HTML of the requested CMS page.
****************/
function createCMSPage($page, $footer) {
	$title = $page['pageTitle'];
	$desc = $page['pageDesc'];
	$content = $page['pageContent'];
	$link = $page['pageLink'];
	$text = $page['pageButton'];
	if ($link != '' or $link != NULL) {
		$button = '<p class="lead"><a class="pure-button outline-inverse" href="' . $link . '">' . $text . '</a></p>';
	} else {
		$button = '';
	}
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1 construction">
				<h1 class="home-heading">{$title}</h1>
				<h2>{$desc}</h2>
				<p class="lead">{$content}</p>
				{$button}
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Create CMS page
**** END ****/

/**** START ****
Create News

Utilizes the GitHub API. For each commit, create a short version of what changed, who did it, and when.

@param $footer - HTML of the Footer to be added at the bottom of the page.

@return - HTML for news page to give updates for each commit.
****************/
function createNews($footer) {
	$list = '';
	$url = 'https://api.github.com/repos/iammenasco/journal/commits';
	// $curl_handle=curl_init();
	// curl_setopt($curl_handle, CURLOPT_URL, $url);
	// curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	// curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	// curl_setopt($curl_handle, CURLOPT_USERAGENT, 'I am Menasco');
	// $query = curl_exec($curl_handle);
	// curl_close($curl_handle);
	$result = file_get_contents($url);
	$json = json_decode($result,true);
	foreach ($json as $commit) {
		$authorName = $commit['commit']['author']['name'];
		$authorEmail = $commit['commit']['author']['email'];
		$commitDate = date("g:ia, F jS, Y",strtotime($commit['commit']['author']['date']));
		$message = $commit['commit']['message'];
		$url = $commit['html_url'];
		$titlePosition = strpos($message, PHP_EOL);
		if ($commit['author']['avatar_url']) {
			$avatar = $commit['author']['avatar_url'];
		} else {
			$avatar = 'http://www.gravatar.com/avatar/c8c1467507a042f49ab30024e6e7f6d9?s=32';
		}
		$title = '';
		$description = '';
		if (!$titlePosition) {
			$title = $message;
		} else {
			$messageLength = strlen($message);
			$title = substr($message, 0, $titlePosition);
			$description = substr($message, $titlePosition, $messageLength);
		}
		$list .= <<<HTML
		<div class="pure-u-1 commit">
			<h1 class="home-heading">{$title}</h1>
			<h3><img class="commit-avatar" title="{$authorName}" alt="{$authorName}&amp;#x27;s avatar" height="32" width="32" src="{$avatar}"> Changes by <span class="name">{$authorName}</span> at {$commitDate}</h3>
			<p><a class="pure-button outline-inverse changes" title="View changes for {$title}" href="{$url}"><span class="changeIcon ion-fork-repo"></span>View Changes</a>{$description}</p>
		</div>
HTML;
	}
	return <<<HTML
	<div class="news">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			{$list}
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Create News
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
	if ($entries) {
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
	if ($entries) {
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
						<a title="Create a New entry" href="?page=new" class="pure-button edit outline-inverse"><span class="entryIcon ion-plus"></span>New</a>
						<a title="Edit current entry" href="?page=new&amp;entry={$entry['entryID']}" class="pure-button edit outline-inverse"><span class="entryIcon ion-edit"></span>Edit</a>
						<a title="Delete this entry" href="?page=delete&amp;entry={$entry['entryID']}" class="pure-button edit outline-inverse"><span class="entryIcon ion-trash-a"></span>Delete</a>
					</div>
				</div>
				<div class="entry-content-body">{$content}</div>
				{$footer}
			</div>
HTML;
			$i++;
		}
	} else {
		$list = <<<HTML
			<div class="entry-content tab-pane active">
				{$footer}
			</div>
HTML;
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
			$title = '<label for="title">Title</label><textarea required="required" type="text" id="title" rows="1" placeholder="Title" name="title">' . $entryTitle .'</textarea>';
		} else {
			$title = '<input type="hidden" name="title" value="' . $entryTitle .'">';
		}

		if ($template['templateTime'] == 1) {
			$time = $displayTime;
		} else {
			$time = '';
		}

		if ($template['templateContent'] == 1) {
			$content = '<label for="entry">Entry</label><textarea required="required"  id="entry" rows="15" cols="50" placeholder="Entry" name="content">' . $entryContent . '</textarea>';
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
Delete User

Warning view if the user wants to delete the current user. userID is sent through the back end, as well as the POST to validate they are deleting their account only.

@param $userID - ID of the currently logged in user.
@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the delete page used for the view.
****************/
function createDeleteUser($userID, $footer) {
	if(isset($_GET['user'])) {
		$urlUserID = $_GET['user'];
		$user = selectUser($userID);
		$firstName = $user['userFirstName'];
		$lastName = $user['userLastName'];
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
				<h1 class="home-heading">Are you sure you want to delete<br><span class="name">{$firstName} {$lastName}</span>'s account?</h1>
				<p class="lead">This can not be undone. Please dont cry if everything blows up.</p>
				<form class="pure-form pure-form-stacked" action="." method="post">
					<input type="hidden" name="urlUserID" value="{$urlUserID}">
					<button title="Delete User" type="submit" name="action" value="deleteUser" class="pure-button outline-inverse">Yes</button>
					<a title="Cancel Deletion" href="?page=settings" class="pure-button outline-inverse">No</a>
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
			<div class="pure-u-1">
				<h1 class="home-heading">Features.</h1>
				<p class="lead name">Create a journal.</p>
				<p>You have come to the right place if you want to keep an online journal. Just sign up, and start making history!</p>
				<p class="lead name">Edit/Delete an entry.</p>
				<p>Made a mistake? Have memories you want redacted? Do it.</p>
				<p class="lead name">Colors.</p>
				<p>Don’t like the standard <span class=“name”>I am Menasco</span>. blue? Change it in your settings.</p>
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
			<div class="pure-u-1">
				<h1 class="home-heading">Support.</h1>
				<p class="lead">If you find something <span class="name">broken</span>, <a href="mailto:menasco+site@me.com?Subject=Site%20busted" target="_top">email me</a> and I will fix it.</p>
				<p class="lead">If you need help with <span class="name">I am Menasco</span>, <a href="mailto:menasco+site@me.com?Subject=Site%20help" target="_top">email me</a> and I can help you.</p>
				<p class="lead">If you want a good <span class="name">joke</span>, <a href="mailto:menasco+site@me.com?Subject=Humor%20me" target="_top">email me</a> and I can try.</p>
				<p class="lead">Anything else, ask your <span class="name">mother</span>.</p>
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
Site Plan Page

List a bunch of useless requirements that the site has.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createSitePlan($footer) {
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1">
				<h1 class="home-heading">Site <span class="name">Plan</span>.</h1>
				<p class="lead"><span class="name">Purpose</span>.</p>
				<p>I am Menasco is an online journaling website for people to create entries for themselves and store them online to be accessible anywhere at anytime.</p>
				<p class="lead"><span class="name">Audience</span>.</p>
				<p>Anyone with computer skills, and a desire to journal it up!</p>
				<p class="lead">Use <span class="name">Case</span>.</p>
				<p>Male - <span class="name">Dave</span>. 25 years old. Student in sports medicine. <span class="name">Dave</span> reads many books and needs a place to keep a study journal. He hates paper, and wants a place to store everything online so he can use it from his tablet, phone, and computer. Atta boy, <span class="name">Dave</span>.</p>
				<p>Female - <span class="name"><span class="name">LaQuisha</span></span>. 16 years old. Highschooler with a desire to be a singer. <span class="name">LaQuisha</span> write songs and poems. She current does not have a stable place since she moves around a lot. She wants a place to store her lyrical masterpieces online so they don’t get lost as she goes here and there. Write on, <span class="name">LaQuisha</span>.</p>
				<p class="lead">Site <span class="name">Scenarios</span>.</p>
					<ol>
						<li>Grade my final website/follow my commits and changes</li>
						<li>Keep a journal</li>
						<li>Change their settings</li>
						<li>Create an account</li>
						<li>Edit/Create new entries on their journal</li>
						<li>Delete their account (Don't be that guy)</li>
					</ol>
				<p class="lead">Site <span class="name">Map</span>.</p>
				<p>Oh, its beautiful. <a href="http://iammenasco.com/images/sitemap.pdf">Check it out here</a>.</p>
				<p class="lead">Site <span class="name">Assets</span>.</p>
				<p>I literally have no idea what you are talking about here. So here is a list of every file to make my site possible.</p>
				<img src="http://iammenasco.com/images/asset.png" alt="List of Assets" height="500">
				<p class="lead">Style<span class="name">Guide</span>.</p>
				<p>Typography - Helvetica at various sizes, boldness, and colors.</p>
				<p>Color - Default is #369DDA on a blackish background. Can be changed to various colors (See ‘theme’ in the Asset List)</p>
				<p>Navigation - Left Nav that is always there, unless you are looking at this from a phone. Then its hidden… but still there.</p>
				<p>Responsive - Yes. Large screen is the full experience. Tablet is the full experience, just smaller. Phone is a condensed experience. Entries will change the most on these different views. Instead of being side by side, they will be stacked with the Nav hidden and accessible upon toggle.</p>
			</div>
			<div class="pure-u-1">
				{$footer}
			</div>
		</div>
	</div>
HTML;
}
/************
Site Plan Page
**** END ****/

/**** START ****
Settings Page

Displays list settings the user can change and edit for their account.

@param $footer - HTML of the Footer to be added at the bottom of each Entry.

@return - HTML of the home page.
****************/
function createSettings($footer) {
	$firstName = $_SESSION['firstName'];
	$lastName = $_SESSION['lastName'];
	$email = $_SESSION['email'];
	$userID = $_SESSION['userID'];
	$fullName = $firstName . ' ' . $lastName;
	$currentScheme = $_SESSION['color'];
	$currentTheme = $_SESSION['theme'];
	if ($currentTheme == Null or $currentTheme == '') {
		$dark = 'checked';
		$light = '';
	} else if ($currentTheme == 'dark') {
		$dark = 'checked';
		$light = '';
	} else if ($currentTheme == 'light') {
		$dark = '';
		$light = 'checked';
	}
	return <<<HTML
	<div class="main">
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1">
				<h1 class="settingsHeading">Change {$fullName}'s <span class="name">Settings</span>.</h1>
			</div>
			<form class="pure-form pure-form-aligned settings" action="." method="post">
				<fieldset>
					<h3 class="settingsHeading">Personal <span class="name">Information</span>.</h3>
					<div class="pure-control-group">
						<label for="firstName">First Name</label>
						<input id="firstName" placeholder="{$firstName}" value="{$firstName}" name="firstName">
					</div>
					<div class="pure-control-group">
						<label for="lastName">Last Name</label>
						<input id="lastName"  placeholder="{$lastName}" value="{$lastName}" name="lastName">
					</div>
					<div class="pure-control-group">
						<label for="email">Email Address</label>
						<input id="email" type="email" placeholder="{$email}" value="{$email}" name="email">
					</div>
				</fieldset>
				<fieldset>
					<h3 class="settingsHeading">Change <span class="name">Password</span>.</h3>
					<div class="pure-control-group">
						<label for="newPassword">New Password</label>
						<input id="newPassword" type="password" placeholder="New Password" name="newPassword">
					</div>
				</fieldset>
				<fieldset>
					<h3 class="settingsHeading">Change <span class="name">Theme</span>.</h3>
					<div class="pure-control-group">
							<input type="radio" name="theme" value="dark" {$dark}> Dark Theme<br>
							<input type="radio" name="theme" value="light" {$light}> Light Theme
					</div>
					<div class="pure-control-group">
						<label for="scheme">Color Scheme</label>
						<select id="scheme" title="Select a Color theme" name="scheme">
							<option value="blue">Blue</option>
							<option value="lime">Green</option>
							<option value="orange">Orange</option>
							<option value="pink">Pink</option>
							<option value="purple">Purple</option>
							<option value="red">Red</option>
							<option value="white">White</option>
							<option value="yellow">Yellow</option>
						</select>
					</div>
				</fieldset>
				<div class="pure-controls">
					<button title="Save Settings" type="submit" name="action" value="settings" class="pure-button outline-inverse"><span class="entryIcon ion-ios7-locked"></span>Save</button>
				</div>
				<div class="pure-controls">
				<h3 class="settingsHeading">Danger <span class="name">Zone</span>.</h3>
				<a title="Delete this entry" href="?page=deleteUser&amp;user={$userID}" class="pure-button outline-inverse"><span class="entryIcon ion-ios7-minus"></span>Delete Account</a>
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
	<div class="admin">
		<script>
			function showUser(str) {
				if (str.length==0) { 
					document.getElementById("userList").innerHTML="";
					return;
				}
				var xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("userList").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","php/library/getUsers.php?q="+str,true);
				xmlhttp.send();
			}
		</script>
		<script>
			function showAllUsers(str) {
				var xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("userList").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","php/library/getUsers.php?a="+str,true);
				xmlhttp.send();
			}
		</script>
		<script>
			function showPage(str) {
				if (str.length==0) { 
					document.getElementById("userList").innerHTML="";
					return;
				}
				var xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("pageList").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","php/library/getPages.php?q="+str,true);
				xmlhttp.send();
			}
		</script>
		<script>
			function showAllPages(str) {
				var xmlhttp=new XMLHttpRequest();
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("pageList").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","php/library/getPages.php?a="+str,true);
				xmlhttp.send();
			}
		</script>
		<div class="header">
			<h1>I am <span class="name">Menasco</span>.</h1>
			<h2>A magical place of hope and wonder</h2>
		</div>
		<div class="pure-g">
			<div class="pure-u-1">
				<h1 class="settingsHeading">Edit Site <span class="name">Settings</span>.</h1>
			</div>
			<div class="pure-form pure-form-aligned settings">
				<fieldset>
					<h3 class="settingsHeading">Manage <span class="name">Users</span>.</h3>
					<div class="pure-control-group">
						<label for="search">Search Users</label>
						<input id="search" onkeyup="showUser(this.value)" type="search" name="search">
					</div>
				</fieldset>
				<div class="pure-controls">
					<button title="Show all users" class="pure-button outline-inverse" onclick="showAllUsers(this.value)" value="true"><span class="entryIcon ion-ios7-people"></span>Show all Users</button>
				</div>
			</div>
			<div class="pure-u-1">
				<div class="userList" id="userList"></div>
			</div>
			<div class="pure-form pure-form-aligned settings">
				<fieldset>
					<h3 class="settingsHeading">Edit/Create <span class="name">Pages</span>.</h3>
					<div class="pure-control-group">
						<label for="search">Search Pages</label>
						<input id="search" onkeyup="showPage(this.value)" type="search" name="search">
					</div>
				</fieldset>
				<div class="pure-controls">
					<button title="Show all users" class="pure-button outline-inverse" onclick="showAllPages(this.value)" value="true"><span class="entryIcon ion-ios7-copy"></span>Show all Pages</button>
				</div>
			</div>
			<div class="pure-u-1">
				<div class="pageList" id="pageList"></div>
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
					<input required="required" type="text" id="firstName" placeholder="First Name" name="firstName">
				</div>
				<div class="pure-control-group">
					<label for="lastName">Last Name</label>
					<input required="required" type="text" id="lastName"  placeholder="Last Name" name="lastName">
				</div>
			</fieldset>
			<fieldset>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input required="required" id="email" type="email" placeholder="Email Address" name="email">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input required="required" id="password" type="password" placeholder="Password" name="password">
				</div>
				<div class="pure-control-group">
					<label for="password2">Confirm Password</label>
					<input required="required" id="password2" type="password" placeholder="Password" name="password2">
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
			<a title="View my site plan" href="?page=plan">Site Plan! </a>
			<a title="These guys make this possible" href="http://www.arvixe.com" target="_blank">Hosted By Arvixe</a>
		</div>
	</div>
HTML;
}
/************
Footer
**** END ****/
?>