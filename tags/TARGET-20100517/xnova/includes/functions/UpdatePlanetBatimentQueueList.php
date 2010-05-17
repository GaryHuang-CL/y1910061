<?php

/**
 * UpdatePlanetBatimentQueueList.php
 *
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */

//function UpdatePlanetBatimentQueueList ( &$CurrentPlanet, &$CurrentUser ) {
function UpdatePlanetBatimentQueueList ( $planetid ) {
	$RetValue = false;
	$now = time();
	
	begin_transaction();
	
	$CurrentPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '" . $planetid . "' FOR UPDATE", 'planets', true);
	if(!$CurrentPlanet || $CurrentPlanet['b_building'] == 0 || $CurrentPlanet['b_building'] > $now){
		rollback();
		return false;
	}
	
	$CurrentUser = doquery("SELECT * FROM {{table}} WHERE `id` = '" . $CurrentPlanet['id_owner'] ."' LOCK IN SHARE MODE", 'users', true);
	if(!$CurrentUser) return false;
	
	PlanetResourceUpdate ( $CurrentUser, $CurrentPlanet, $CurrentPlanet['b_building'], false );			
	CheckPlanetBuildingQueue( $CurrentPlanet, $CurrentUser );
	
	commit();
}

?>