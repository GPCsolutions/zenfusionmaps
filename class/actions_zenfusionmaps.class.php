<?php

/*
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
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
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions.lib.php';

class ActionsZenFusionMaps
{
    private $db;

    /**
     *  Constructor
     *
     *  @param	DoliDB	$db		Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getLocale()
    {
        global $langs;
        $lang = $langs->getDefaultLang();
        switch ($lang) {
            case 'ar_SA':
                $locale = 'ar';
                break;
            case 'bg_BG':
                $locale = 'bg';
                break;
            case 'ca_ES':
                $locale = 'ca';
                break;
            case 'da_DK':
                $locale = 'da';
                break;
            case 'de_AT':
            case 'de_DE':
                $locale = 'de';
                break;
            case 'el_GR':
                $locale = 'el';
                break;
            case 'en_AU':
            case 'en_IN':
            case 'en_NZ':
            case 'en_SA':
            case 'en_US':
                $locale = 'en';
                break;
            case 'en_GB':
                $locale = 'en-GB';
                break;
            case 'es_AR':
            case 'es_HN':
            case 'es_MX':
            case 'es_PE':
            case 'es_PR':
                $locale = 'es-419';
                break;
            case 'es_ES':
                $locale = 'es';
                break;
            case 'et_EE':
                $locale = 'et';
                break;
            case 'fa_IR':
                $locale = 'fa';
                break;
            case 'fi_FI':
                $locale = 'fi';
                break;
            case 'fr_BE':
            case 'fr_CH':
            case 'fr_FR':
                $locale = 'fr';
                break;
            case 'fr_CA':
                $locale = 'fr-CA';
                break;
            case 'he_IL':
                $locale = 'iw';
                break;
            case 'hu_HU':
                $locale = 'hu';
                break;
            case 'is_IS':
                $locale = 'is';
                break;
            case 'it_IT':
                $locale = 'it';
                break;
            case 'ja_JP':
                $locale = 'ja';
                break;
            case 'nb_NO':
                $locale = 'no';
                break;
            case 'nl_BE':
            case 'nl_NL':
                $locale = 'nl';
                break;
            case 'pl_PL':
                $locale = 'pl';
                break;
            case 'pt_BR':
                $locale = 'pt-BR';
                break;
            case 'pt_PT':
                $locale = 'pt-PT';
                break;
            case 'ro_RO':
                $locale = 'ro';
                break;
            case 'ru_UA':
            case 'ru_RU':
                $locale = 'ru';
                break;
            case 'sl_SI':
                $locale = 'sl';
                break;
            case 'sv_SE':
                $locale = 'sv';
                break;
            case 'tr_TR':
                $locale = 'tr';
                break;
            case 'zh_CN':
                $locale = 'zh-CN';
                break;
            case 'zh_TW':
                $locale = 'zh-tw';
                break;
            default:
                $locale = 'en';
        }

        return $locale;
    }

    public function printAddress($parameters, $object, &$action)
    {
        //$object is just the address
        $element = $parameters['element'];
        $id = $parameters['id'];
        if ($element == 'thirdparty') {
            require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
            $obj = new Societe($this->db);
        } elseif ($element == 'contact') {
            require_once DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';
            $obj = new Contact($this->db);
        } elseif ($element == 'member') {
            require_once DOL_DOCUMENT_ROOT . '/adherent/class/adherent.class.php';
            $obj = new Adherent($this->db);
        }
        $obj->fetch($id);
        $address = $object;
        $address = str_replace('<br>', ' ', $address);
        $address = str_replace("\n", ' ', $address);
        //preg_replace to filter out CEDEXes because Google can't process them
        $address .= ' ' . $obj->zip . ' ' . preg_replace('/\sCEDEX.*$/i', '', $obj->town) . ' ' . $obj->country;
        $address = str_replace(' ', '+', $address);
        $googleurl = 'https://maps.google.com/maps?q=' . $address . '&hl=' . $this->getLocale();
        $this->resprints = '<a href="' . $googleurl . '" target="_blank">' . nl2br($object);
        $picto = img_picto('Google Maps',
                           dol_buildpath('/zenfusionmaps/img/marker.png', 1),
                           '',
                           true
                           );
        $this->resprints .= $picto . '</a>';

        return 1;
    }

}
