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
 *	\file		class/Geocoding.class.php
 *	\ingroup	zenfusionmaps
 *	\brief		Google Geocoding
 */

namespace zenfusion\maps;

use \Exception;

/**
 * Class Geocoding
 * @package Zenfusionmaps
 */
class Geocoding
{
	const ENDPOINT='https://maps.googleapis.com/maps/api/geocode/';

	private $output;

	private $address;
	private $components;

	private $request_uri;


	/**
	 * Start a geocoding
	 *
	 * @param string $output Output type (json or xml)
	 *
	 * @throws Exception
	 */
	public function __construct($output) {
		if ('json' !== $output && 'xml' !== $output) {
			throw new Exception('Wrong output type');
		}
		$this->output = $output;
	}

	/**
	 * Add an address
	 *
	 * @param string $address Address to add
	 */
	private function addAddress($address) {
		$this->address .= $address;
	}

	/**
	 * Add a component
	 *
	 * @param string $component component to add
	 */
	private function addComponent($component) {
		$this->components .= ($this->components?'|':'') . $component;
	}

	/**
	 * Build a full request URI
	 *
	 * @return string The request URI
	 * @throws Exception
	 */
	private function buildRequestURI() {
		$this->request_uri = self::ENDPOINT. $this->output  . '?';
		if (null === $this->address && null === $this->components) {
			throw new Exception('Either address or components is required');
		}
		if (null !== $this->address) {
			$this->request_uri .= '&address=' . urlencode($this->address);
		}
		if (null !== $this->components) {
			$this->request_uri .= '&components=' . urlencode($this->components);
		}
	}

	/**
	 * Make a geocoding request
	 *
	 * @return array
	 * @throws Exception
	 */
	private function request() {
		$this->buildRequestURI();
		$reply = json_decode(file_get_contents($this->request_uri));

		if ('OK' !== $reply->status) {
			throw new Exception(
				'The request for ' . $this->request_uri .' failed with status: ' . $reply->status
				. ($reply->error_message?', and error message: ' . $reply->error_message:'')
			);
		}

		return $reply->results;
	}

	public function addAdministrativeArea($administrative_area) {
		$this->addComponent('administrative_area:' . $administrative_area);
	}

	public function addPostalCode($postal_code) {
		$this->addComponent('postal_code:' . $postal_code);
	}

	public function addCountry($country) {
		$this->addComponent('country:' . $country);
	}

	/**
	 * Get a country geocode
	 *
	 * @param string $country The country to geocode
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getCountry($country) {
		$this->addCountry($country);

		return $this->request();
	}

	/**
	 * Get an administrative area geocode
	 *
	 * @param string $administrative_area The administrative area to geocode
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getByAdministrativeArea($administrative_area) {
		$this->addAdministrativeArea($administrative_area);

		return $this->request();
	}

	/**
	 * Get an address geocode
	 *
	 * @param string $address The address to geocode
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getByAddress($address) {
		$this->addAddress($address);

		return $this->request();
	}
}
