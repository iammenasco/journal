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

function newEntry($user, $title, $content) {
	$con = con();
	$sql = 
	'INSERT INTO entries (entriesCreatedBy, entriesTitle, entriesContent)
	VALUES (:entriesCreatedBy, :entriesTitle, :entriesContent);';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entriesCreatedBy', $user);
	$stmt->bindParam(':entriesTitle', $title);
	$stmt->bindParam(':entriesContent', $content);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function updateEntry($user, $entry, $title, $content) {
	$con = con();
	$sql = 
	'UPDATE entries 
		SET entriesTitle = :entriesTitle, entriesContent = :entriesContent
			WHERE entriesCreatedBy = :entriesCreatedBy
			AND entriesID = :entriesID;';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entriesCreatedBy', $user);
	$stmt->bindParam(':entriesID', $entry);
	$stmt->bindParam(':entriesTitle', $title);
	$stmt->bindParam(':entriesContent', $content);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function deleteEntry($user, $entry, $title, $content) {
	$con = con();
	$sql = 
	'DELETE FROM entries 
		WHERE entriesCreatedBy = :entriesCreatedBy
		AND entriesID = :entriesID;
		AND entriesTitle = :entriesTitle
		AND entriesContent = :entriesContent;';
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':entriesCreatedBy', $user);
	$stmt->bindParam(':entriesID', $entry);
	$stmt->bindParam(':entriesTitle', $title);
	$stmt->bindParam(':entriesContent', $content);
	$stmt->execute();
	$insertResult = $con->lastInsertId();
	$stmt->closeCursor();

	if ($insertResult) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function listAll($user) {
	$con = con();
	$query = 'SELECT * FROM entries
				WHERE entriesCreatedBy = :entriesCreatedBy;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entriesCreatedBy', $user);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}

function listSingle($user, $entry) {
	$con = con();
	$query = 'SELECT * FROM entries
				WHERE entriesCreatedBy = :entriesCreatedBy
				AND entriesID = :entriesID;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entriesCreatedBy', $user);
	$stmt->bindParam(':entriesID', $entry);
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
	ON users.userID = entries.entriesCreatedBy
	WHERE entriesCreatedBy=:entriesCreatedBy;';
	$stmt = $con->prepare($query);
	$stmt->bindParam(':entriesCreatedBy', $entryID);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$stmt->closeCursor();
	return $result;
}
?>