<?php

/**
 * BuildingSavePlanetRecord.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function BuildingSavePlanetRecord ( $CurrentPlanet ) {

	// Enregistrement des divers changements dans les tables
	$QryUpdatePlanet  = "UPDATE {{table}} SET ";
	$QryUpdatePlanet .= "`metal` = '".         $CurrentPlanet['metal']         ."' , ";
	$QryUpdatePlanet .= "`crystal` = '".       $CurrentPlanet['crystal']       ."' , ";
	$QryUpdatePlanet .= "`deuterium` = '".     $CurrentPlanet['deuterium']     ."' , ";
	$QryUpdatePlanet .= "`last_update` = '".   $CurrentPlanet['last_update']   ."' , ";
	$QryUpdatePlanet .= "`b_building_id` = '". $CurrentPlanet['b_building_id'] ."' , ";
	$QryUpdatePlanet .= "`b_building` = '".    $CurrentPlanet['b_building']    ."'   ";
	$QryUpdatePlanet .= "WHERE ";
	$QryUpdatePlanet .= "`id` = '".            $CurrentPlanet['id']            ."';";
	doquery( $QryUpdatePlanet, 'planets');

	return;
}

?>