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

	# Preparing the query with placeholders
	$sql = "SELECT * FROM contacts WHERE UserID = :userid AND firstname LIKE :query";
	$stmt = $pdo->prepare($sql);

	# Running the query and populating placeLholders
	$query = "%" . $inData['query'] . "%";
	$tmp = $stmt->execute([
		'userid' => $inData['userId'],
		'query' => $query,
	]);

    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }
    // $result = $stmt->fetch(PDO::FETCH_ASSOC);
    returnWithInfo($result, 200);



	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResponse( $obj , $code)
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
		// Requires that the posted json has at least the keys in expected. 
		$expected = array_flip(['userId', 'query']);

		$missingKeys = array_diff_key($expected, $inputJson);
		return count($missingKeys) > 0;
	}

?>
