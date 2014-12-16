<?php
/*
 * ZenFusion Maps - A Google Maps module for Dolibarr
 * Copyright (C) 2013 Cédric Salvador       <csalvador@gpcsolutions.fr>
 * Copyright (C) 2014 Raphaël Doursenaud    <rdoursenaud@gpcsolutions.fr>
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
 * \defgroup zenfusionmaps Module Zenfusion Maps
 * \brief Zenfusion Maps module for Dolibarr
 *
 * Integration of Google Maps in Dolibarr
 * using the Google Maps API.
 *
 */

/**
 * \file core/modules/modZenFusionMaps.class.php
 * \brief Zenfusion Maps module
 *
 * Declares and initializes the Zenfusion Maps module in Dolibarr
 *
 * \ingroup zenfusionmaps
 * \authors Cédric Salvador <csalvador@gpcsolutions.fr>
 */

include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Describes and activates Zenfusion Maps module
 */
class modZenFusionMaps extends DolibarrModules
{

    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->numero = 105005;
        $this->rights_class = 'zenfusionmaps';
        $this->family = "other";
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Google Maps";
        $this->version = '1.0.0';
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        $this->special = 1; // Interface
        $this->picto = 'maps@zenfusionmaps';
        $this->module_parts = array(
            'hooks' => array('thirdpartycard', 'contactcard', 'membercard')
        );
        $this->dirs = array();
        $this->config_page_url = array("about.php@zenfusionmaps");
        $this->hidden = false;
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->phpmin = array(5, 3);
        $this->need_dolibarr_version = array(3, 2);
        $this->langfiles = array("zenfusionmaps@zenfusionmaps");
        $this->const = array();
        $this->tabs = array();
        $this->dictionaries = array();
        $this->boxes = array();
        $this->rights = array();
        $this->rights[0][0] = 7685239;
        $this->rights[0][1] = 'Use ZenFusionMaps';
        $this->rights[0][3] = 0;
        $this->rights[0][4] = 'use';
        $this->menu = array();
    }

    /**
     * Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     * It also creates data directories.
     *
     * @param string $options Options when enabling module ('', 'noboxes')
     *
     * @return int 1 if OK, 0 if KO
     */
    public function init($options='')
    {
        $sql = array();
        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted.
     *
     * @return int 1 if OK, 0 if KO
     */
    public function remove()
    {
        $sql = array();
        return $this->_remove($sql);
    }
}
