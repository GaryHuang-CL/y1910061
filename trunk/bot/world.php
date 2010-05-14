<?php
if($argc < 2) die("please give  server parameter");
$server = $argv[1];

include("db.php");

$dumpfile = dirname(__FILE__) . "/map.sql";
$tblname = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd');
$tblname_yesterday = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 48);
$tblname_idle_village = "idle_villages_" . str_replace(".", "_", $server);
$tblname_very_old = "x_world_" . str_replace(".", "_", $server) . "_" . date('ymd', time() - 3600 * 120);

unlink($dumpfile); 

$cmd = "wget http://$server/map.sql.gz -O " . $dumpfile . ".gz";
echo $cmd . "\n";
system($cmd);

$cmd = "gzip -d $dumpfile";
echo $cmd . "\n";
system($cmd);

$sql = "DROP TABLE IF EXISTS $tblname ";
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

// update crops information
@mysql_query("BEGIN");
$sql = "
UPDATE y1910061_travian.crops a, y1910061_travian.servers c
 SET a.player = NULL, a.ally = NULL, a.pop = NULL, a.village_name= NULL
WHERE a.server = c.id and c.name = '$server'
";

echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "
UPDATE y1910061_travian.crops a, y1910061_traviandb.$tblname b, y1910061_travian.servers c
 SET a.player = b.player, a.ally = b.alliance, a.pop = b.population, a.village_name= b.village
WHERE a.server = c.id and c.name = '$server' and a.d = b.id
";

echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

@mysql_query("COMMIT");

// create idle villages table
$sql = "DROP TABLE IF EXISTS $tblname_idle_village ";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "
CREATE TABLE $tblname_idle_village AS
SELECT a.*
FROM $tblname a
INNER JOIN
(
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
) idle_players
ON a.uid = idle_players.uid
";

echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "ALTER TABLE $tblname_idle_village ADD PRIMARY KEY (`id`)";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

$sql = "DROP TABLE IF EXISTS $tblname_very_old";
echo $sql . "\n";
if(!mysql_query($sql)) die(mysql_error());

?>
