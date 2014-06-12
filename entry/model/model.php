<?php

try {
	require $_SERVER['DOCUMENT_ROOT'] . '/connections/iammenasco.php';
} catch (Exception $e) {
	header('location: /errordocs/500.php');
	exit;
}

function logIn($email, $password) {
	$con = con();
	try {
		$sql = 
		'SELECT * FROM users WHERE userEmail = :userEmail and userPassword = :userPassword';
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':userEmail', $email, PDO::PARAM_STR);
		$stmt->bindParam(':userPassword', $password, PDO::PARAM_STR);
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

function listAll($userID) {
	$con = con();
	try {
		$query = 'SELECT * FROM entries
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

function listSingle($userID, $entryID) {
	$con = con();
	try {
		$query = 'SELECT * FROM entries
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
	try {
		$query = 'SELECT users.userFirstName, users.userLastName
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
?>