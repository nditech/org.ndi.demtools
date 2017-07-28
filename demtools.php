<?php

require_once 'demtools.civix.php';

require_once 'includes/address_permissions.inc';
require_once 'includes/contactpermonth.inc';
require_once 'includes/dashboard.inc';
require_once 'includes/simplify_ui.inc';

/**
 * Implements hook_civicrm_pageRun().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_pageRun/
 */
function demtools_civicrm_pageRun(&$page) {
  _demtools_dashboard_pageRun($page);
}

/**
 * Implements hook_civicrm_dashboard_defaults().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_dashboard_defaults/
 */
function demtools_civicrm_dashboard_defaults($availableDashlets, &$defaultDashlets) {
  _demtools_contactpermonth_dashboard_defaults($availableDashlets, $defaultDashlets);
}

/**
 * Implements hook_civicrm_dashboard().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_dashboard/
 */
function demtools_civicrm_dashboard($contactID, &$contentPlacement) {
  $contentPlacement = CRM_Utils_Hook::DASHBOARD_ABOVE;
  return _demtools_build_dashboard();
}

/**
 * Implements hook_civicrm_permission().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_permission/
 */
function demtools_civicrm_permission( &$permissions ){
  $permissions = _demtools_address_permissions(); 
}

/**
 * Implements hook_civicrm_aclWhereClause().
 * 
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_aclWhereClause/
 */
function demtools_civicrm_aclWhereClause($type, &$tables, &$whereTables, &$contactID, &$where) {
  _demtools_address_aclWhereClause($type, $tables, $whereTables, $contactID, $where);
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_postProcess/
 */
function demtools_civicrm_postprocess($formName, &$form){
  _demtools_address_postProcess($formName, $form);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_buildForm/
 */
function demtools_civicrm_buildForm($formName, &$form) {
  _demtools_address_buildForm($formName, $form);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_install/
 */
function demtools_civicrm_install() {
  _demtools_address_create_format();
  _demtools_address_create_db_table();
  $group_id = _demtools_address_create_custom_group();
  _demtools_address_create_custom_field($group_id);
  return _demtools_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function demtools_civicrm_uninstall() {
  _demtools_address_delete_db_table();
  _demtools_address_delete_custom_field();
  _demtools_address_delete_custom_group();
  return _demtools_civix_civicrm_uninstall();
}
/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_config/
 */
function demtools_civicrm_config(&$config) {
  _demtools_address_config();
  # This function was causing failures during 4.6->4.7 upgrades. See:
  # https://issues.civicrm.org/jira/browse/CRM-19915
  #_demtools_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function demtools_civicrm_xmlMenu(&$files) {
  _demtools_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function demtools_civicrm_enable() {
  #_demtools_enable_only_components(DEMTOOLS_CIVICRM_COMPONENTS);
  _demtools_enable_only_components(_demtools_get_demtools_civicrm_components());
  #_demtools_set_menu_item_visibility(DEFAULT_CIVICRM_MENU_ITEMS, FALSE);
  _demtools_set_menu_item_visibility(_demtools_get_default_civicrm_menu_items(), FALSE);
  return _demtools_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function demtools_civicrm_disable() {
  #_demtools_enable_only_components(DEFAULT_CIVICRM_COMPONENTS);
  _demtools_enable_only_components(_demtools_get_default_civicrm_components());
  #_demtools_set_menu_item_visibility(DEFAULT_CIVICRM_MENU_ITEMS, TRUE);
  _demtools_set_menu_item_visibility(_demtools_get_default_civicrm_menu_items(), TRUE);
  return _demtools_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function demtools_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _demtools_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_managed/
 */
function demtools_civicrm_managed(&$entities) {
  $entities[] = _demtools_contactpermonth_managed();
  return _demtools_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_caseTypes/
 */
function demtools_civicrm_caseTypes(&$caseTypes) {
  _demtools_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_alterSettingsFolders/
 */
function demtools_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _demtools_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @link https://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_alterContent/
 */
function demtools_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  _demtools_simplify_ui_alterContent($content, $context, $tplName, $object);
}
