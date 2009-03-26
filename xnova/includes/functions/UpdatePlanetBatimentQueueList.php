<?php

/**
 * UpdatePlanetBatimentQueueList.php
 *
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */

function UpdatePlanetBatimentQueueList ( &$CurrentPlanet, &$CurrentUser ) {
	$RetValue = false;
	$now = time();
	
	if ( $CurrentPlanet['b_building_id'] != 0 && $CurrentPlanet['b_building'] <= $now) {
		begin_transaction();
		
		$CurrentPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '".$CurrentPlanet['id']."' FOR UPDATE;", 'planets', true);
		
		while ( $CurrentPlanet['b_building_id'] != 0 ) {
			assert($CurrentPlanet['b_building']);
			if ( $CurrentPlanet['b_building'] <= $now ) {
				PlanetResourceUpdate ( $CurrentUser, $CurrentPlanet, $CurrentPlanet['b_building'], false );
				
				/*$IsDone =*/
				CheckPlanetBuildingQueue( $CurrentPlanet, $CurrentUser );
/*
				if ( $IsDone == true ) {
					SetNextQueueElementOnTop ( $CurrentPlanet, $CurrentUser );
					BuildingSavePlanetRecord ( $CurrentPlanet );
				}
*/
			} else {
				$RetValue = true;
				break;
			}
		}
		
		commit();
	}
	return $RetValue;
}

?>