<?php

try {
	require $_SERVER['DOCUMENT_ROOT'] . '/connections/iammenasco.php';
} catch (Exception $e) {
	header('location: /errordocs/500.php');
	exit;
}

function logIn($email, $password) {
	$con = con();
	$sql = 
	'SELECT * FROM users WHERE userEmail = :userEmail and userPassword = :userPassword';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':userEmail', $email);
	$stmt->bindParam(':userPassword', $password);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $result;
}

function registerUser($firstName, $lastName, $email, $password) {
	$con = con();
	$sql = 
	'INSERT INTO users (userFirstName, userLastName, userEmail, userPassword)
	VALUES (:userFirstName, :userLastName, :userEmail, :userPassword);';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':userFirstName', $firstName);
	$stmt->bindParam(':userLastName', $lastName);
	$stmt->bindParam(':userEmail', $email);
	$stmt->bindParam(':userPassword', $password);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function newEntry($userID, $title, $content, $url, $start, $end, $templateID) {
	$con = con();
	$sql = 
	'INSERT INTO entries (entryCreatedBy,entryTitle, entryContent, entryURL, entryStartTime, entryEndTime, entryTemplateID)
	VALUES (:entryCreatedBy, :entryTitle, :entryContent, :entryURL, :entryStartTime, :entryEndTime, :entryTemplateID);';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->bindParam(':entryTitle', $title);
	$stmt->bindParam(':entryContent', $content);
	$stmt->bindParam(':entryURL', $url);
	$stmt->bindParam(':entryStartTime', $start);
	$stmt->bindParam(':entryEndTime', $end);
	$stmt->bindParam(':entryTemplateID', $templateID);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function updateEntry($userID, $entryID, $title, $content, $url, $start, $end, $templateID) {
	$con = con();
	$sql = 
	'UPDATE entries 
		SET entryTitle = :entryTitle, entryContent = :entryContent, entryURL = :entryURL, entryStartTime = :entryStartTime, entryEndTime = :entryEndTime, entryTemplateID = :entryTemplateID
			WHERE entryCreatedBy = :entryCreatedBy
			AND entryID = :entryID;';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->bindParam(':entryID', $entryID);
	$stmt->bindParam(':entryTitle', $title);
	$stmt->bindParam(':entryContent', $content);
	$stmt->bindParam(':entryURL', $url);
	$stmt->bindParam(':entryStartTime', $start);
	$stmt->bindParam(':entryEndTime', $end);
	$stmt->bindParam(':entryTemplateID', $templateID);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function deleteEntry($userID, $entryID, $title, $content) {
	$con = con();
	$sql = 
	'DELETE FROM entries 
		WHERE entryCreatedBy = :entryCreatedBy
		AND entryID = :entryID;
		AND entryTitle = :entryTitle
		AND entryContent = :entryContent;';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->bindParam(':entryID', $entryID);
	$stmt->bindParam(':entryTitle', $title);
	$stmt->bindParam(':entryContent', $content);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function listAll($userID) {
	$con = con();
	$query = 'SELECT * FROM entries
				WHERE entryCreatedBy = :entryCreatedBy
				ORDER BY entryTime DESC;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $result;
}

function listSingle($userID, $entryID) {
	$con = con();
	$query = 'SELECT * FROM entries
				WHERE entryCreatedBy = :entryCreatedBy
				AND entryID = :entryID;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->bindParam(':entryID', $entryID);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $result;
}

// function currentUser($userID) {
// 	$con = con();
// 	$query = 'SELECT * FROM users
// 				WHERE userID = :userID;';
// 	$stmt = $con->prepare($query);
// 	$stmt->bindParam(':userID', $userID);
// 	$stmt->execute();
// 	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 	$stmt->closeCursor();
// 	return $result;
// }

function entryName($entryID) {
	$con = con();
	$query = 'SELECT users.userFirstName, users.userLastName
	FROM users
	INNER JOIN entries
	ON users.userID = entries.entryCreatedBy
	WHERE entryCreatedBy=:entryCreatedBy;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entryCreatedBy', $entryID);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $result;
}

function getTemplates() {
	$con = con();
	$query = 'SELECT * FROM templates';
	$stmt = $con->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $result;
}
?>