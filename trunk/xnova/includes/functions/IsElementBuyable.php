<?php

/**
 * IsElementBuyable.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// Checks whether an item is auyable at the time requested
// $CurrentUser   -> Le Joueur lui meme
// $CurrentPlanet -> La planete sur laquelle l'Element doit etre construit
// $Element       -> L'Element que l'on convoite
// $Incremental   -> true  for a building or research
//                -> false for a defense or vessel
// $ForDestroy    -> false par defaut pour une construction
//                -> true pour calculer la demi valeur du niveau en cas de destruction
//
// Reponse        -> boolean (oui / non)
function IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false) {
	global $pricelist, $resource;

	if (IsVacationMode($CurrentUser)){
		return false;
	}

	if ($Incremental) {
		$level  = ($CurrentPlanet[$resource[$Element]]) ? $CurrentPlanet[$resource[$Element]] : $CurrentUser[$resource[$Element]];
	}

	$RetValue = true;
	$array    = array('metal', 'crystal', 'deuterium', 'energy_max');

	foreach ($array as $ResType) {
		if (isset($pricelist[$Element][$ResType]) && $pricelist[$Element][$ResType] != 0) {
			if ($Incremental) {
				$cost[$ResType]  = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
			} else {
				$cost[$ResType]  = floor($pricelist[$Element][$ResType]);
			}

			if ($ForDestroy) {
				$cost[$ResType]  = floor($cost[$ResType] / 2);
			}

			if ($cost[$ResType] > $CurrentPlanet[$ResType]) {
				$RetValue = false;
			}
		}
	}
	return $RetValue;
}

?>