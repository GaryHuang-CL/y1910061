<?php
include("db.php");

$server = "s3.travian.jp";

$dumpfile = dirname(__FILE__) . "/map.sql";
$tblname = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd');
$tblname_yesterday = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 24);
$tblname_idle_player = "idle_players_" . str_replace(".", "_", $server);

unlink($dumpfile); 

$cmd = "wget http://$server/map.sql.gz -O " . $dumpfile . ".gz";
echo $cmd . "\n";
system($cmd);

$cmd = "gzip -d $dumpfile";
echo $cmd . "\n";
system($cmd);

$sql = "DROP TABLE $tblname IF EXISTS";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "
CREATE TABLE `$tblname` (
  `id` int(9) unsigned NOT NULL default '0',
  `x` smallint(3) NOT NULL default '0',
  `y` smallint(3) NOT NULL default '0',
  `tid` tinyint(1) unsigned NOT NULL default '0',
  `vid` int(9) unsigned NOT NULL default '0',
  `village` varbinary(255) NOT NULL default '',
  `uid` int(9) NOT NULL default '0',
  `player` varbinary(255) NOT NULL default '',
  `aid` int(9) unsigned NOT NULL default '0',
  `alliance` varbinary(255) NOT NULL default '',
  `population` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`))
";

echo "creating table $tblname ...\n";

if(!mysql_query($sql)) die(mysql_error());
    

echo "loading table data ... \n";

$sql = "
LOAD DATA LOCAL INFILE '$dumpfile'
INTO TABLE $tblname
CHARACTER SET utf8
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '\''
LINES STARTING BY 'INSERT INTO `x_world` VALUES ('
TERMINATED BY ');';";

echo "$sql\n";

if(!mysql_query($sql)) die(mysql_error());


$sql = "DROP TABLE $tblname_idle_player IF EXISTS";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "
CREATE TABLE $tblname_idle_player AS 
SELECT m.uid
FROM
	(
	SELECT uid, count(*) x
	FROM $tblname
	GROUP BY uid
	) m,
	(
	SELECT a.uid, count(*) x
	FROM $tblname a, 
	     $tblname_yesterday b
	WHERE a.id = b.id
	  AND a.population = b.population
	GROUP BY a.uid
	) n
WHERE m.uid = n.uid and m.x = n.x
";

echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "ALTER TABLE $tblname_idle_playerd ADD PRIMARY KEY (`uid`);";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

?>
