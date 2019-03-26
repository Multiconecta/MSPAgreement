<?php
/*
 -------------------------------------------------------------------------
 MSP Agreement plugin for GLPI
 Copyright (C) 2001-2019 by Multiconecta Solucoes Informatica Ltda.

 https://github.com/Multiconecta/mspagreement
 -------------------------------------------------------------------------

 LICENSE

 This file is part of MSP Agreement.

 MSP Agreement is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 MSP Agreement is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Example. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Helvio Pichamone Jr.
// Purpose of file: Plugin hooks
// ----------------------------------------------------------------------

/**
 * Adjust current profile rights and also create them in database if inexistent
 *
 * @return void
 */
function plugin_change_profile_mspagreement() {
   $prof = new PluginMspagreementProfile();
   $prof->initSessionRights();
}

// Define dropdown relations
function plugin_mspagreement_getDatabaseRelations() {
   return ["glpi_plugin_mspagreement_agreementtypes" => ["glpi_plugin_mspagreement_parents" => "plugin_mspagreement_agreementtypes_id"]];
}

// Define Dropdown tables to be manage in GLPI :
function plugin_mspagreement_getDropdown() {
   // Table => Name
   return ['PluginMspagreementAgreementType' => PluginMspagreementAgreementType::getTypeName(Session::getPluralNumber())];
}

////// SEARCH FUNCTIONS ///////(){

// Define Additionnal search options for types (other than the plugin ones)
/*function plugin_example_getAddSearchOptions($itemtype) {
   $sopt = [];
   if ($itemtype == 'Computer') {
         // Just for example, not working...
         $sopt[1001]['table']     = 'glpi_plugin_example_dropdowns';
         $sopt[1001]['field']     = 'name';
         $sopt[1001]['linkfield'] = 'plugin_example_dropdowns_id';
         $sopt[1001]['name']      = __('Example plugin', 'example');
   }
   return $sopt;
}*/

/*function plugin_example_getAddSearchOptionsNew($itemtype) {
   $options = [];
   if ($itemtype == 'Computer') {
      //Just for example, not working
      $options[] = [
         'id'        => '1002',
         'table'     => 'glpi_plugin_example_dropdowns',
         'field'     => 'name',
         'linkfield' => 'plugin_example_dropdowns_id',
         'name'      => __('Example plugin new', 'example')
      ];
   }
   return $options;
}*/

/*
// See also PluginExampleExample::getSpecificValueToDisplay()
function plugin_example_giveItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table = $searchopt[$ID]["table"];
   $field = $searchopt[$ID]["field"];

   switch ($table.'.'.$field) {
      case "glpi_plugin_example_examples.name" :
         $out = "<a href='".Toolbox::getItemTypeFormURL('PluginExampleExample')."?id=".$data['id']."'>";
         $out .= $data[$num][0]['name'];
         if ($_SESSION["glpiis_ids_visible"] || empty($data[$num][0]['name'])) {
            $out .= " (".$data["id"].")";
         }
         $out .= "</a>";
         return $out;
   }
   return "";
}*/

function plugin_mspagreement_displayConfigItem($type, $ID, $data, $num) {
   $searchopt = &Search::getOptions($type);
   $table     = $searchopt[$ID]["table"];
   $field     = $searchopt[$ID]["field"];

   // Example of specific style options
   // No need of the function if you do not have specific cases
   switch ($table.'.'.$field) {
      case "glpi_plugin_mspagreement_parents.name" :
         return " style=\"background-color:#DDDDDD;\" ";
   }
   return "";
}

/**
 * Plugin install process
 *    Execute all classes' install methods if they exist.
 *
 * @return boolean
 */
function plugin_mspagreement_install() {

   $migration = new Migration(PLUGIN_MSPAGREEMENT_VERSION);

   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
      if (preg_match("/inc.(.+)\.class.php$/", $filepath, $matches)) {
         $classname = 'PluginMspagreement' . ucfirst($matches[1]);
         include_once($filepath);
         if (method_exists($classname, 'install')) {
            $classname::install($migration);
         }
      }
   }

   $migration->executeMigration();
   return true;
}

/**
 * Plugin uninstall process
 *    Drop all classes' tables and execute all classes' uninstall methods if
 *    exist.
 *
 * @return boolean
 */
function plugin_mspagreement_uninstall() {

   $migration = new Migration(PLUGIN_MSPAGREEMENT_VERSION);

   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
//print "AAA filepath: $filepath<BR>\n";
      if (preg_match("/inc.(.+)\.class.php$/", $filepath, $matches)) {
         $classname = 'PluginMspagreement' . ucfirst($matches[1]);
         include_once($filepath);
         if (method_exists($classname, 'uninstall')) {
            $classname::uninstall($migration);
         }
         // Drop the object table here, so most classes would not
         // need uninstall method
         $table = $classname::getTable();
print "BBB table: $table; classname: $classname<BR>\n";
         $migration->displayMessage("Uninstalling $table");
         $migration->dropTable($table);
      }
   }

   $migration->executeMigration();
   return true;
}

//////////////////////////////
////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////


// Define actions :
/*function plugin_example_MassiveActions($type) {
   switch ($type) {
      // New action for core and other plugin types : name = plugin_PLUGINNAME_actionname
      case 'Computer' :
         return ['PluginExampleExample'.MassiveAction::CLASS_ACTION_SEPARATOR.'DoIt' =>
                                                              __("plugin_example_DoIt", 'example')];

      // Actions for types provided by the plugin are included inside the classes
   }
   return [];
}*/

/*
// How to display specific update fields ?
// options must contain at least itemtype and options array
function plugin_example_MassiveActionsFieldsDisplay($options = []) {
   //$type,$table,$field,$linkfield

   $table     = $options['options']['table'];
   $field     = $options['options']['field'];
   $linkfield = $options['options']['linkfield'];

   if ($table == getTableForItemType($options['itemtype'])) {
      // Table fields
      switch ($table.".".$field) {
         case 'glpi_plugin_example_examples.serial' :
            echo __("Not really specific - Just for example", 'example');
            //Html::autocompletionTextField($linkfield,$table,$field);
            // Dropdown::showYesNo($linkfield);
            // Need to return true if specific display
            return true;
      }

   } else {
      // Linked Fields
      switch ($table.".".$field) {
         case "glpi_plugin_example_dropdowns.name" :
            echo __("Not really specific - Just for example", 'example');
            // Need to return true if specific display
            return true;
      }
   }
   // Need to return false on non display item
   return false;
}/*

/*
// How to display specific search fields or dropdown ?
// options must contain at least itemtype and options array
// MUST Use a specific AddWhere & $tab[X]['searchtype'] = 'equals'; declaration
function plugin_example_searchOptionsValues($options = []) {
   $table = $options['searchoption']['table'];
   $field = $options['searchoption']['field'];

    // Table fields
   switch ($table.".".$field) {
      case "glpi_plugin_example_examples.serial" :
            echo __("Not really specific - Use your own dropdown - Just for example", 'example');
            Dropdown::show(getItemTypeForTable($options['searchoption']['table']),
                                               ['value'    => $options['value'],
                                                'name'     => $options['name'],
                                                'comments' => 0]);
            // Need to return true if specific display
            return true;
   }
   return false;
}*/

//////////////////////////////

// Hook done on before update item case
//function plugin_pre_item_update_example($item) {
//   /* Manipulate data if needed
//   if (!isset($item->input['comment'])) {
//      $item->input['comment'] = addslashes($item->fields['comment']);
//   }
//   $item->input['comment'] .= addslashes("\nUpdate: ".date('r'));
//   */
//   Session::addMessageAfterRedirect(__("Pre Update Computer Hook", 'example'), true);
//}

/*
// Hook done on update item case
function plugin_item_update_example($item) {
   Session::addMessageAfterRedirect(sprintf(__("Update Computer Hook (%s)", 'example'), implode(',', $item->updates)), true);
   return true;
}


// Hook done on get empty item case
function plugin_item_empty_example($item) {
   if (empty($_SESSION['Already displayed "Empty Computer Hook"'])) {
      Session::addMessageAfterRedirect(__("Empty Computer Hook", 'example'), true);
      $_SESSION['Already displayed "Empty Computer Hook"'] = true;
   }
   return true;
}


// Hook done on before delete item case
function plugin_pre_item_delete_example($object) {
   // Manipulate data if needed
   Session::addMessageAfterRedirect(__("Pre Delete Computer Hook", 'example'), true);
}


// Hook done on delete item case
function plugin_item_delete_example($object) {
   Session::addMessageAfterRedirect(__("Delete Computer Hook", 'example'), true);
   return true;
}


// Hook done on before purge item case
function plugin_pre_item_purge_example($object) {
   // Manipulate data if needed
   Session::addMessageAfterRedirect(__("Pre Purge Computer Hook", 'example'), true);
}


// Hook done on purge item case
function plugin_item_purge_example($object) {
   Session::addMessageAfterRedirect(__("Purge Computer Hook", 'example'), true);
   return true;
}


// Hook done on before restore item case
function plugin_pre_item_restore_example($item) {
   // Manipulate data if needed
   Session::addMessageAfterRedirect(__("Pre Restore Computer Hook", 'example'));
}


// Hook done on before restore item case
function plugin_pre_item_restore_example2($item) {
   // Manipulate data if needed
   Session::addMessageAfterRedirect(__("Pre Restore Phone Hook", 'example'));
}


// Hook done on restore item case
function plugin_item_restore_example($item) {
   Session::addMessageAfterRedirect(__("Restore Computer Hook", 'example'));
   return true;
}


// Hook done on restore item case
function plugin_item_transfer_example($parm) {
   //TRANS: %1$s is the source type, %2$d is the source ID, %3$d is the destination ID
   Session::addMessageAfterRedirect(sprintf(__('Transfer Computer Hook %1$s %2$d -> %3$d', 'example'), $parm['type'], $parm['id'],
                                     $parm['newID']));

   return false;
}

// Do special actions for dynamic report
function plugin_example_dynamicReport($parm) {
   if ($parm["item_type"] == 'PluginExampleExample') {
      // Do all what you want for export depending on $parm
      echo "Personalized export for type ".$parm["display_type"];
      echo 'with additional datas : <br>';
      echo "Single data : add1 <br>";
      print $parm['add1'].'<br>';
      echo "Array data : add2 <br>";
      Html::printCleanArray($parm['add2']);
      // Return true if personalized display is done
      return true;
   }
   // Return false if no specific display is done, then use standard display
   return false;
}


// Add parameters to Html::printPager in search system
function plugin_example_addParamFordynamicReport($itemtype) {
   if ($itemtype == 'PluginExampleExample') {
      // Return array data containing all params to add : may be single data or array data
      // Search config are available from session variable
      return ['add1' => $_SESSION['glpisearch'][$itemtype]['order'],
              'add2' => ['tutu' => 'Second Add',
                         'Other Data']];
   }
   // Return false or a non array data if not needed
   return false;
}*/

   // To be called for each task the plugin manage
   // task in class
/*   CronTask::Register('PluginExampleExample', 'Sample', DAY_TIMESTAMP, ['param' => 50]);
   return true;
}*/

/*function plugin_example_AssignToTicket($types) {
   $types['PluginExampleExample'] = "Example";
   return $types;
}


function plugin_example_get_events(NotificationTargetTicket $target) {
   $target->events['plugin_example'] = __("Example event", 'example');
}


function plugin_example_get_datas(NotificationTargetTicket $target) {
   $target->data['##ticket.example##'] = __("Example datas", 'example');
}*/

/*function plugin_mspagreement_postinit() {
   CommonGLPI::registerStandardTab('PluginMspagreementParent', 'periods');
}*/

/**
 * Hook to add more data from ldap
 * fields from plugin_retrieve_more_field_from_ldap_example
 *
 * @param $datas   array
 *
 * @return un tableau
 **/
/*function plugin_retrieve_more_data_from_ldap_example(array $datas) {
   return $datas;
}*/


/**
 * Hook to add more fields from LDAP
 *
 * @param $fields   array
 *
 * @return un tableau
 **/
/*function plugin_retrieve_more_field_from_ldap_example($fields) {
   return $fields;
}

// Check to add to status page
function plugin_example_Status($param) {
   // Do checks (no check for example)
   $ok = true;
   echo "example plugin: example";
   if ($ok) {
      echo "_OK";
   } else {
      echo "_PROBLEM";
      // Only set ok to false if trouble (global status)
      $param['ok'] = false;
   }
   echo "\n";
   return $param;
}

function plugin_example_display_central() {
   echo "<tr><th colspan='2'>";
   echo "<div style='text-align:center; font-size:2em'>";
   echo __("Plugin example displays on central page", "example");
   echo "</div>";
   echo "</th></tr>";
}

function plugin_example_display_login() {
   echo "<div style='text-align:center; font-size:2em'>";
   echo __("Plugin example displays on login page", "example");
   echo "</div>";
}

function plugin_example_infocom_hook($params) {
   echo "<tr><th colspan='4'>";
   echo __("Plugin example displays on central page", "example");
   echo "</th></tr>";
}*/
