<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();

    # Variables for the register details
	$id = 0;
	$firstName = "";
	$lastName = "";
    $email = "";

	// # find table to insert into
	$sql = "SELECT * FROM users WHERE username = :uname";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([
		'username' => $inData['username'],
	]);
	if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
		returnWithError("No user found");
		exit();
	}

	# Preparing the query with placeholders
	$sql = "INSERT INTO contacts (firstName, lastName, email) 
            VALUES (:firstName, :lastName, :email)";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeholders
	$worked = $stmt->execute([
		'firstName' => $inData["firstName"], 
        'lastName'  => $inData["lastName"],
        'email'     => $inData["email"],
	]);

	if ($worked) {
		returnWithInfo("Person Added");
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
