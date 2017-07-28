<?php

/**
 * @TODO: This doesn't appear to actually do anything.
 */
function _demtools_dashboard_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Case_Page_DashBoard') { 
  }
}

/**
 * Build dashboard HTML.
 */
function _demtools_build_dashboard() {
  $contactsHeader = '<h2>' . ts("Contacts", array('domain' => 'org.ndi.demtools')) . '</h2>';
  $contactsButtons = array(
    'Create New Individual' => array('url' => 'civicrm/contact/add', 'query' => 'reset=1&ct=Individual'),
    'Browse Contacts' => array('url' => 'civicrm/contact/search', 'query' => 'reset=1&force=1'),
    'Manage Groups' => array('url' => 'civicrm/group', 'query' => 'reset=1'),
    'View All Reports' => array('url' => 'civicrm/report/list', 'query' => 'reset=1'),
    'Send Mailing' => array('url' => 'civicrm/mailing/send', 'query' => 'reset=1'),
  );
  $eventsHeader = '<h2>' . ts("Events", array('domain' => 'org.ndi.demtools')) . '</h2>';
  $eventsButtons = array(
    'Organize Event' => array('url' => 'civicrm/event/add', 'query' => 'reset=1&action=add'),
    'All Events' => array('url' => 'civicrm/event/manage', 'query' => 'reset=1'),
    'Search Participants' => array('url' => 'civicrm/event/search', 'query' => 'reset=1'),
    'Register Participant' => array('url' => 'civicrm/participant/add', 'query' => 'reset=1&action=add&context=standalone'),
    'Schedule Reminder' => array('url' => 'civicrm/admin/scheduleReminders', 'query' => 'reset=1'),
  );

  $dashboard = array(
    $contactsHeader => _demtools_build_dashboard_body($contactsButtons),
    $eventsHeader => _demtools_build_dashboard_body($eventsButtons),
  );

  return $dashboard;
}

/**
 * Build dashboard section HTML from a list of buttons.
 */
function _demtools_build_dashboard_body($buttons) {
  // Build links.
  foreach ($buttons as $name => $link) {
    $buttons[$name] = CRM_Utils_System::url($link['url'], $link['query']);
  }
  // Translate names.
  foreach ($buttons as $name => $link) {
    // Remove the original first, in case the site is in English.
    unset($buttons[$name]);
    $buttons[ts($name, array('domain' => 'org.ndi.demtools'))] = $link;
  }
  // Build body.
  $body = '<p>';
  foreach ($buttons as $name => $link) {
    $body .= "<a class='button' href='$link'><span>$name</span></a>";
  }
  $body .= '</p>';

  return $body;
}

