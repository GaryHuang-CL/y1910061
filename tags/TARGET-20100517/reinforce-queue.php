<?php
	require_once('login.php');
	require_once('transfer.php');
	require_once('build.php');

	sleep(3 * 3600 + 1200);
	touch(CATAPULT_BUSY_FILE);
	sleep(5 * 60);
	
	// 87477 Home
	// 79451 Start
	switch_village(87477);
	
	system("php reinforce.php");
	
	unlink(CATAPULT_BUSY_FILE);

	echo "Done.\n";
?>