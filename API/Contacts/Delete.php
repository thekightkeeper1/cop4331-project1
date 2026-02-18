<?php

    require_once '../db_config.php';

    # Get the post request body
    $inData = getRequestInfo();


    $sql = "DELETE FROM contacts WHERE id = :ids";
    $stmt = $pdo->prepare($sql);

    $worked = $stmt->execute([
        'ids' => $inData["id"],
    ]);

    if ($worked) {
        returnWithInfo("Person removed");
    } else
    {
        returnWithError("ID not found");
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
    
    function returnWithInfo($id)
    {
        $retValue = [
            "id" => $id,
            "error" => "",
        ];
        sendResultInfoAsJson( json_encode($retValue) );
    }
    
?>