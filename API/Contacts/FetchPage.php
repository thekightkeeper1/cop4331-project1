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
	$cacheSize = intval($inData['cacheSize'], 10);
	$userid = intval($inData['id'], 10);
	$requestedPage = (int) $inData['pageNumber'];

	// Getting paging information
	$offset = $requestedPage * $cacheSize;

	$sql = "SELECT COUNT(*) FROM contacts WHERE UserID = :userid";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':userid', $userid, PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->execute();

	$numRows = intval($stmt->fetchColumn(), 10); 
	$totalPages = ceil($numRows / $cacheSize);
	if ($requestedPage > $totalPages - 1) { // Pages are 0 indexed
		returnWithError('Page is out of bounds', 400, $totalPages);
		exit();
	}

	$sql = "SELECT * FROM contacts WHERE UserID = :userid ORDER BY firstName LIMIT :cacheSize OFFSET :offset;";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':cacheSize', $cacheSize, PDO::PARAM_INT); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':userid', $userid, PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->execute();

    $queryResult = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $queryResult[] = $row;
    }
	
    returnWithInfo($queryResult, $totalPages, 200);

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
	
	function returnWithError($err, $code, $totalPages=-1)
{
    // 1. Create an associative array
    $retValue = [
        "id"    => 0,
        "error" => $err,
		"totalPages" => $totalPages
    ];

    // 2. Convert the array to a JSON string
    $jsonResponse = json_encode($retValue);

    sendResponse($jsonResponse, $code);
}
	
	function returnWithInfo($rows, $totalPages, $code)
	{
		$retValue = [
			"totalPages" => $totalPages,
			"results" => $rows,
			"error" => "",
		];
		sendResponse( json_encode($retValue), $code );
	}
	
	function isMissingParameter($inputJson) {
		try {
			// Requires that the posted json has at least the keys in expected. 
			$expected = array_flip(['id', 'cacheSize', 'pageNumber']);
	
			$missingKeys = array_diff_key($expected, $inputJson);
			return count($missingKeys) > 0;
		} catch (Error $e) {
			returnWithError("Malformed json.");
			exit();
		}
	}

?>
