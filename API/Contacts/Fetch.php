<?php

	require_once '../db_config.php';
	require_once '../utils.php';

    # Get the post request body
	if (!valid_body()) {
		returnWithError("Body was not valid JSON syntax.", 400);
	}
	$inData = getRequestInfo();
	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.', 400);
		exit();
	}

    // Verifying that cacheSize is a number
	if (!is_numeric($inData['cacheSize'])) {
        returnWithError('Cache must be an integer > 0.', 400);
        exit();
    }

	// Getting paging information
	$pages = 0;
	$sql = "SELECT COUNT(*) FROM contacts WHERE UserID = :userid LIMIT :cacheSize";
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
    returnWithInfo($result, 200);

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResponse( $obj, $code)
	{
		http_response_code($code);
		header('Content-type: application/json');
		echo $obj;
	}
	


	function returnWithError($err, $code)
{
    // 1. Create an associative array
    $retValue = [
        "id"    => 0,
        "error" => $err
    ];

    // 2. Convert the array to a JSON string
    $jsonResponse = json_encode($retValue);

    sendResponse($jsonResponse, $code);
}
	
	function returnWithInfo($rows, $code)
	{
		$retValue = [
			"results" => $rows,
			"error" => "",
		];
		sendResponse( json_encode($retValue), $code );
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
