<?php
/**** START ****
model.php Information

Created by Brian Menasco
This work is licensed under a Creative Commons 
Attribution-NonCommercial-ShareAlike 4.0 International License.

Contains functions used for all database communications

Information Section
**** END ****/

/**** START ****
Connection

Create the connection to the database
****************/
try {
	require $_SERVER['DOCUMENT_ROOT'] . '/connections/iammenasco.php';
} catch (Exception $e) {
	header('location: /500.shtml');
	exit;
}
/************
Connection
**** END ****/

/**** START ****
Log In

Check to see if the given credentials match a row stored in the users table.

@param $email - Sent to check the database and log in the user
@param $password - Sent through md5() and then matched with entries in the database

@return - Will return user information to create session, or FALSE if no match is made.
****************/
function logIn($email) {
	$con = con();
	try {
		$sql = 
		'SELECT * FROM users 
		WHERE userEmail = :userEmail 
		AND userActive = TRUE;';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userEmail', $email, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Log in
**** END ****/

/**** START ****
Register User

Create a new row in the user table, using the given parameters.

@param $firstName - First name of user ex. "Brian"
@param $lastName - Last Name of user ex. "Menasco"
@param $email - Email of user ex. "menasco@me.com"
@param $password - Password that was sent through md5()

@return - If user is registered properly, function will return true. Other wise, it will return false.
****************/
function registerUser($firstName, $lastName, $email, $password) {
	$con = con();
	try {
		$sql = 
		'INSERT INTO users (userFirstName, userLastName, userEmail, userPassword)
		VALUES (:userFirstName, :userLastName, :userEmail, :userPassword);';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userFirstName', $firstName, PDO::PARAM_STR);
		$stmt->bindParam(':userLastName', $lastName, PDO::PARAM_STR);
		$stmt->bindParam(':userEmail', $email, PDO::PARAM_STR);
		$stmt->bindParam(':userPassword', $password, PDO::PARAM_STR);
		$stmt->execute();
		$result = $con->lastInsertId();
		$stmt->closeCursor();
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result >= 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}
/************
Includes
**** END ****/

/**** START ****
New Entry

Create a new entry in the entries table, using the following parameters. Other values not listed here are auto generated or auto incramented through the database.

@param $userID - Unique identifier of each user
@param $title - Title of the new entry
@param $content - Content or body of the new entry
@param $url - URL value as part of the entry.
@param $start - User indicated start time
@param $end - User indicated end time
@param $templateID - ID of the template used for the new entry

@return - If the entry is inserted properly, function will return true. Other wise, it will return false.
****************/
function newEntry($userID, $title, $content, $url, $start, $end, $templateID) {
	$con = con();
	try {
		$sql = 
		'INSERT INTO entries (entryCreatedBy,entryTitle, entryContent, entryURL, entryStartTime, entryEndTime, entryTemplateID)
		VALUES (:entryCreatedBy, :entryTitle, :entryContent, :entryURL, :entryStartTime, :entryEndTime, :entryTemplateID);';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':entryCreatedBy', $userID, PDO::PARAM_INT);
		$stmt->bindParam(':entryTitle', $title, PDO::PARAM_STR);
		$stmt->bindParam(':entryContent', $content, PDO::PARAM_STR);
		$stmt->bindParam(':entryURL', $url, PDO::PARAM_STR);
		$stmt->bindParam(':entryStartTime', $start, PDO::PARAM_STR);
		$stmt->bindParam(':entryEndTime', $end, PDO::PARAM_STR);
		$stmt->bindParam(':entryTemplateID', $templateID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $con->lastInsertId();
		$stmt->closeCursor();
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result >= 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}
/************
New Entry
**** END ****/

/**** START ****
Update Entry

Update an existing entry, matching the userID and entryID with content in the entries table.

@param $userID - Unique identifier of each user
@param $entryID - ID of the specific entry the user is updating
@param $title - Title of the entry
@param $content - Content or body of the entry
@param $url - URL value as part of the entry.
@param $start - User indicated start time
@param $end - User indicated end time
@param $templateID - ID of the template used for the entry

@return - If the entry is updated properly, function will return true. Other wise, it will return false.
****************/
function updateEntry($userID, $entryID, $title, $content, $url, $start, $end, $templateID) {
	$con = con();
	try {
		$sql = 
		'UPDATE entries 
		SET entryTitle = :entryTitle, entryContent = :entryContent, entryURL = :entryURL, entryStartTime = :entryStartTime, entryEndTime = :entryEndTime, entryTemplateID = :entryTemplateID
		WHERE entryCreatedBy = :entryCreatedBy
		AND entryID = :entryID;';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':entryCreatedBy', $userID, PDO::PARAM_INT);
		$stmt->bindParam(':entryID', $entryID, PDO::PARAM_INT);
		$stmt->bindParam(':entryTitle', $title, PDO::PARAM_STR);
		$stmt->bindParam(':entryContent', $content, PDO::PARAM_STR);
		$stmt->bindParam(':entryURL', $url, PDO::PARAM_STR);
		$stmt->bindParam(':entryStartTime', $start, PDO::PARAM_STR);
		$stmt->bindParam(':entryEndTime', $end, PDO::PARAM_STR);
		$stmt->bindParam(':entryTemplateID', $templateID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Update Entry
**** END ****/

/**** START ****
Delete Entry

Delete an entry given the specific entryID, also matching entryTitle and entryContent which are all passed through the POST of the page. For extra security, userID is sent to ensure that user B can not delete user A's entries.

@param $userID - Unique identifier of each user
@param $entryID - ID of the specific entry the user is deleting
@param $title - Title of the entry
@param $content - Content or body of the entry

@return - If the entry is updated properly, a number will be returned as $result. Other wise, it will return false.
****************/
function deleteEntry($userID, $entryID, $title, $content) {
	$con = con();
	try {
		$sql = 
		'DELETE FROM entries 
		WHERE entryCreatedBy = :entryCreatedBy
		AND entryID = :entryID;
		AND entryTitle = :entryTitle
		AND entryContent = :entryContent;';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':entryCreatedBy', $userID, PDO::PARAM_INT);
		$stmt->bindParam(':entryID', $entryID, PDO::PARAM_INT);
		$stmt->bindParam(':entryTitle', $title, PDO::PARAM_STR);
		$stmt->bindParam(':entryContent', $content, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Delete Entry
**** END ****/

/**** START ****
List All Entries

List all entries from the user with the given userID.

@param $userID - Unique identifier of each user

@return - Return an array of rows from the entries table, which contains each column of that table. If the query fails, then it returns false.
****************/
function listAll($userID) {
	$con = con();
	try {
		$query = 
		'SELECT * FROM entries
		WHERE entryCreatedBy = :entryCreatedBy
		ORDER BY entryTime DESC;';
		$stmt = $con->prepare($query);
		$stmt->bindParam(':entryCreatedBy', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
List All Entries
**** END ****/

/**** START ****
List Single Entry

List one and only one entry given that matches the given entryID.

@param $userID - Unique identifier of each user
@param $entryID - ID of the specific entry the user is requesting

@return - Returns one row, which contains each column of that row stored in an array. If it is unsuccessful, it returns false.
****************/
function listSingle($userID, $entryID) {
	$con = con();
	try {
		$query = 
		'SELECT * FROM entries
		WHERE entryCreatedBy = :entryCreatedBy
		AND entryID = :entryID;';
		$stmt = $con->prepare($query);
		$stmt->bindParam(':entryCreatedBy', $userID, PDO::PARAM_INT);
		$stmt->bindParam(':entryID', $entryID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
List Single Entry
**** END ****/

/**** START ****
Show created by User Name

Used to find the first name and last name of the author of the specific entry.

@param $entryID - ID of the specific entry the user is requesting

@return - Returns userFirstName and userLastName in an array. If it fails, or there is no match, it returns false.
****************/
function entryName($entryID) {
	$con = con();
	try {
		$query = 
		'SELECT users.userFirstName, users.userLastName
		FROM users
		INNER JOIN entries
		ON users.userID = entries.entryCreatedBy
		WHERE entryCreatedBy=:entryCreatedBy;';
		$stmt = $con->prepare($query);
		$stmt->bindParam(':entryCreatedBy', $entryID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Shoe created by User Name
**** END ****/

/**** START ****
Get all Templates

Select all templates in the templates table

@return - Returns each template in an array and the boolean value if the column is present in the template or not. If it fails, it returns false.
****************/
function getTemplates() {
	$con = con();
	try {
		$query = 'SELECT * FROM templates';
		$stmt = $con->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Get all Templates
**** END ****/

/**** START ****
Valid page

Checks to see if the page was created and exists within the database

@param $page - Page requested for the view

@return - Returns true if the page exists, and false if it is not there.
****************/
function validPage($page) {
	$con = con();
	try {
		$query = 
		'SELECT pageActive
		FROM pages
		WHERE pageActive = TRUE
		AND pageURL = :pageURL;';
		$stmt = $con->prepare($query);
		$stmt->bindParam(':pageURL', $page, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return TRUE;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Valid Page
**** END ****/

/**** START ****
Get Created Page

For pages created from the CMS, it checks the gets the content for the requested page.

@param $page - Page requested for the view

@return - Returns the page content for each page on the CMS. Returns false if it is not there
****************/
function getCreatedPage($page) {
	$con = con();
	try {
		$query = 
		'SELECT * FROM pages
		WHERE pageURL = :pageURL';
		$stmt = $con->prepare($query);
		$stmt->bindParam(':pageURL', $page, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Get Created Page
**** END ****/

/**** START ****
Get Created Page Nav

For each of the created pages from the CMS, it gets the NAV items

@param $page - Page requested for the view

@return - Returns the page content for each page on the CMS. Returns false if it is not there
****************/
function getCMSNav() {
	$con = con();
	try {
		$query = 
		'SELECT pageNav, pageClass, pageURL FROM pages
		WHERE pageActive = TRUE;';
		$stmt = $con->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Get Created Page
**** END ****/

/**** START ****
Update Password

From the settings page, this function will update the current user's password.

@param $newPass - New Password of the user
@param $userID - Unique identifier of the user

@return - If the entry is updated properly, function will return true. Other wise, it will return false.
****************/
function updatePassword($newPass, $userID) {
	$con = con();
	try {
		$sql = 
		'UPDATE users 
		SET userPassword = :userPassword
		WHERE userID = :userID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userPassword', $newPass, PDO::PARAM_STR);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Update Password
**** END ****/

/**** START ****
Update User

Update information on the user, including their name, email, and color settigns.

@param $firstName - First Name of the user
@param $lastName - Last Name of the user
@param $email - Email of the user
@param $theme - Dark or light CSS
@param $scheme - Changes the colors on the page
@param $userID - Unique identifier of each user

@return - If the entry is updated properly, function will return true. Other wise, it will return false.
****************/
function updateUser($firstName, $lastName, $email, $theme, $scheme, $userID) {
	$con = con();
	try {
		$sql = 
		'UPDATE users 
		SET userFirstName = :userFirstName, userLastName = :userLastName, userEmail = :userEmail, userTheme = :userTheme, userColor = :userColor
		WHERE userID = :userID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userFirstName', $firstName, PDO::PARAM_STR);
		$stmt->bindParam(':userLastName', $lastName, PDO::PARAM_STR);
		$stmt->bindParam(':userEmail', $email, PDO::PARAM_STR);
		$stmt->bindParam(':userTheme', $theme, PDO::PARAM_STR);
		$stmt->bindParam(':userColor', $scheme, PDO::PARAM_STR);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Update User
**** END ****/

/**** START ****
Update User Admin

Update information on the user from the Admin, including their name, email, and color settigns.

@param $firstName - First Name of the user
@param $lastName - Last Name of the user
@param $email - Email of the user
@param $admin - Boolean value if the user is an Administrator
@param $active - Boolean value if the user is active
@param $userID - Unique identifier of each user

@return - If the entry is updated properly, function will return true. Other wise, it will return false.
****************/
function updateUserAdmin($firstName, $lastName, $email, $admin, $active, $userID) {
	$con = con();
	try {
		$sql = 
		'UPDATE users 
		SET userFirstName = :userFirstName, userLastName = :userLastName, userEmail = :userEmail, userAdmin = :userAdmin, userActive = :userActive
		WHERE userID = :userID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userFirstName', $firstName, PDO::PARAM_STR);
		$stmt->bindParam(':userLastName', $lastName, PDO::PARAM_STR);
		$stmt->bindParam(':userEmail', $email, PDO::PARAM_STR);
		$stmt->bindParam(':userAdmin', $admin, PDO::PARAM_INT);
		$stmt->bindParam(':userActive', $active, PDO::PARAM_INT);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Update User Admin
**** END ****/

/**** START ****
Select User

After changing user information, get user information to reset session variables.

@param $userID - Unique identifier of the user

@return - Will return user information to create session, or FALSE if no match is made.
****************/
function selectUser($userID) {
	$con = con();
	try {
		$sql = 
		'SELECT * FROM users WHERE userID = :userID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Select User
**** END ****/

/**** START ****
Delete User

This function does not actually delete the user, but flags them as inactive. This way, if there is a mistake, all is not lost forever. Just disabled.

@param $userID - Unique identifier of each user

@return - If the entry is updated properly, function will return true. Other wise, it will return false.
****************/
function deleteUser($userID) {
	$con = con();
	try {
		$sql = 
		'UPDATE users 
		SET userActive = FALSE
		WHERE userID = :userID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Delete User
**** END ****/

/**** START ****
Select Names

Select all users

@return - Return array of all the users.
****************/
function selectNames() {
	$con = con();
	try {
		$sql = 
		'SELECT * FROM users';
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Select Names
**** END ****/

/**** START ****
Select Pages

Select all CMS Pages

@return - Return array of all the users.
****************/
function selectPages() {
	$con = con();
	try {
		$sql = 
		'SELECT * FROM pages';
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (!empty($result)) {
			return $result;
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		return FALSE;
	}
}
/************
Select Pages
**** END ****/

/**** START ****
Update Page

Will change the current values of a page with the content sent from the user

@param $pageIdent - pageID of the current page
@param $pageTitle - Title of the current Page
@param $pageDesc - Description of the current page
@param $pageContent - Body/Content of the page
@param $pageClass - Ionicon class of the navigation icon
@param $pageURL - Navigation URL used for the page
@param $pageNav - Title of the page on the Navigation
@param $pageLink - Link where the button takes the user
@param $PageButton - Text on the button of the page
@param $pageActive - Boolean value if the user is active

@return - If the page is updated properly, function will return true. Other wise, it will return false.
****************/
function updatePage($pageIdent, $pageTitle, $pageDesc, $pageContent, $pageClass, $pageURL, $pageNav, $pageLink, $pageButton, $pageActive) {
	$con = con();
	try {
		$sql = 
		'UPDATE pages 
		SET pageTitle = :pageTitle, pageDesc = :pageDesc, pageContent = :pageContent, pageClass = :pageClass, pageURL = :pageURL, pageNav = :pageNav, pageLink = :pageLink, pageButton = :pageButton, pageActive = :pageActive
		WHERE pageID = :pageID';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':pageTitle', $pageTitle, PDO::PARAM_STR);
		$stmt->bindParam(':pageDesc', $pageDesc, PDO::PARAM_STR);
		$stmt->bindParam(':pageContent', $pageContent, PDO::PARAM_STR);
		$stmt->bindParam(':pageClass', $pageClass, PDO::PARAM_STR);
		$stmt->bindParam(':pageURL', $pageURL, PDO::PARAM_STR);
		$stmt->bindParam(':pageNav', $pageNav, PDO::PARAM_STR);
		$stmt->bindParam(':pageLink', $pageLink, PDO::PARAM_STR);
		$stmt->bindParam(':pageButton', $pageButton, PDO::PARAM_STR);
		$stmt->bindParam(':pageActive', $pageActive, PDO::PARAM_INT);
		$stmt->bindParam(':pageID', $pageIdent, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->rowCount();
		$stmt->closeCursor();
		return $result;
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result) {
		return $result;
	} else {
		return FALSE;
	}
}
/************
Update Page
**** END ****/

/**** START ****
New Page

Create a new page from the Admin view

@param $pageTitle - Title of the new Page
@param $pageDesc - Description of the new page
@param $pageContent - Body/Content of the page
@param $pageClass - Ionicon class of the navigation icon
@param $pageURL - Navigation URL used for the page
@param $pageNav - Title of the page on the Navigation
@param $pageLink - Link where the button takes the user
@param $PageButton - Text on the button of the page
@param $pageActive - Boolean value if the user is active

@return - If the page is inserted properly, function will return true. Other wise, it will return false.
****************/
function newPage($pageTitle, $pageDesc, $pageContent, $pageClass, $pageURL, $pageNav, $pageLink, $pageButton, $pageActive) {
	$con = con();
	try {
		$sql = 
		'INSERT INTO pages (pageTitle, pageDesc, pageContent, pageClass, pageURL, pageNav, pageLink, pageButton, pageActive)
		VALUES (:pageTitle, :pageDesc, :pageContent, :pageClass, :pageURL, :pageNav, :pageLink, :pageButton, :pageActive);';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':pageTitle', $pageTitle, PDO::PARAM_STR);
		$stmt->bindParam(':pageDesc', $pageDesc, PDO::PARAM_STR);
		$stmt->bindParam(':pageContent', $pageContent, PDO::PARAM_STR);
		$stmt->bindParam(':pageClass', $pageClass, PDO::PARAM_STR);
		$stmt->bindParam(':pageURL', $pageURL, PDO::PARAM_STR);
		$stmt->bindParam(':pageNav', $pageNav, PDO::PARAM_STR);
		$stmt->bindParam(':pageLink', $pageLink, PDO::PARAM_STR);
		$stmt->bindParam(':pageButton', $pageButton, PDO::PARAM_STR);
		$stmt->bindParam(':pageActive', $pageActive, PDO::PARAM_INT);
		$stmt->execute();
		$result = $con->lastInsertId();
		$stmt->closeCursor();
	} catch (PDOException $e) {
		return FALSE;
	}
	if ($result >= 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}
/************
New Page
**** END ****/
?>