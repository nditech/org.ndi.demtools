<?php

require_once 'ndicivimp.civix.php';

/*** Create New Permissions ***/
function ndicivimp_civicrm_permission( &$permissions ){
  $prefix = ts('NDI CiviCRM County-State Permissions') . ': '; 
  $permissions = array(
    'view county contacts'     => $prefix . ts('view all contacts who live in your county'),
    'edit county contacts'     => $prefix . ts('edit all contacts who live in your county'),
    'delete county contacts'   => $prefix . ts('delete all contacts who live in your county'),
    'view state contacts'      => $prefix . ts('view all contacts who live in your state'),
    'edit state contacts'      => $prefix . ts('edit all contacts who live in your state'),
    'delete state contacts'    => $prefix . ts('delete all contacts who live in your state'),
  ); // NB: note the convention of using delete in ComponentName, plural for edits
}
/*** Add permissions to current user and adds primary address to new table ***/
function ndicivimp_civicrm_aclWhereClause( $type, &$tables, &$whereTables, &$contactID, &$where ){
  require_once 'CRM/Contact/BAO/Contact/Permission.php';
  if ((CRM_Core_Permission::check('view state contacts') && $type == "1")
      || (CRM_Core_Permission::check('edit state contacts') && $type == "2")
        || (CRM_Core_Permission::check('delete state contacts') && $type == "3")){
    $address_table = 'civicrm_address';
    $tables[$address_table] = $whereTables[$address_table] = "LEFT JOIN {$address_table} ON {$address_table}.contact_id = contact_a.id";
    $where = "{$address_table}.state_province_id IN 
								(SELECT state_province_id FROM civicrm_address WHERE contact_id = {$contactID} AND is_primary = 1 )";
  } elseif ((CRM_Core_Permission::check('view county contacts') && $type == "1")
           || (CRM_Core_Permission::check('edit county contacts') && $type == "2")
             || (CRM_Core_Permission::check('delete county contacts') && $type == "3")){
    $address_table = 'civicrm_address';
    $tables[$address_table] = $whereTables[$address_table] = "LEFT JOIN {$address_table} ON {$address_table}.contact_id = contact_a.id";
    $where = "{$address_table}.county_id IN (SELECT county_id FROM civicrm_address WHERE contact_id = {$contactID} AND is_primary = 1 )";
  }
}
/*** Prevents address_id from being overriden, creates address record will NULL id ***/
function ndicivimp_civicrm_postprocess($formName, &$form){
  if (CRM_Utils_Array::value('address', $form->getVar('_values'))){
    $contactID = $form->_contactId;
    $settings =  CRM_Core_BAO_Setting::getItem("Permission Address");
    $custom_id = $settings["custom_id"];
    $addresses = $form->_values['address'];
    $settings =  CRM_Core_BAO_Setting::getItem("Permission Address");
    $submitted = $form->_submitValues["address"];
    $table = "address_permissions";
    foreach ($addresses as $key => $value) { 
    	foreach($submitted[$key] as $k => $v){
     		$keys = explode("_", $k);
     		if ($keys[0] == "custom" && $keys[1] == $custom_id && $v == 1){
    			$sql  = "SELECT address_id FROM {$table} WHERE contact_id = {$contactID} AND address_id = {$value['id']}";
    			$dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
    			if (!$dao->fetch( )) {
      			$insertSql = "INSERT INTO {$table} (contact_id, address_id) VALUES ({$contactID}, {$value['id']})";
      			$dao2 = CRM_Core_DAO::executeQuery($insertSql, CRM_Core_DAO::$_nullArray);
    			}
     		} elseif ( $keys[0] == "custom" && $keys[1] == $custom_id && $v == 0 ){
    			$sql  = "SELECT address_id FROM {$table} WHERE contact_id = {$contactID} AND address_id = {$value['id']}";
    			$dao = CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
          if ($dao->fetch()){
            $sql = "DELETE FROM {$table} WHERE address_id = {$value['id']} AND contact_id = {$contactID}";
            $dao = CRM_Core_DAO::executeQuery($sql);
          }
        } else {
        	$checksql = "SELECT address_id FROM {$table} WHERE address_id = {$value['id']}";
      		$checkdao = CRM_Core_DAO::executeQuery( $checksql, CRM_Core_DAO::$_nullArray );
      		if (!$checkdao->fetch()){
        		$sql = "SELECT id, state_province_id, county_id FROM civicrm_address WHERE id = {$value['id']}";
        		$dao = CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        		$dao->fetch();
        		if ( ($dao->id > 0) && (array_key_exists('county_id', $value) && $value['county_id'] == $dao->county_id) && ($value['state_province_id'] == $dao->state_province_id ) && (!CRM_Core_Permission::check('edit county contacts') || !CRM_Core_Permission::check('edit all contacts') ) ){
          		$value['id'] = "NULL";
        		}
      		}
        }
   		}//end foreach
    }//end foreach
  }//end if
}
/*** Freeze state and county field is user doesn't have permission to change ***/
function ndicivimp_civicrm_buildform($formName, &$form){
  if (CRM_Utils_Array::value('address', $form->getVar('_values')) && CRM_Core_Permission::check('edit all contacts') != "1"){
    foreach ($form->_values['address'] as $key => $value){
      $sql = "SELECT address_id FROM address_permissions WHERE address_id = {$value['id']}";
      $dao = CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
      if ($dao->fetch()){
        foreach ($form->_elements as $k => $v) {
          if(CRM_Utils_Array::value('name', $v->_attributes)){
          	if(strpos($v->_attributes['name'],"county_id")){
            	$element = $form->_elements[$k];
            	$element->_flagFrozen = 1;
          	}
		        if(strpos($v->_attributes['name'],"state_province_id")){
		          $element = $form->_elements[$k];
		          $element->_flagFrozen = 1;
		        }
          }
        }
      }
    }
  }
}

function ndicivimp_add_default_dashboard($contactid){
  $result = civicrm_api3('Dashboard', 'get', array(
    'name' => "contact_per_month",
  ));
  $exists=0;
$dashletid=0;
  if($result['count']>0){
    $dashletid = $result['id'];
    $result = civicrm_api3('DashboardContact','get',array(
      'contact_id' =>$contactid,
'return' => array("dashboard_id", "contact_id"),
    ));
    if($result['count']>0){
      foreach ($result['values'] as $key => $value){
        if($value['dashboard_id']==$dashletid){
          $exists = 1; 
        }
      }
    }
    if($exists!=1){
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
      $add=civicrm_api3('DashboardContact', 'create', $dashlet);
} catch (CiviCRM_API3_Exception $e) {
  $tx->rollback();
  echo get_class($e) . ' -- ' . $e->getMessage() . "\n";
  echo $e->getTraceAsString() . "\n";
  print_r($e->getExtraParams());
}
    }//end if exists
  }//end if dashlet found
}

function ndicivimp_remove_default_dashboard($contactid){
  $result = civicrm_api3('Dashboard', 'get', array(
    'name' => "contact_per_month",
  ));
  $exists=0;
  if($result['count']>0){
    $dashletid = $result['id'];
    $result = civicrm_api3('DashboardContact','get',array(
      'contact_id' =>$contactid,
'return' => array("dashboard_id", "contact_id"),
    ));
    foreach ($result['values'] as $key => $value){
      if($value['dashboard_id'==$dashletid]){
        civicrm_api3('DashboardContact','delete',array(
          'id'=>$value['id']
        ));
      }
    }
  }//end if dashlet found
}


/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function ndicivimp_civicrm_install() {
  //set homepage = civicrm
  variable_set('site_frontpage','civicrm');

$result = civicrm_api3('Setting', 'create', array(
  'address_options' => array("1", "2", "4","5","7","8","9"),
  'address_format' => "{contact.address_name}\\n{contact.street_address}\\n{contact.supplemental_address_1}\\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\\n{contact.county}{ }{contact.country}"));

  $sql = 'DROP TABLE IF EXISTS address_permissions';
  $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
  $sql = 'CREATE TABLE address_permissions ( address_id int, contact_id int );';
  $dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
  $params = array (
        'version'=>'3',
        'name' => 'permissioned_address',
        'name' => 'permissioned_address',
        'title' => 'Permissioned Address',
        'extends' => 'Address',
        'style' => 'Inline',
        'collapse_display' => '0',
        'weight' => '3',
        'is_active' => '1',
        'is_multiple' => '0');
  $results=civicrm_api('CustomGroup','create', $params);
  $gid=$results['id'];
  $params = array('version'=>'3',
                "custom_group_id"=>$gid,
                "name"=>"is_permissioned",
                "label"=>"Is Permissioned",
                "data_type"=>"Boolean",
                "html_type"=>"Radio",
                "help_post" => "If enabled this contact will have permission over contacts in this graphical region",
                "is_required"=>"0",
                "is_searchable"=>"1",
                "is_search_range"=>"0",
                "weight"=>"2",
                "is_active"=>"1",
                "is_view"=>"0",
                "text_length"=>"255",
                "note_columns"=>"60",
                "note_rows"=>"4",);
  $results=civicrm_api('CustomField','create', $params);
  $fid = $results["id"];
  CRM_Core_BAO_Setting::setItem($fid, "Permission Address", "custom_id");
  CRM_Core_BAO_Setting::setItem($gid, "Permission Address", "group_id");
  return _ndicivimp_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function ndicivimp_civicrm_uninstall() {
  $sql = 'DROP TABLE IF EXISTS address_permissions';
  //$dao = CRM_Core_DAO::executeQuery($sql, CRM_Core_DAO::$_nullArray);
  return _ndicivimp_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_pageRun
 */
function ndicivimp_civicrm_pageRun(&$page ) {
$pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Case_Page_DashBoard') { 
}
}

/**
 * Implementation of hook_civicrm_pageRun
 */
function ndicivimp_civicrm_dashboard_defaults($availableDashlets, &$defaultDashlets){
$contactID = CRM_Core_Session::singleton()->get('userID');
/*
  try{
   $dashlet = civicrm_api3('DashboardContact', 'get', array(
      'dashboard_id'  =>  $availableDashlets['contact_per_month']['id'],
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
 * Implementation of hook_civicrm_dashboard
 */
function ndicivimp_civicrm_dashboard( $contactID, &$contentPlacement ) {
ndicivimp_add_default_dashboard($contactID);
  //Communication
  $sendMailing = CRM_Utils_System::url('civicrm/mailing/send', $query = 'reset=1' );
    //CRM_Core_Resources::singleton()->addStyleFile('org.ndi.ndicivimp', 'css/bootstrap.min.css');
#    CRM_Core_Resources::singleton()->addStyleUrl('http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css');
#    CRM_Core_Resources::singleton()->addStyleUrl('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css');
  //Manage Contacts
  $newIndLink = CRM_Utils_System::url('civicrm/contact/add', $query = 'reset=1&ct=Individual' );
  $browseContacts = CRM_Utils_System::url('civicrm/contact/search', $query = 'reset=1&force=1' );
  $manageGroupLink = CRM_Utils_System::url('civicrm/group', $query = 'reset=1' );
  $viewAllReports = CRM_Utils_System::url('civicrm/report/list', $query = 'reset=1' );

  //Manage Events
  $newEvent = CRM_Utils_System::url('civicrm/event/add', $query = 'reset=1&action=add' );
  $manageEvents = CRM_Utils_System::url('civicrm/event/manage', $query = 'reset=1' );
  $searchParticipants = CRM_Utils_System::url('civicrm/event/search', $query = 'reset=1' );
  $registerParticipant = CRM_Utils_System::url('civicrm/participant/add', $query = 'reset=1&action=add&context=standalone' );
  $scheduleReminder = CRM_Utils_System::url('civicrm/admin/scheduleReminders', $query = 'reset=1' );




  $contentPlacement =2;
  return array(
 //'<h2>Welcome</h2>' => "<p>Welcome to your CiviCRM Dashboard<p>",

                  '<h2>'.ts("Contacts",array('domain' => 'org.ndi.ndicivimp')).'</h2>' =>
                    "<p>
                    <a href='".$newIndLink."'><button type=\"button\" class=\"btn btn-primary\">".ts('Create New Individual',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    <a href='".$browseContacts."'><button type=\"button\" class=\"btn btn-primary\">".ts('Browse Contacts',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                   <a href='".$manageGroupLink."'><button type=\"button\" class=\"btn btn-primary\">".ts('Manage Groups',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    <a href='".$viewAllReports."'><button type=\"button\" class=\"btn btn-primary\">".ts('View All Reports',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    <a href='".$sendMailing."'><button type=\"button\" class=\"btn btn-primary\">".ts('Send Mailing',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    </p>
                  ",
                  '<h2>'.ts("Events",array('domain' => 'org.ndi.ndicivimp')).'</h2>' =>
                    "<p>
                    <a href='".$newIndLink."'><button type=\"button\" class=\"btn btn-success\">".ts('Organize Event',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    <a href='".$manageEvents."'><button type=\"button\" class=\"btn btn-success\">".ts('All Events',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                   <a href='".$searchParticipants."'><button type=\"button\" class=\"btn btn-success\">".ts('Search Participants',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                    <a href='".$registerParticipant."'><button type=\"button\" class=\"btn btn-success\">".ts('Register Participant',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                      <a href='".$scheduleReminder."'><button type=\"button\" class=\"btn btn-success\">".ts('Schedule Reminder',array('domain' => 'org.ndi.ndicivimp'))."</button></a>
                  </p>
                  ",
    );
}

function disable_components(){
  $result = civicrm_api3('Setting', 'create', array(
  	'debug' => 1,
  	'sequential' => 1,
  	'enable_components' => array("CiviEvent","CiviMail","CiviReport","CiviCase"),
	));
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 5');//Search -> Full-text Search
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 6');//Search -> Search Builder
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 9');//Search -> Find Mailings
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 11');//Search -> Find Participants
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 13');//Search -> Find Activites
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 14');//Search -> custom searches
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 17');//Contacts -> new Household
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 60');//Event -> Personal Campaign Pages
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 62');//Events -> new price set
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE id = 63');//Events -> manage price set
}

function enable_components(){
   $result = civicrm_api3('Setting', 'create', array(
  	'debug' => 1,
  	'sequential' => 1,
  	'enable_components' => array("CiviEvent","CiviMail","CiviReport", "CiviContribute", "CiviMember", "CiviPledge","CiviCase"),
	));
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 5');//Search -> Full-text Search
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 6');//Search -> Search Builder
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 9');//Search -> Find Mailings
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 11');//Search -> Find Participants
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 13');//Search -> Find Activites
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 14');//Search -> custom searches
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 17');//Contacts -> new Household
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 60');//Event -> Personal Campaign Pages
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 62');//Events -> new price set
	CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE id = 63');//Events -> manage price set
}


/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function ndicivimp_civicrm_config(&$config) {
include_once 'CRM/Core/BAO/Setting.php';
$address_options =
            CRM_Core_BAO_Setting::getItem(
            'CiviCRM Preferences',
            'address_options',
            NULL,
            NULL,
            NULL,
            1
          );
/*$address_options =
            CRM_Core_BAO_Setting::setItem(
            '145789',
            'CiviCRM Preferences',
            'address_options',
            NULL,
            NULL,
            NULL,
            1
          );
*/
  # This function was causing failures during 4.6->4.7 upgrades. See:
  # https://issues.civicrm.org/jira/browse/CRM-19915
  #_ndicivimp_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function ndicivimp_civicrm_xmlMenu(&$files) {
  _ndicivimp_civix_civicrm_xmlMenu($files);
}


/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function ndicivimp_civicrm_enable() {
disable_components();
  return _ndicivimp_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function ndicivimp_civicrm_disable() {
enable_components();
  return _ndicivimp_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function ndicivimp_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _ndicivimp_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function ndicivimp_civicrm_managed(&$entities) {
  $entities[] = array(
    'module' => 'org.ndi.ndicivimp',
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

  return _ndicivimp_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function ndicivimp_civicrm_caseTypes(&$caseTypes) {
  _ndicivimp_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function ndicivimp_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _ndicivimp_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
