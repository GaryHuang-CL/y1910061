<?php

/**
 *
 * AddBuildingToQueue.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// Adds a building in the queue
// $CurrentPlanet -> Planete sur laquelle on construit
// $CurrentUser   -> Current player
// $Element       -> Building a construire
//
// Retour         -> Value of the inserted element
//                   or false if it can not insert (queue full)
//
function AddBuildingToQueue ( &$CurrentPlanet, $CurrentUser, $Element, $AddMode = true) {
	global $lang, $resource;

	$CurrentQueue  = $CurrentPlanet['b_building_id'];
	if ($CurrentQueue != 0) {
		$QueueArray    = explode ( ";", $CurrentQueue );
		$ActualCount   = count ( $QueueArray );
	} else {
		$QueueArray    = "";
		$ActualCount   = 0;
	}

	if ($AddMode == true) {
		$BuildMode = 'build';
	} else {
		$BuildMode = 'destroy';
	}

	if ( $ActualCount < MAX_BUILDING_QUEUE_SIZE ) {
		$QueueID      = $ActualCount + 1;
	} else {
		$QueueID      = false;
	}

	if ( $QueueID != false ) {
		// Must check if the item you want to integrate is already in the table!
		if ($QueueID > 1) {
			$InArray = 0;
			for ( $QueueElement = 0; $QueueElement < $ActualCount; $QueueElement++ ) {
				$QueueSubArray = explode ( ",", $QueueArray[$QueueElement] );
				if ($QueueSubArray[0] == $Element) {
					$InArray++;
				}
			}
		} else {
			$InArray = 0;
		}

		if ($InArray != 0) {
			$ActualLevel  = $CurrentPlanet[$resource[$Element]];
			if ($AddMode == true) {
				$BuildLevel   = $ActualLevel + 1 + $InArray;
				$CurrentPlanet[$resource[$Element]] += $InArray;
				$BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
				$CurrentPlanet[$resource[$Element]] -= $InArray;
			} else {
				$BuildLevel   = $ActualLevel - 1 + $InArray;
				$CurrentPlanet[$resource[$Element]] -= $InArray;
				$BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element) / 2;
				$CurrentPlanet[$resource[$Element]] += $InArray;
			}
		} else {
			$ActualLevel  = $CurrentPlanet[$resource[$Element]];
			if ($AddMode == true) {
				$BuildLevel   = $ActualLevel + 1;
				$BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
			} else {
				$BuildLevel   = $ActualLevel - 1;
				$BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element) / 2;
			}
		}

		if ($QueueID == 1) {
			$BuildEndTime = time() + $BuildTime;
		} else {
			$PrevBuild = explode (",", $QueueArray[$ActualCount - 1]);
			$BuildEndTime = $PrevBuild[3] + $BuildTime;
		}
		$QueueArray[$ActualCount]       = $Element .",". $BuildLevel .",". $BuildTime .",". $BuildEndTime .",". $BuildMode;
		$NewQueue                       = implode ( ";", $QueueArray );
		$CurrentPlanet['b_building_id'] = $NewQueue;
	}
	return $QueueID;
}

?>