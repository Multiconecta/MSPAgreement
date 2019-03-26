<?php
/*
 -------------------------------------------------------------------------
 Example plugin for GLPI
 Copyright (C) {YEAR} by the {NAME} Development Team.

 https://github.com/pluginsGLPI/example
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Example.

 Example is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Example is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Example. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Helvio Pichamone Jr.
// Purpose of file: initialize plugin
// ----------------------------------------------------------------------

define ('PLUGIN_MSPAGREEMENT_VERSION', '0.0.1');

/**
 * Name and version of the plugin
 *
 * @return array
 */
function plugin_version_mspagreement() {
   return [
      'name'           => 'MSP Agreement',
      'version'        => PLUGIN_MSPAGREEMENT_VERSION,
      'author'         => '<a href="www.multiconecta.com.br">Multiconecta</a>',
      'license'        => 'GPLv2+',
      'homepage'       => 'https://github.com/Multiconecta/mspagreement',
      'requirements'   => [
         'glpi' => [
            'min' => '9.3',
            'dev' => true
         ]
      ]
   ];
}

/**
 * Init hooks of the plugin.
 *
 * @return void
 */
function plugin_init_mspagreement() {
   global $PLUGIN_HOOKS,$CFG_GLPI;
   $PLUGIN_HOOKS['csrf_compliant']['mspagreement'] = true;

   $plugin = new Plugin();

   if ($plugin->isActivated('mspagreement')) { //is plugin active?

      $prof = new PluginMspagreementProfile();
      $prof->initSessionRights();
      $my_config = Config::getConfigurationValues('plugin:MSPAgreement');

      // Config class hooks
      if (Session::haveRight('config', UPDATE)) {
         $PLUGIN_HOOKS['config_page']['mspagreement'] = 'front/config.form.php';
         Plugin::registerClass('PluginMspagreementConfig', ['addtabon' => 'Config']);
      }

      // Profile class hooks
      if (Session::haveRight('profile', UPDATE)) {
         Plugin::registerClass('PluginMspagreementProfile', ['addtabon' => 'Profile']);
         $PLUGIN_HOOKS['change_profile']['mspagreement'] = 'plugin_change_profile_mspagreement';
      }

      // Link (??)
      if (version_compare(GLPI_VERSION, '9.1', 'ge')) {
         if (class_exists('PluginMspagreementParent')) {
            Link::registerTag(PluginMspagreementParent::$tags);
         }
      }

      // Menu
      if (Session::haveRight(PluginMspagreementParent::$rightname, READ)) {
         
         $PLUGIN_HOOKS['menu_toadd']['mspagreement'] = [$my_config['menu'] => ['PluginMspagreementParent']];

         //$PLUGIN_HOOKS["helpdesk_menu_entry"]['mspagreement'] = true;
      }

      $PLUGIN_HOOKS['item_update']['mspagreement'] = [
         'Config' => ['PluginMspagreementConfig', 'afterUpdate'],
      ];

   }

   // EXAMPLES:

   // Init session
   //$PLUGIN_HOOKS['init_session']['example'] = 'plugin_init_session_example';
   // Change entity
   //$PLUGIN_HOOKS['change_entity']['example'] = 'plugin_change_entity_example';

   // Restrict right
//   $PLUGIN_HOOKS['item_can']['example']          = ['Computer' => ['PluginExampleComputer', 'item_can']];
//   $PLUGIN_HOOKS['add_default_where']['example'] = ['Computer' => ['PluginExampleComputer', 'add_default_where']];

   // Add event to GLPI core itemtype, event will be raised by the plugin.
   // See plugin_example_uninstall for cleanup of notification
//   $PLUGIN_HOOKS['item_get_events']['example']
//                                 = ['NotificationTargetTicket' => 'plugin_example_get_events'];

   // Add datas to GLPI core itemtype for notifications template.
//   $PLUGIN_HOOKS['item_get_datas']['example']
//                                 = ['NotificationTargetTicket' => 'plugin_example_get_datas'];

//   $PLUGIN_HOOKS['item_transfer']['example'] = 'plugin_item_transfer_example';

   // Massive Action definition
//   $PLUGIN_HOOKS['use_massive_action']['example'] = 1;

//   $PLUGIN_HOOKS['assign_to_ticket']['example'] = 1;

   // Add specific files to add to the header : javascript or css
//   $PLUGIN_HOOKS['add_javascript']['mspagreement'] = 'js/mspagreement.js';
//   $PLUGIN_HOOKS['add_css']['mspagreement']   = 'css/mspagreement.css';

   // request more attributes from ldap
   //$PLUGIN_HOOKS['retrieve_more_field_from_ldap']['example']="plugin_retrieve_more_field_from_ldap_example";

   // Retrieve others datas from LDAP
   //$PLUGIN_HOOKS['retrieve_more_data_from_ldap']['example']="plugin_retrieve_more_data_from_ldap_example";

   // Reports
//   $PLUGIN_HOOKS['reports']['example'] = ['report.php'       => 'New Report',
//                                          'report.php?other' => 'New Report 2'];

   // Stats
//   $PLUGIN_HOOKS['stats']['example'] = ['stat.php'       => 'New stat',
//                                        'stat.php?other' => 'New stats 2',];

//   $PLUGIN_HOOKS['post_init']['example'] = 'plugin_example_postinit';

//   $PLUGIN_HOOKS['status']['example'] = 'plugin_example_Status';

   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();

//   $PLUGIN_HOOKS['infocom']['example'] = "plugin_example_infocom_hook";

   // pre_show and post_show for tabs and items,
   // see PluginExampleShowtabitem class for implementation explanations
//   $PLUGIN_HOOKS['pre_show_tab']['example']     = ['PluginExampleShowtabitem', 'pre_show_tab'];
//   $PLUGIN_HOOKS['post_show_tab']['example']    = ['PluginExampleShowtabitem', 'post_show_tab'];
//   $PLUGIN_HOOKS['pre_show_item']['example']    = ['PluginExampleShowtabitem', 'pre_show_item'];
//   $PLUGIN_HOOKS['post_show_item']['example']   = ['PluginExampleShowtabitem', 'post_show_item'];

//   $PLUGIN_HOOKS['pre_item_form']['example']    = ['PluginExampleItemForm', 'preItemForm'];
//   $PLUGIN_HOOKS['post_item_form']['example']   = ['PluginExampleItemForm', 'postItemForm'];

   // declare this plugin as an import plugin for Computer itemtype
//   $PLUGIN_HOOKS['import_item']['exemple'] = ['Computer' => ['Plugin']];

   // add additional informations on Computer::showForm
//   $PLUGIN_HOOKS['autoinventory_information']['exemple'] =  [
//      'Computer' =>  ['PluginExampleComputer', 'showInfo']
//   ];

}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_mspagreement_check_prerequisites() {

   $version = rtrim(GLPI_VERSION, '-dev');
   if (version_compare($version, '9.3', 'lt')) {
      echo sprintf(__("This plugin requires GLPI version %s", 'mspagreement'),
                   '9.3');
      return false;
   }

   return true;
}

/**
 * Check plugin's config before activation
 */
function plugin_mspagreement_check_config($verbose = false) {
   return true;
}
