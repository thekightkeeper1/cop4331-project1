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

    // Verifying that id is a number
	if (!is_numeric($inData['id'])) {
        returnWithError('Field "id" must be an integer > 0.', 400);
        exit();
    }

	// Updating the contact
	$sql = "UPDATE contacts
			SET firstname = :firstname, lastname = :lastname, email = :email, phone = :phone
			WHERE id = :id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':firstname', $inData['firstName'], PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':lastname', $inData['lastName'], PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':email', $inData['email'], PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':phone', $inData['phone'], PDO::PARAM_STR); // Must use bindParam to set type. Can't be inside the execute statement
	$stmt->bindParam(':id', $inData['id'], PDO::PARAM_INT); // Must use bindParam to set type. Can't be inside the execute statement
	if ($stmt->execute()) {
		returnWithInfo(200);
	} else {
		returnWithError("Unknown error occurred.", 400);
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResponse($obj, $code)
	{
		http_response_code($code);
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError($err, $code)
		{
			// 1. Create an associative array
			$retValue = [
				"error" => $err,
			];

			// 2. Convert the array to a JSON string
			$jsonResponse = json_encode($retValue);
			sendResponse($jsonResponse, $code);
		}
	
	function returnWithInfo($code)
	{
		$retValue = [
			"error" => "",
		];
		sendResponse(json_encode($retValue), $code );
	}
	
	function isMissingParameter($inputJson) {
		try {
			// Requires that the posted json has at least the keys in expected. 
			$expected = array_flip(['id', "firstName", "lastName", "email", "phone"]);
	
			$missingKeys = array_diff_key($expected, $inputJson);
			return count($missingKeys) > 0;
		} catch (Error $e) {
			returnWithError("Malformed json.");
			exit();
		}
	}

?>
