<?php

function connect()
{
	global $link, $debug, $xnova_root_path;
	if(!$link)
	{
		require($xnova_root_path.'config.php');
		$link = mysql_connect($dbsettings["server"], $dbsettings["user"],$dbsettings["pass"]) or
		$debug->error(mysql_error()."<br />$query","SQL Error");
		mysql_select_db($dbsettings["name"]) or $debug->error(mysql_error()."<br />$query","SQL Error");
		mysql_set_charset('utf8', $link) or $debug->error(mysql_error()."<br />$query","SQL Error");
		echo mysql_error();
	}
}

function begin_transaction()
{
	connect();
	mysql_query("begin") or $debug->error(mysql_error()."<br />$query","SQL Error");
}

function commit()
{
	mysql_query("commit") or $debug->error(mysql_error()."<br />$query","SQL Error");
}

function rollback()
{
	mysql_query("rollback") or $debug->error(mysql_error()."<br />$query","SQL Error");
}

function doquery($query, $table, $fetch = false){
	global $debug, $xnova_root_path;
	require($xnova_root_path.'config.php');

	connect();
	
	$sql = str_replace("{{table}}", $dbsettings["prefix"].$table, $query);


	$sqlquery = mysql_query($sql) or
	$debug->error(mysql_error()."<br />$sql<br />","SQL Error");
	//print(mysql_error()."<br />$query"."SQL Error");


	unset($dbsettings);//se borra la array para liberar algo de memoria

	global $numqueries,$debug;//,$depurerwrote003;
	$numqueries++;
	//$depurerwrote003 .= ;
	$debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$table</th><th>$fetch</th></tr>");

	if($fetch)
	{ //hace el fetch y regresa $sqlrow
		$sqlrow = mysql_fetch_array($sqlquery);
		return $sqlrow;
	}else{ //devuelve el $sqlquery ("sin fetch")
		return $sqlquery;
	}

}



// Created by Perberos. All rights reversed (C) 2006
?>
