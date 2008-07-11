<?php
	require_once("common.php");

	if(file_exists(BUSY_FILE)){
		echo("Busy.");
	}else{
		echo("Done.");
	}
?>