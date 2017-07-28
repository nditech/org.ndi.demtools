<?php

/**
 * Define a new managed dashboard entity.
 */
function _demtools_contactpermonth_managed() {
  return array(
    'module' => 'org.ndi.demtools',
    'name' => 'contactpermonth',
    'entity' => 'Dashboard',
    'params' => array(
      'version' => 3,
      "domain_id" => "1",
      "name" => "contact_per_month",
      "label" => "Recently Added",
      "url" => "civicrm/dashlets/contactpermonth?snippet=1",
      "column_no" => "0",
      "is_minimized" => "0",
      "is_fullscreen" => "0",
      "is_active" => "1",
      "is_reserved" => "1",
      "weight" => "0"
    ),
  );
}

/**
 * @TODO: fix or remove this. It was commented-out during the 4.7 upgrade.
 */
function _demtools_contactpermonth_dashboard_defaults($availableDashlets, &$defaultDashlets) {
/*
  $contactID = CRM_Core_Session::singleton()->get('userID');
  try {
    $dashlet = civicrm_api3('DashboardContact', 'get', array(
      'dashboard_id' => $availableDashlets['contact_per_month']['id'],
      'contact_id' => $contactID,
      'is_active' => 1,
    ));
  }
  catch (CiviCRM_API3_Exception $e) {
    $error = $e->getMessage();
  }

  $defaultDashlets[] = array(
    'dashboard_id' => $availableDashlets['contact_per_month']['id'],
    'is_active' => 1,
    'column_no' => 1,
    'contact_id' => $contactID,
  );
*/
}

/**
 * Create the 'contactpermonth' dashlet.
 */
function _demtools_contactpermonth_add_default_dashlet($contactid) {
  $result = civicrm_api3('Dashboard', 'get', array(
    'name' => "contact_per_month",
  ));
  $exists = 0;
  $dashletid = 0;
  if ($result['count'] > 0) {
    $dashletid = $result['id'];
    $result = civicrm_api3('DashboardContact', 'get', array(
      'contact_id' => $contactid,
      'return' => array("dashboard_id", "contact_id"),
    ));
    if ($result['count'] > 0) {
      foreach ($result['values'] as $key => $value) {
        if ($value['dashboard_id'] == $dashletid) {
          $exists = 1;
        }
      }
    }
    if ($exists != 1) {
      $tx = new CRM_Core_Transaction();
      $dashlet = array(
        'dashboard_id' => $dashletid,
        'contact_id' => $contactid,
        'is_active' => 1,
        'column_no' => 0,
        'is_minimized' => 0,
        'is_fullscreen' => 0,
        'weight' => 0,
      );
      try {
        $add = civicrm_api3('DashboardContact', 'create', $dashlet);
      }
      catch (CiviCRM_API3_Exception $e) {
        $tx->rollback();
        echo get_class($e) . ' -- ' . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
        print_r($e->getExtraParams());
      }
    }
  }
}

/**
 * Delete the 'contactpermonth' dashlet.
 */
function _demtools_contactpermonth_remove_default_dashlet($contactid) {
  $result = civicrm_api3('Dashboard', 'get', array(
    'name' => "contact_per_month",
  ));
  $exists = 0;
  if ($result['count'] > 0) {
    $dashletid = $result['id'];
    $result = civicrm_api3('DashboardContact', 'get', array(
      'contact_id' =>$contactid,
      'return' => array("dashboard_id", "contact_id"),
    ));
    foreach ($result['values'] as $key => $value) {
      if ($value['dashboard_id'] == $dashletid) {
        civicrm_api3('DashboardContact', 'delete', array(
          'id' => $value['id'],
        ));
      }
    }
  }
}
