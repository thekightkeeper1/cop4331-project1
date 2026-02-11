
<?php


	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();
	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.');
		exit();
	}

    # Variables for the register details
	$id = 0;
	$firstName = "";
	$lastName = "";

	# Preparing the query with placeholders
	$sql = "SELECT ID,firstName,lastName FROM users WHERE username= :userName AND password = :password";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeholders
	$stmt->execute([
		'userName' => $inData["userName"],
		'password'=> $inData["password"],
	]);
	// $stmt->debugDumpParams();


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

	function isMissingParameter($inputJson) {
		// Requires that the posted json has at least the keys in expected. 
		$expected = array_flip(['userName', 'password']);

		$missingKeys = array_diff_key($expected, $inputJson);
		return count($missingKeys) != 0;
	}
	
?>
