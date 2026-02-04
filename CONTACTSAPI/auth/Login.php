
<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();

    # Variables for the register details
	$id = 0;
	$firstName = "";
	$lastName = "";

	# Preparing the query with placeholders
	$sql = "SELECT ID,firstName,lastName FROM users WHERE Login= :username AND Password = :pass";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeholders
	$stmt->execute([
		'username' => $inData["username"],
		'pass'=> $inData["password"],
	]);


	# Resolving the data into a associative array format
	if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
		returnWithInfo( $user['firstName'], $user['lastName'], $user['ID'] );
	}
	else
	{
		returnWithError("No Records Found");
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
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
