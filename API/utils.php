	<?php


		function valid_body() {
		$json = file_get_contents("php://input");
		return json_validate($json);
	}