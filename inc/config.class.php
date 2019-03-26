<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2011 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

class PluginMspagreementConfig extends CommonDBTM {

   static protected $notable = true;

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if (!$withtemplate) {
         if ($item->getType() == 'Config') {
            return __('MSP Agreement', 'mspagreement');
         }
      }
      return '';
   }

   static function configUpdate($input) {
      // pra que server? $input['configuration'] = 1 - $input['configuration'];
      return $input;
   }

   function showForm() {
      global $CFG_GLPI;

      if (!Session::haveRight("config", UPDATE)) {
         return false;
      }

      $my_config = Config::getConfigurationValues('plugin:MSPAgreement');

      echo "<form name='form' action=\"".Toolbox::getItemTypeFormURL('Config')."\" method='post'>";
      echo "<div class='center' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe'>";

      echo "<tr><th colspan='4'>" . __('MSP Agreement setup') . "</th></tr>";

      $values = [
         'msp'        => __("On MSP menu (top-level)", 'mspagreement'),
         'plugins'    => __("Under 'Plugin' menu", 'mspagreement'),
         'helpdesk'   => __("Under 'Assistance' menu", 'mspagreement'),
      ];
      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __("Show MSP menu options", "mspagreement") . "</td><td>";
      Dropdown::showFromArray(
         'menu',
         $values,
         [
            'value' => $my_config['menu']
         ]
      );
      echo "<input type='hidden' name='config_class' value='".__CLASS__."'>";
      echo "<input type='hidden' name='config_context' value='plugin:MSPAgreement'>";
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td colspan='4' class='center'>";
      echo "<input type='submit' name='update' class='submit' value=\""._sx('button', 'Save')."\">";
      echo "</td></tr>";

      echo "</table></div>";
      Html::closeForm();
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      if ($item->getType() == 'Config') {
         $config = new self();
         $config->showForm();
      }
   }

   static function afterUpdate($item) {
      if(is_object($item)
         && is_array($item->fields)
         && isset($item->fields['context'])
         && ($item->fields['context'] == 'plugin:MSPAgreement')
         && isset($item->fields['name'])
         && ($item->fields['name'] == 'menu')) {

         // reset menu to force redoing
         unset($_SESSION['glpimenu']);
      }
   }

   /**
    * Install class related stuff
    *    Tables, settings, rights
    *
    * @param $migration  Migration   Migration object to execute the install
    *                                process
    *
    * @return void
    */
   function install(Migration $migration) {
      $config = new Config();
      // menu: 'msp'      (MSP top menu)
      //       'plugins'  (items under Plugin menu)
      //       'helpdesk' (items under Assistence menu)
      $config->setConfigurationValues('plugin:MSPAgreement', ['menu' => 'msp']);
   }
   
   /**
    * Uninstall class related stuff
    *    Settings, rights, secondary tables (primary table already dropped in
    *    hook.php uninstall function
    *
    * @param $migration  Migration   Migration object to execute the uninstall
    *                                process
    *
    * @return void
    */
   function uninstall() {
      $config = new Config();
      $config->deleteConfigurationValues('plugin:MSPAgreement', ['menu']);
   }

}
