<?php
	$r = opendir(".");
    while (false !== ($file = readdir($r))) {
    	if(!is_dir ($file)){
        	$c = file_get_contents($file);
        	$o = iconv("GBK", "UTF-8", $c);
        	
        	if($o){
        		file_put_contents($file, $o);
        	}
        }
    }
?>
