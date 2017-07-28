<?php

/**
 * Define new permissions.
 */
function _demtools_address_permissions() {
  $prefix = ts('DemTools CiviCRM County-State Permissions: '); 
  $permissions = array(
    'view county contacts'   => $prefix . ts('view all contacts who live in your county'),
    'edit county contacts'   => $prefix . ts('edit all contacts who live in your county'),
    'delete county contacts' => $prefix . ts('delete all contacts who live in your county'),
    'view state contacts'    => $prefix . ts('view all contacts who live in your state'),
    'edit state contacts'    => $prefix . ts('edit all contacts who live in your state'),
    'delete state contacts'  => $prefix . ts('delete all contacts who live in your state'),
  );
  return $permissions;
}

/**
 * Add permissions to current user and adds primary address to new table
 */
function _demtools_address_aclWhereClause($type, &$tables, &$whereTables, &$contactID, &$where) {
  require_once 'CRM/Contact/BAO/Contact/Permission.php';
  if ((CRM_Core_Permission::check('view state contacts') && $type == "1")
      || (CRM_Core_Permission::check('edit state contacts') && $type == "2")
      || (CRM_Core_Permission::check('delete state contacts') && $type == "3")) {
    $address_table = 'civicrm_address';
    $tables[$address_table] = $whereTables[$address_table] = "LEFT JOIN {$address_table} ON {$address_table}.contact_id = contact_a.id";
    $where = "{$address_table}.state_province_id IN (SELECT state_province_id FROM civicrm_address WHERE contact_id = {$contactID} AND is_primary = 1 )";
  }
  elseif ((CRM_Core_Permission::check('view county contacts') && $type == "1")
           || (CRM_Core_Permission::check('edit county contacts') && $type == "2")
           || (CRM_Core_Permission::check('delete county contacts') && $type == "3")) {
    $address_table = 'civicrm_address';
    $tables[$address_table] = $whereTables[$address_table] = "LEFT JOIN {$address_table} ON {$address_table}.contact_id = contact_a.id";
    $where = "{$address_table}.county_id IN (SELECT county_id FROM civicrm_address WHERE contact_id = {$contactID} AND is_primary = 1 )";
  }
}

/**
 * Prevents address_id from being overriden, creates address record will NULL id 
 */
function _demtools_address_postProcess($formName, &$form) {
  if (CRM_Utils_Array::value('address', $form->getVar('_values'))) {
    $contactID = $form->_contactId;
    $settings =  CRM_Core_BAO_Setting::getItem("Permission Address");
    $custom_id = $settings["custom_id"];
    $addresses = $form->_values['address'];
    $settings =  CRM_Core_BAO_Setting::getItem("Permission Address");
    $submitted = $form->_submitValues["address"];
    $table = "address_permissions";
    foreach ($addresses as $key => $value) { 
      foreach ($submitted[$key] as $k => $v) {
        $keys = explode("_", $k);
        if ($keys[0] == "custom" && $keys[1] == $custom_id && $v == 1) {
          $sql = "SELECT address_id FROM {$table} WHERE contact_id = {$contactID} AND address_id = {$value['id']}";
          $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
          if (!$dao->fetch()) {
            $insertSql = "INSERT INTO {$table} (contact_id, address_id) VALUES ({$contactID}, {$value['id']})";
            $dao2 = CRM_Core_DAO::executeQuery($insertSql, CRM_Core_DAO::$_nullArray);
          }
        }
        elseif ($keys[0] == "custom" && $keys[1] == $custom_id && $v == 0) {
          $sql = "SELECT address_id FROM {$table} WHERE contact_id = {$contactID} AND address_id = {$value['id']}";
          $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
          if ($dao->fetch()){
            $sql = "DELETE FROM {$table} WHERE address_id = {$value['id']} AND contact_id = {$contactID}";
            $dao = CRM_Core_DAO::executeQuery($sql);
          }
        }
        else {
          $checksql = "SELECT address_id FROM {$table} WHERE address_id = {$value['id']}";
      	  $checkdao = CRM_Core_DAO::executeQuery($checksql, CRM_Core_DAO::$_nullArray);
          if (!$checkdao->fetch()) {
            $sql = "SELECT id, state_province_id, county_id FROM civicrm_address WHERE id = {$value['id']}";
            $dao = CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
            $dao->fetch();
            if (($dao->id > 0) && (array_key_exists('county_id', $value) && $value['county_id'] == $dao->county_id) && ($value['state_province_id'] == $dao->state_province_id ) && (!CRM_Core_Permission::check('edit county contacts') || !CRM_Core_Permission::check('edit all contacts') ) ){
              $value['id'] = "NULL";
            }
          }
        }
      }
    }
  }
}

/**
 * Freeze state and county field is user doesn't have permission to change
 */
function _demtools_address_buildForm($formName, &$form) {
  if (CRM_Utils_Array::value('address', $form->getVar('_values')) && CRM_Core_Permission::check('edit all contacts') != "1") {
    foreach ($form->_values['address'] as $key => $value) {
      $sql = "SELECT address_id FROM address_permissions WHERE address_id = {$value['id']}";
      $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
      if ($dao->fetch()) {
        foreach ($form->_elements as $k => $v) {
          if(CRM_Utils_Array::value('name', $v->_attributes)) {
            if(strpos($v->_attributes['name'],"county_id")) {
              $element = $form->_elements[$k];
              $element->_flagFrozen = 1;
            }
            if (strpos($v->_attributes['name'],"state_province_id")) {
              $element = $form->_elements[$k];
              $element->_flagFrozen = 1;
            }
          }
        }
      }
    }
  }
}

/**
 * Create a new address format (I think).
 */
function _demtools_address_create_format() {
  civicrm_api3('Setting', 'create',
    array(
      'address_options' => array("1", "2", "4", "5", "7", "8", "9"),
      'address_format' => "{contact.address_name}\\n{contact.street_address}\\n{contact.supplemental_address_1}\\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\\n{contact.county}{ }{contact.country}"
    )
  );
}

/**
 * Create the address permissions database table.
 */
function _demtools_address_create_db_table() {
  #$sql = 'DROP TABLE IF EXISTS address_permissions';
  #$dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
  #$sql = 'CREATE TABLE address_permissions ( address_id int, contact_id int );';
  // This seems safer than the above. @TODO: confirm and remove the cruft.
  $sql = 'CREATE TABLE IF NOT EXISTS address_permissions ( address_id int, contact_id int );';
  CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
}

/**
 * Delete the address permissions database table.
 */
function _demtools_address_delete_db_table() {
  $sql = 'DROP TABLE IF EXISTS address_permissions';
  CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
}

/**
 * Return a custom entity's ID (or FALSE if it doesn't exist.)
 */
function _demtools_get_custom_entity_id($type, $name) {
  $result = civicrm_api3($type, 'get', array(
    'sequential' => 1,
    'return' => array('id'),
    'name' => $name,
  ));
  return array_key_exists('id', $result) ? $result['id'] : FALSE;
}

/**
 * Return a custom group's ID (or FALSE if it doesn't exist.)
 */
function _demtools_get_custom_group_id($name) {
  return _demtools_get_custom_entity_id('CustomGroup', $name);
}

/**
 * Return a custom group's ID (or FALSE if it doesn't exist.)
 */
function _demtools_get_custom_field_id($name) {
  return _demtools_get_custom_entity_id('CustomField', $name);
}

/**
 * Delete a custom entity by name.
 */
function _demtools_delete_custom_entity($type, $name) {
  if ($id = _demtools_get_custom_entity_id($type, $name)) {
    $result = civicrm_api3($type, 'delete', array(
      'sequential' => 1,
      'id' => $id,
    ));
    return (bool) !$result['is_error'];
  }
}

/**
 * Return a custom group's ID (or FALSE if it doesn't exist.)
 */
function _demtools_delete_custom_group($name) {
  return _demtools_delete_custom_entity('CustomGroup', $name);
}

/**
 * Return a custom group's ID (or FALSE if it doesn't exist.)
 */
function _demtools_delete_custom_field($name) {
  return _demtools_delete_custom_entity('CustomField', $name);
}

/**
 * Delete this extension's custom group.
 */
function _demtools_address_delete_custom_group() {
  _demtools_delete_custom_group('permissioned_address');
}

/**
 * Delete this extension's custom field.
 */
function _demtools_address_delete_custom_field() {
  _demtools_delete_custom_field('is_permissioned');
}

/**
 * Create a custom group for permissioned addresses.
 */
function _demtools_address_create_custom_group() {
  if ($group_id = _demtools_get_custom_group_id('permissioned_address')) {
    return $group_id;
  }
  $params = array(
    'version'=>'3',
    'name' => 'permissioned_address',
    'title' => 'Permissioned Address',
    'extends' => 'Address',
    'style' => 'Inline',
    'collapse_display' => '0',
    'weight' => '3',
    'is_active' => '1',
    'is_multiple' => '0',
  );
  $results = civicrm_api('CustomGroup','create', $params);
  $group_id = $results['id'];

  CRM_Core_BAO_Setting::setItem($group_id, "Permission Address", "group_id");
  return $group_id;
}

/**
 * Create a custom field to designate an address as permissioned.
 */
function _demtools_address_create_custom_field($group_id) {
  if ($field_id = _demtools_get_custom_field_id('is_permissioned')) {
    return $field_id;
  }
  $params = array(
    "version" => '3',
    "custom_group_id" => $group_id,
    "name" => "is_permissioned",
    "label" => "Is Permissioned",
    "data_type" => "Boolean",
    "html_type" => "Radio",
    "help_post" => "If enabled this contact will have permission over contacts in this graphical region",
    "is_required" => "0",
    "is_searchable" => "1",
    "is_search_range" => "0",
    "weight" => "2",
    "is_active" => "1",
    "is_view" => "0",
    "text_length" => "255",
    "note_columns" => "60",
    "note_rows" => "4",
  );
  $results = civicrm_api('CustomField', 'create', $params);
  $field_id = $results["id"];

  CRM_Core_BAO_Setting::setItem($field_id, "Permission Address", "custom_id");
  return $field_id;
}

/**
 * Mysterious setting of a magic number.
 *
 * @TODO: figure out and document what this is supposed to do.
 */
function _demtools_address_config() {
  include_once 'CRM/Core/BAO/Setting.php';
  // @TODO: figure out whether this does anything.
  $address_options = CRM_Core_BAO_Setting::getItem(
    'CiviCRM Preferences',
    'address_options',
    NULL,
    NULL,
    NULL,
    1
  );
  /*
  // @TODO: figure out where that magic number comes from.
  $address_options = CRM_Core_BAO_Setting::setItem(
    '145789',
    'CiviCRM Preferences',
    'address_options',
    NULL,
    NULL,
    NULL,
    1
  );
  */
}
