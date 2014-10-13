<?php
civicrm_initialize();
$config=CRM_Core_Config::singleton( ); 
print_r($config->lcMessages);
print ts('New Contacts by Month',array('domain' => 'org.ndi.ndicivimp'));
print "\n";
CRM_Core_Error::backtrace();
