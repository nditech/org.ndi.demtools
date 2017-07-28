<?php

/**
 * CiviCRM doesn't allow individual components to be enabled or disabled.
 * Instead, we need to provide a list of all components we want to have
 * enabled, including ones already enabled. So *excluding* a component from
 * this list is how we disable it. 
 */

// A list of components that CiviCRM enables by default. We use this when
// uninstalling the DemTools extension, to re-enable the components we'd turned
// off to simplify the UI.
#define('DEFAULT_CIVICRM_COMPONENTS', array(
#  'CiviCase',
#  'CiviContribute',
#  'CiviEvent',
#  'CiviMail',
#  'CiviMember',
#  'CiviPledge',
#  'CiviReport',
#));

/**
 * Shim pending server upgrades.
 */
function _demtools_get_default_civicrm_components() {
  // @TODO: Remove this once we're no longer running any servers with PHP 5.5.
  return array(
    'CiviCase',
    'CiviContribute',
    'CiviEvent',
    'CiviMail',
    'CiviMember',
    'CiviPledge',
    'CiviReport',
  );
}

// A reduced list of components that we use with DemTools to simplify the UI.
#define('DEMTOOLS_CIVICRM_COMPONENTS', array(
#  'CiviCase',
#  'CiviEvent',
#  'CiviMail',
#  'CiviReport',
#));

/**
 * Shim pending server upgrades.
 */
function _demtools_get_demtools_civicrm_components() {
  // @TODO: Remove this once we're no longer running any servers with PHP 5.5.
  return array(
    'CiviCase',
    'CiviEvent',
    'CiviMail',
    'CiviReport',
  );
}

// A list of menu items to deactivate for DemTools to simplify UI. We
// reactivate this same list when uninstalling the DemTools extension.
#define('DEFAULT_CIVICRM_MENU_ITEMS', array(
#  '5',  //Search -> Full-text Search
#  '6',  //Search -> Search Builder
#  '9',  //Search -> Find Mailings
#  '11', //Search -> Find Participants
#  '13', //Search -> Find Activites
#  '14', //Search -> custom searches
#  '17', //Contacts -> new Household
#  '60', //Event -> Personal Campaign Pages
#  '62', //Events -> new price set
#  '63', //Events -> manage price set
#));

/**
 * Shim pending server upgrades.
 */
function _demtools_get_default_civicrm_menu_items() {
  // @TODO: Remove this once we're no longer running any servers with PHP 5.5.
  return array(
    '5',  //Search -> Full-text Search
    '6',  //Search -> Search Builder
    '9',  //Search -> Find Mailings
    '11', //Search -> Find Participants
    '13', //Search -> Find Activites
    '14', //Search -> custom searches
    '17', //Contacts -> new Household
    '60', //Event -> Personal Campaign Pages
    '62', //Events -> new price set
    '63', //Events -> manage price set
  );
}

/**
 * Set visibility for a list of menu items.
 */
function _demtools_set_menu_item_visibility(array $menu_items, bool $visible) {
  $active = (int) $visible;
  foreach ($menu_items as $menu_item) {
    CRM_Core_DAO::executeQuery("UPDATE civicrm_navigation SET is_active = {$active} WHERE id = {$menu_item}");
  }
}

/**
 * Given a list of CiviCRM components, ensure *only* those components are
 * enabled.
 */
function _demtools_enable_only_components(array $components) {
  $result = civicrm_api3('Setting', 'create', array(
    'sequential' => 1,
    'enable_components' => $components,
  ));
}

/**
 * Append javascript snippets to hide page elements.
 */
function _demtools_simplify_ui_alterContent(&$content, $context, $tplName, &$object) {
/*** Replaces old template overrides ***/
  switch($tplName) {
    case "CRM/Activity/Form/Activity.tpl":
      $content .= '
        <script type="text/javascript">
        cj(".crm-activity-form-block-attachment").hide();
        </script>';
      break;
    case "CRM/Event/Form/ManageEvent/EventInfo.tpl":
      $content .= '
        <script type="text/javascript">
          cj(".crm-event-manage-eventinfo-form-block-is_map").hide();
          cj(".crm-event-manage-eventinfo-form-block-is_public").hide();
          cj(".crm-event-manage-eventinfo-form-block-is_share").hide();
        </script>';
      break;
    case "CRM/Admin/Page/Tag.tpl":
      $content .= '
        <script type="text/javascript">
          cj(".crm-tag-used_for").hide();
          cj("th:contains(\'Used For\')").hide();
          cj(".crm-tag-is_reserved").hide();
          cj("th:contains(\'Reserved?\')").hide()
        </script>';
      break;
    case "CRM/Contact/Form/Contact.tpl":
      $content .= '
        <script type="text/javascript">
          cj("label[for=\'external_identifier\']").parent().hide();
          cj("label[for=\'image_URL\']").parent().hide();
          cj("label[for=\'internal_identifier_display\']").text("Internal Identifier");
          cj("div.collapsible-title:contains(\'Signature\')").hide();
          cj("label[for=\'prefix_id\']").parent().hide();
          cj("label[for=\'suffix_id\']").parent().hide();
          cj("label[for=\'nick_name\']").parent().hide();
          cj("label[for=\'contact_sub_type\']").parent().hide();
          cj("span:contains(\'Suffix\')").hide();
          cj("input[name*=\'postal_code_suffix\']").hide();
          cj("input[name*=\'[street_address]\']").parent().append(\'<br>\');
        </script>';
      break;
  }
}
