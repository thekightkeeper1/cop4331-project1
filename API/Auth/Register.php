<?php

	require_once '../db_config.php';
	require_once '../utils.php';

    # Get the post request body
	$inData = getRequestInfo();

	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.', 400);
		exit();
	}

    # Variables for the register details
	$newId = 0; 

	// $sql = sprintf("select id from users where username = %s", $inData["userName"]);
	// # Checking that the user does not exist
	$sql = "SELECT id FROM users WHERE username = :uname";  // This is a sql string with placeholders will data will go
	$stmt = $pdo->prepare($sql);  // starts the sql connection with our string
	$stmt->execute([  // This populates the placeholders with acctual data
		'uname' => $inData['userName'],
	]);

	if ($stmt->fetch(PDO::FETCH_ASSOC)) {
		returnWithError("Username already exists", 409);
		exit();
	}

 
	# Preparing the query with placeholders
	$sql = "INSERT INTO users (firstname, lastname, username, password) VALUES (:fname, :lname, :uname, :pass)";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeLholders
	$worked = $stmt->execute([
		'fname' => $inData['firstName'],
		'lname' => $inData['lastName'],
		'uname' => $inData["userName"],
		'pass' => $inData['password'],
	]);

	if ($worked) {
		returnWithInfo($pdo->lastInsertId(), 200);
	} else
	{
		returnWithError("Unknown error occurred.", 400);
	}

	# Closing the cursor we used. Not necessary unless we didn't read all of the rows.
	// $stmt->closeCursor(); #todo remove this?

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj, $code )
	{
		http_response_code($code);
		header('Content-type: application/json');
		echo $obj;
	}
	


	function returnWithError($err, $code)
{
		$retValue = [
			"id"    => 0,
			"error" => $err
		];

    $jsonResponse = json_encode($retValue);

    sendResultInfoAsJson($jsonResponse, $code);
}
	
	function returnWithInfo($id, $code)
	{
		$retValue = [
			"id" => $id,
			"error" => "",
		];
		sendResultInfoAsJson( json_encode($retValue), $code);
	}
	
	function isMissingParameter($inputJson) {
		// Requires that the posted json has at least the keys in expected. 
		$expected = array_flip(['firstName', 'lastName', 'userName', 'password']);

		$missingKeys = array_diff_key($expected, $inputJson);
		return count($missingKeys) > 0;
	}

?>
