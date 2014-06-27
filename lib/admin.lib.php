<?php
/*
 * ZenFusion Maps - A Google Maps module for Dolibarr
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
 * \file lib/admin.lib.php
 * \ingroup zenfusionmaps
 * \brief Module functions library
 */

/**
 * \function zfPrepareHead
 * \brief Display tabs in module admin page
 */
function zfPrepareHead()
{
    global $langs, $conf, $user;
    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/zenfusionmaps/admin/conf.php", 1);
    $head[$h][1] = $langs->trans("Config");
    $head[$h][2] = 'conf';
    $h ++;

    if ($conf->global->ZF_SUPPORT) {
        $head[$h][0] = dol_buildpath("/zenfusionmaps/admin/support.php", 1);
        $head[$h][1] = $langs->trans("HelpCenter");
        $head[$h][2] = 'help';
        $h ++;
    }

    $head[$h][0] = dol_buildpath("/zenfusionmaps/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h ++;

    return $head;
}