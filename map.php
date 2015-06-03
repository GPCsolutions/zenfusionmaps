<?php
/*
 * ZenFusion Maps - A Google Maps module for Dolibarr
 * Copyright (C) 2015 Raphaël Doursenaud    <rdoursenaud@gpcsolutions.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		map.php
 *	\ingroup	zenfusionmaps
 *	\brief		Thirdparties map
 */

$res = 0;
if (! $res && file_exists('../main.inc.php')) {
	$res = @include '../main.inc.php';
}
if (! $res && file_exists('../../main.inc.php')) {
	$res = @include '../../main.inc.php';
}
if (! $res && file_exists('../../../main.inc.php')) {
	$res = @include '../../../main.inc.php';
}
if (! $res) {
	die("Main include failed");
}

require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once 'class/Geocoding.class.php';

global $conf, $db, $langs, $user;

// Load translation files required by the page
$langs->load("zenfusionmaps@zenfusionmaps");

// Get parameters
$id = GETPOST('id', 'int');
$mode = GETPOST('mode', 'alpha');

// Access control
if (! $conf->zenfusionmaps->enabled) {
	accessforbidden();
}
if ($user->societe_id > 0) {
	// External user
	accessforbidden();
}

if (!('thirdparties' === $mode || 'contacts' === $mode)) {
	// TODO: issue a proper error
	echo 'ERROR';
	exit;
}

$list = array();

if ('thirdparties' === $mode) {
	$sql = 'SELECT s.nom AS name, s.address, s.zip, s.town, d.nom AS state, c.code AS country FROM ' . MAIN_DB_PREFIX . 'societe AS s';
	$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_departements as d ON s.fk_departement = d.rowid';
	$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_country as c ON s.fk_pays = c.rowid';
	$result = $db->query( $sql );
	if ($result) {
		while ($obj = $db->fetch_object( $resql )) {
			$list[] = $obj;
		}
	}
} elseif ('contacts' === $mode) {
	$sql = 'SELECT s.lastname as name, s.address, s.zip, s.town, d.nom AS state, c.code AS country FROM ' . MAIN_DB_PREFIX . 'socpeople AS s';
	$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_departements as d ON s.fk_departement = d.rowid';
	$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'c_country as c ON s.fk_pays = c.rowid';
	$result = $db->query( $sql );
	if ($result) {
		while ($obj = $db->fetch_object( $resql )) {
			$list[] = $obj;
		}
	}
}

/**
 * TODO: geocode addresses in Dolibarr with Geocoding API
 * https://developers.google.com/maps/documentation/geocoding/
 * Free limits:
 * - 2500 requests/day
 * - 5 requests/s
 */

$geocoding = new \zenfusion\maps\Geocoding('json');
$france = $geocoding->getCountry('France')[0];

$data = array();

// TODO: use cached results if available
// TODO: add an InfoWindow with clickable urls
foreach ($list as $entry) {
	$geocoding = new \zenfusion\maps\Geocoding( 'json' );
	if (!empty($entry->zip))
		$geocoding->addPostalCode($entry->zip);
	if (!empty($entry->state))
		$geocoding->addAdministrativeArea($entry->state);
	if (!empty($entry->country))
		$geocoding->addCountry($entry->country);
	$address = $entry->address;
	$address .= ($address?',':'') . $entry->town;
	$position = $geocoding->getByAddress($address)[0]->geometry->location;

	$data[] = array(
		'position' => $position,
		'title' => $entry->name
	);
}

/**
 * TODO: cache results
 */

// FIXME: add to configuration page
//$maps_apikey = 'AIzaSyAzVn2XYz7AA2gELTKs_CObPZuYaYtFjEU';

/*
 * ACTIONS
 */

/*
 * VIEW
 */

if('thirdparties' === $mode) {
	llxHeader( '', $langs->trans( 'ThirdpartiesMap' ), '' );
} elseif('contacts' === $mode) {
	llxHeader( '', $langs->trans( 'ContactsMap' ), '' );
}

/**
 * Display geocoded addresses
 */
?>
	<script type="text/javascript">
		function initialize() {
			var mapOptions = {
				center: <?php echo json_encode($france->geometry->location) ?>,
				zoom: 6
			};

			var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

			var markers = [];

			var markersdata = <?php echo json_encode($data) ?>;

			for(var i = 0; i< markersdata.length; i++) {
				var marker = new google.maps.Marker(markersdata[i]);
				markers.push(marker);
			}

			new MarkerClusterer(map, markers);
		}

		function loadMapsScript() {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp' +
				'&signed_in=true&callback=initialize';
			document.body.appendChild(script);
		}

		function loadMarkerClustererScript() {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = '<?php echo dol_buildpath('zenfusionmaps/js/js-marker-clusterer/src/markerclusterer_compiled.js', 2) ?>';
			document.body.appendChild(script);
		}

		window.onload = loadMarkerClustererScript();
		window.onload = loadMapsScript();
	</script>
	<div id="map-canvas" style="height: 800px;"></div>
<?php

// End of page
llxFooter();
