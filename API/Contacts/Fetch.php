<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();
	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.');
		exit();
	}

    // Verifying that cacheSize is a number
	if (!is_numeric($inData['cacheSize'])) {
        returnWithError('Cache must be an integer > 0.');
        exit();
    }


	# Preparing the query with placeholders
	
	echo $cacheSize;
	$sql = "SELECT * FROM contacts WHERE UserID = :userid LIMIT :cacheSize;";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeLholders
	$stmt->bindParam(':userid', $inData['id'], PDO::PARAM_STR); // Must use bindParam to set type. Can't be dont inside the execute statement
	$stmt->bindParam(':cacheSize', $inData['cacheSize'], PDO::PARAM_INT); // Must use bindParam to set type. Can't be dont inside the execute statement
	$stmt->execute();

    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }
    // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    returnWithInfo($result);

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
	
	function returnWithInfo($rows)
	{
		$retValue = [
			"results" => $rows,
			"error" => "",
		];
		sendResultInfoAsJson( json_encode($retValue) );
	}
	
	function isMissingParameter($inputJson) {
		try {
			// Requires that the posted json has at least the keys in expected. 
			$expected = array_flip(['id', 'cacheSize']);
	
			$missingKeys = array_diff_key($expected, $inputJson);
			return count($missingKeys) > 0;
		} catch (Error $e) {
			returnWithError("Malformed json.");
			exit();
		}
	}

?>
