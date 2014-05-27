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
$email = "menasco@me.com";

// Create function to render the entry List on load, and then load the content when clicked.

$entryID = 'entry' . '1';
$entrySubject = 'Read this to keep you safe.';
// First X characters from entry body
$entrySnip = 'Lets talk. There are some features I would like to point... ';
$entryTime = '1:29pm, May 24th, 2014';
$entryContent = <<<HTML
<p>Lets talk. There are some features I would like to point out since I worked my butt of making it possible. First off, you might have recognized that the site you are now using is responsive. Second, each of the three parts of the screen scroll separately. Why is this awesome? Well, how often are you going to need to scroll the Left Navigation? Never, if I do my job right. But lets say you have 80 entries. You can scroll those willy nilly, while the content on the right (this box) stays in one place. You can happily do whatever you want. Happy?</p>
<p>Not yet, huh? There is more. Personally, I like the blue. I call it Menasco blue. <span class="name">#368DDA</span>, feels like home. You can change it if you want. I built a drop down selection of various themes on the left. I gave it some colors that look beautiful.</p>
<p>Still reading? If you are adventurous, you might have noticed that some things dont work. Thats intentional. This is just a basic mock-up of what I want it to look like. The first two items on the left will change what you see in this box. The other ones wont, because I am too lazy. Also, you might recognize the blue line on the side of the second entry. I am thinking about flagging entries like that if someone comments or shares or does something special with it.</p>
<p>Let me know what you think so far.</p>
HTML;
$loggedIn = isset($_GET['loggedIn']);
if (isset($_GET['view'])) {
	$view = $_GET['view'];
} else {
	$view = '';
}
// $loggedIn = true;
$lastName = 'Menasco';
$firstName = 'Brian';
$entryName = $firstName . ' ' . $lastName;
$alertCount = 2;
$userID = 1;
/************
Test Content
**** END ****/

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
function createNav($loggedIn, $lastName, $alertCount) {
	$userItems = '';
	if ($loggedIn) {
		$userItems = <<<HTML
			<li class="pure-menu-heading">Journals</li>
			<li><a href="#">New Entry</a></li>
			<li><a href="#">Alerts <span class="name entry-count">{$alertCount}</span></a></li>
			<li><a href="#">Families</a></li>
			<li><a href="#">Share</a></li>
			<li class="pure-menu-heading">Account</li>
			<li><a href="#">Switch Journal</a></li>
			<li><a href="#">Edit Journal</a></li>
			<li class="pure-menu-heading"></li>
			<li><a class="pure-menu-heading" href="?view=home">Log out</a></li>
			<li><a href="#">Admin</a></li>
HTML;
} else {
	$userItems = <<<HTML
	<li class="pure-menu-heading"></li>
	<li><a class="pure-menu-heading" href="?view=signIn">Log in</a></li>
HTML;
}
	return <<<HTML
	
		<a href="#menu" id="menuLink" class="menu-link">
			<span></span>
		</a>
		<div id="menu">
			<div class="pure-menu pure-menu-open">
				<a class="pure-menu-heading" href="http://iammenasco.com">I am <span class="name">{$lastName}</span>.</a>
				<ul id="std-menu-items">
					<li class="menu-item-divided pure-menu-selected"><a href="#">Home</a></li>
					<li><a href="#">About</a></li>
					<li><a href="#">Features</a></li>
					<li><a href="#">Support</a></li>
					{$userItems}
					<li><select class="menu-select" onChange="loadCSS(this.value);">
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
		</div>
	
HTML;
}
/************
Create nav
**** END ****/

/**** START ****
Get avatar

Use Gravatar. Maybe just as an option. Users can opt-out, but use this service as default
****************/
function getAvatar($entryName, $email) {
	$default = "http://www.gravatar.com/avatar/c8c1467507a042f49ab30024e6e7f6d9?s=64";
	$url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default ) . "&s=64";
	return <<<HTML
	<img class="entry-avatar" alt="{$entryName}&#x27;s avatar" height="64" width="64" src="{$url}">
HTML;
}
/************
Get avatar
**** END ****/

/**** START ****
Create entry list

Populate a list of 10 most current entries with "Show More" button, or Lazy Load all in Desktop View
****************/

function entryList($entryID, $entryName, $avatar, $entrySubject, $entrySnip) {
	return <<<HTML
	<li class="active"><a href="#entry1" data-toggle="tab">
		<div class="entry-item pure-g">
			<div class="pure-u">
				{$avatar}
			</div>
			<div class="pure-u-3-4">
				<h5 class="entry-name">{$entryName}</h5>
				<h4 class="entry-subject">{$entrySubject}</h4>
				<p class="entry-desc">{$entrySnip}</p>
			</div>
		</div>
	</a></li>

	<li><a href="#entry2" data-toggle="tab">
		<div class="entry-item entry-item-unread pure-g">
			<div class="pure-u">
				{$avatar}
			</div>
			<div class="pure-u-3-4">
				<h5 class="entry-name">{$entryName}</h5>
				<h4 class="entry-subject">{$entrySubject}</h4>
				<p class="entry-desc">{$entrySnip}</p>
			</div>
		</div>
	</a></li>
HTML;
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
function entryContent($entryID, $entrySubject, $entryName, $entryTime, $entryContent, $footer) {
	return <<<HTML
	<div class="entry-content tab-pane active" id="entry1">
		<div class="entry-content-header pure-g">
			<div class="pure-u-1-2">
				<h1 class="entry-content-title">{$entrySubject}</h1>
				<p class="entry-content-subtitle">From <a>{$entryName}</a> at <span>{$entryTime}</span>
				</p>
			</div>
			<div class="entry-content-controls pure-u-1-2">
				<button class="pure-button outline-inverse">Edit</button>
				<button class="pure-button outline-inverse">New</button>
				<button class="pure-button outline-inverse">Share</button>
			</div>
		</div>
		<div class="entry-content-body">{$entryContent}</div>
		{$footer}
	</div>

	<div class="entry-content tab-pane" id="entry2">
		<div class="entry-content-header pure-g">
			<div class="pure-u-1-2">
				<h1 class="entry-content-title">{$entrySubject}</h1>
				<p class="entry-content-subtitle">From <a>{$entryName}</a> at <span>{$entryTime}</span>
				</p>
			</div>
			<div class="entry-content-controls pure-u-1-2">
				<button class="pure-button outline-inverse">Edit?</button>
				<button class="pure-button outline-inverse">New</button>
				<button class="pure-button outline-inverse">Share</button>
			</div>
		</div>
		<div class="entry-content-body">{$entryContent}</div>
		{$footer}
	</div>
HTML;
}
/************
Create entry content
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
				<button type="signUp" class="pure-button outline-inverse signUp">Sign up</button>
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
		<form class="pure-form pure-form-aligned signIn">
			<fieldset>
				<div class="pure-control-group">
					<label for="firstName">First Name</label>
					<input id="firstName" placeholder="First Name">
				</div>
				<div class="pure-control-group">
					<label for="lastName">Last Name</label>
					<input id="lastName"  placeholder="Last Name">
				</div>
			</fieldset>
			<fieldset>
				<div class="pure-control-group">
					<label for="email">Email Address</label>
					<input id="email" placeholder="Email Address">
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input id="password" type="password" placeholder="Password">
				</div>
				<div class="pure-control-group">
					<label for="password2">Repeat</label>
					<input id="password2" type="password" placeholder="Password">
				</div>
			</fieldset>
			<div class="pure-controls">
				<button type="signUp" class="pure-button outline-inverse signUp">Sign up</button>
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
$nav = createNav($loggedIn, $lastName, $alertCount);
$footer = createFooter();
$avatar = getAvatar($entryName ,$email);
$home = createHome($footer);
$signIn = createSignIn($footer);
$signUp = createSignUp($footer);
$body = '<div class="pure-g">';
$body .= '<ul class="pure-1 entryList nav-tabs">';
$body .= entryList($entryID, $entryName, $avatar, $entrySubject, $entrySnip);
$body .= '</ul><div class="pure-1 entry tab-content">';
$body .= entryContent($entryID, $entrySubject, $entryName, $entryTime, $entryContent, $footer);
$body .= '</div>';
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