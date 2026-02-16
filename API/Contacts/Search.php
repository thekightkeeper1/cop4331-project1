<?php

	require_once '../db_config.php';

    # Get the post request body
	$inData = getRequestInfo();
	if (isMissingParameter($inData)) {
		returnWithError('Missing or incorrect json keys.');
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
	var_dump(($stmt));

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
		// Requires that the posted json has at least the keys in expected. 
		$expected = array_flip(['userId', 'query']);

		$missingKeys = array_diff_key($expected, $inputJson);
		return count($missingKeys) > 0;
	}

?>
