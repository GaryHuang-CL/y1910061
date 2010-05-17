<?php

include("db.php");

$sql = "DELETE FROM `ally_reports` WHERE DATE_ADD(`timestamp`, INTERVAL 30 day) < NOW()";
if(!mysql_query($sql)) die(mysql_error());

?>
