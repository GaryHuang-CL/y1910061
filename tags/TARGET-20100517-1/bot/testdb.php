<?php
    function table_exist($table_name)
	{
		$res = mysql_query("SHOW TABLES LIKE '$table_name'");
		if($res){
			$row = mysql_fetch_row($res);
			
			if($row && $row[0] == $table_name){
				return true;
			}
		}
		return false;
	}
	
    include("db.php");    
    
    
    // load the map.sql via system command using "wget" into the folder data/
    // IMPORTANT: PHP has to be allowed to write into that folder, if necessary set the needed rights!
    $sqldump = "tmp.sql";
    system('wget http://s1.travian.jp/map.sql.gz -O ' . $sqldump . '.gz');
    system('gzip -d ' . $sqldump . '.gz');

	if(table_exist('x_world')){
		echo "Drop table x_world.\n";
		mysql_query("DROP TABLE x_world");
	}
	
	
    // Check whether the file has been downloaded and is larger than zero bytes
    if (file_exists($sqldump) AND filesize($sqldump)) {
        
        // Empty table
        $query = "CREATE TABLE `x_world` (
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
			  `population` smallint(5) unsigned NOT NULL default '0'
			);";
		
        $result = @mysql_query($query);
        
        // Exceute map.sql using the programme "mysql"
        // IMPORTANT: The charset "latin1" has to be used for T2 game worlds (if there should be any left with that version)
        system('mysql --host=localhost --user=root --password= --default-character-set=utf8 test < ' . $sqldump);
        mysql_query("RENAME TABLE x_world TO x_world_" . date('Y_m_d'));
        echo 'Update finished!';
        
    } else {
        
        echo 'Failed downloading map.sql or file is empty!';

    }
    
    // In case the temporary file exists it will be deleted
    //if (file_exists($sqldump)) {
    //    unlink($sqldump);
    //}
    
    // Close database connection
    @mysql_close($db);

?> 