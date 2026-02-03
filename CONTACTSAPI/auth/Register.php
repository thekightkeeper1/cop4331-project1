
<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();

    # Variables for the register details
	$id = 0;
	$firstName = "";
	$lastName = "";

	# Preparing the query with placeholders
	$sql = "INSERT INTO users (firstname, lastname, login, password) VALUES (:fname, :lname, :uname, :pass)";
	$stmt = $pdo->prepare($sql);
	# Running the query and populating placeholders
	$stmt->execute([
		'uname' => $inData["username"],
		'fname' => $inData['password'],
		'lname' => $inData['lastName'],
		'pass' => $inData['password'],
	]);


	# Ex
	if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
		echo "worked";
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
