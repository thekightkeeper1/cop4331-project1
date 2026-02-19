<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();
    

    // insert contact into list at user id
    $sql = "INSERT INTO contacts (firstName, lastName, email, phone, userId) 
            VALUES (:firstName, :lastName, :email, :phone, :userId)";
    $stmt = $pdo->prepare($sql);

    $worked = $stmt->execute([      
        'firstName' => $inData["firstName"], 
        'lastName'  => $inData["lastName"],
        'email'     => $inData["email"],
        'phone' => $inData["phone"],
        'userId' => $inData['userId']
    ]);
	if ($worked) {
		returnWithInfo("Person Added");
	} else
	{
		returnWithError("Incorrect info");
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
