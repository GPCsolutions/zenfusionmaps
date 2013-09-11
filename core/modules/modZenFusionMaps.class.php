<?php
/*
 * ZenFusion Maps - A Google Maps for Dolibarr
 * Copyright (C) 2013 Cédric Salvador <csalvador@gpcsolutions.fr>
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
dol_include_once('/zenfusionoauth/inc/oauth.inc.php');
dol_include_once('/zenfusionoauth/lib/scopes.lib.php');

/**
 * \class modZenFusionMaps
 * \brief Describes and activates Zenfusion Maps module
 */
class modZenFusionMaps extends DolibarrModules
{

    /**
     * 	Constructor. Define names, constants, directories, boxes, permissions
     *
     * 	@param	DoliDB		$db	Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->numero = 105005;
        $this->rights_class = 'zenfusionmaps';
        $this->family = "other";
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Sync with Google Maps";
        $this->version = 'development';
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        $this->special = 1;
        $this->picto = 'maps@zenfusionmaps';
        $this->module_parts = array(
            //'triggers' => 1,
            'hooks' => array('thirdpartycard', 'contactcard')
        );
        $this->dirs = array();
        $this->config_page_url = array("about.php@zenfusionmaps");
        $this->depends = array("modZenFusionOAuth");
        $this->requiredby = array();
        $this->phpmin = array(5, 3);
        $this->need_dolibarr_version = array(3, 2);
        $this->langfiles = array("zenfusionmaps@zenfusionmaps");
        $this->const = array();
        $this->tabs = array();
        $this->boxes = array();
        $this->rights = array();
        $this->rights[0][0] = 7685239;
        $this->rights[0][1] = 'Use ZenFusionMaps';
        $this->rights[0][3] = 0;
        $this->rights[0][4] = 'use';
        $this->menus = array();
    }

    /**
     * \brief Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     * It also creates data directories.
     * \return int 1 if OK, 0 if KO
     */
    public function init()
    {
        global $conf, $langs;
        // We set the scope we need
        $sql = array();
        $result = $this->load_tables();
        // Activation des modules dont le module depend
        $modulesdir[] = DOL_DOCUMENT_ROOT . '/extensions/zenfusionoauth/core/modules/';
        $modulesdir[] = DOL_DOCUMENT_ROOT . '/custom/zenfusionoauth/core/modules/';
        $modulesdir[] = DOL_DOCUMENT_ROOT . '/core/modules/';
        $num = count($this->depends);
        $exists = 0;
        foreach ($modulesdir as $dir) {
            for ($i = 0; $i < $num; $i++) {
                if (file_exists($dir.$this->depends[$i].".class.php")) {
                    $err += activateModule($this->depends[$i]);
                    $exists++;
                }
            }
        }

        if (!$err && function_exists('curl_init') && $exists >= $num) {
            //addScope(GOOGLE_MAPS_SCOPE); no need for scope for now
            $this->_init($sql);
        } else {
            $langs->load('zenfusionmaps@zenfusionmaps');
            if ($err || $exists < $num) {
                $mesg = $langs->trans("MissingMod");
                if (DOL_VERSION >= '3.3') {
                    setEventMessage($mesg, 'errors');
                } else {
                    $mesg = urlencode($mesg);
                    $msg = '&mesg=' . $mesg;
                }
            } else {
                $mesg = $langs->trans("MissingCURL");
                if (DOL_VERSION >= '3.3') {
                    setEventMessage($mesg, 'errors');
                } else {
                    $mesg = urlencode($mesg);
                    $msg = '&mesg=' . $mesg;
                }
            }
            header("Location: modules.php?mode=interfaces" . $msg);
            exit;
        }
    }

    /**
     * \brief Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted.
     * \return int 1 if OK, 0 if KO
     */
    public function remove()
    {
        $sql = array();
        //removeScope(GOOGLE_MAPS_SCOPE); no need for scope for now
        return $this->_remove($sql);
    }

    /**
     * \brief Create tables, keys and data required by module
     * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     *  and create data commands must be stored in directory /mymodule/sql/
     * This function is called by this->init.
     * \return int <=0 if KO, >0 if OK
     */
    public function load_tables()
    {
        return $this->_load_tables('/zenfusionmaps/sql/');
    }
}
