
<?php

	require_once '../db_config.php';

    # Get the post request body
	if (!valid_body()) {
		returnWithError("Body was not valid JSON syntax.", 400);
	}
	$inData = getRequestInfo();
	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.', 400);
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
		returnWithInfo( 200, $user['firstName'], $user['lastName'], $user['ID'] );
	}
	else
	{
		returnWithError( "No Records Found", 401);
	}


	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj, $code)
	{
		http_response_code($code);
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err, $code )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue,  $code);
	}
	
	function returnWithInfo($code, $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue, $code );
	}


	// Some error checking functions
	function isMissingParameter($inputJson) {
		// Requires that the posted json has at least the keys in expected. 
		$expected = array_flip(['userName', 'password']);

		$missingKeys = array_diff_key($expected, $inputJson);
		return count($missingKeys) != 0;
	}

		function valid_body() {
		$json = file_get_contents("php://input");
		return json_validate($json);
	}
?>
