<?php

try {
	require $_SERVER['DOCUMENT_ROOT'] . '/connections/iammenasco.php';
} catch (Exception $e) {
	header('location: /errordocs/500.php');
	exit;
}

function registerUser($firstName, $lastName, $email, $password) {
	$con = con();
	$sql = 
	'INSERT INTO users (userFirstName, userLastName, userEmail, userPassword)
	VALUES (:userFirstName, :userLastName, :userEmail, :userPassword);';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':userFirstName', $firstName);
	$stmt->bindParam(':userLastName', $firstName);
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

function newEntry($userID, $title, $content) {
	$con = con();
	$sql = 
	'INSERT INTO entries (entryCreatedBy, entryTitle, entryContent)
	VALUES (:entryCreatedBy, :entryTitle, :entryContent);';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entryCreatedBy', $userID);
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

function updateEntry($userID, $entryID, $title, $content) {
	$con = con();
	$sql = 
	'UPDATE entries 
		SET entryTitle = :entryTitle, entryContent = :entryContent
			WHERE entryCreatedBy = :entryCreatedBy
			AND entryID = :entryID;';
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
				WHERE entryCreatedBy = :entryCreatedBy;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entryCreatedBy', $userID);
	$stmt->execute();
	$result = $stmt->fetchAll();
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
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function currentUser($userID) {
	$con = con();
	$query = 'SELECT * FROM users
				WHERE userID = :userID;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':userID', $userID);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

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
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}
?>