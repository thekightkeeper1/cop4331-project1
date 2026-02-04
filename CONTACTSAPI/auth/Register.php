<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();

    # Variables for the register details
	$newId = 0;

	// # Checking that the user does not exist
	// $sql = "SELECT id FROM users WHERE username = ";

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
		returnWithInfo($pdo->lastInsertId());
	} else
	{
		returnWithError("Unknown error occurred.");
	}

	# Closing the cursor we used. Not necessary unless we didn't read all of the rows.
	// $stmt->closeCursor(); #todo remove this?

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	


	function returnWithError($err)
	{
    // 1. Create an associative array
    $retValue = [
        "id"    => 0,
        "error" => $err
    ];

    // 2. Convert the array to a JSON string
    $jsonResponse = json_encode($retValue);

    sendResultInfoAsJson($jsonResponse);
	}
	
	function returnWithInfo($id)
	{
		$retValue = [
			"id" => $id,
			"error" => "",
		];
		sendResultInfoAsJson( json_encode($retValue) );
	}
	
?>
