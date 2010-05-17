<?php
	require_once('login.php');
	require_once('transfer.php');
	require_once('build.php');

	sleep(6 * 3600);
	touch(CATAPULT_BUSY_FILE);
	sleep(5 * 60);
	
	// 87477 Home
	// 79451 Start
	switch_village(87477);
	
	system("php catapult.php");
	
	unlink(CATAPULT_BUSY_FILE);

	echo "Done.\n";
?>