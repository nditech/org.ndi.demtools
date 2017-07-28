<?php

require_once 'CRM/Core/Page.php';

class CRM_DemTools_Page_ContactPerMonth extends CRM_Core_Page {
  function run() {
    $date_ranges = array(
      'thisMonth' => '',
      'lastMonth' => '- INTERVAL 1 MONTH',
      'twoMonths' => '- INTERVAL 2 MONTH',
      'threeMonths' => '- INTERVAL 3 MONTH',
      'fourMonths' => '- INTERVAL 4 MONTH',
      'fiveMonths' => '- INTERVAL 5 MONTH',
      'sixMonths' => '- INTERVAL 6 MONTH',
    );
    $createdArray = array();
    foreach ($date_ranges as $name => $date_range) {
      // $sql = "SELECT count(id) as createdtotal, DATE_FORMAT(MONTH(CURRENT_DATE ".$date_range."), '%M') as month  FROM civicrm_contact WHERE MONTH(created_date) = MONTH(CURRENT_DATE ".$date_range.") AND YEAR(created_date) = YEAR(CURRENT_DATE ".$date_range.");";
  $sql = "SELECT count(id) as createdtotal  FROM civicrm_contact WHERE MONTH(created_date) = MONTH(CURRENT_DATE ".$date_range.") AND YEAR(created_date) = YEAR(CURRENT_DATE ".$date_range.");";

      $dao = CRM_Core_DAO::executeQuery($sql);
      if ($dao->fetch()){
        $createdArray[$name] = $dao->createdtotal;
      }
    }
    $createdAgg = array();
    $createdTotal = array_sum($createdArray);
    foreach ($createdArray as $name => $created){
      if ($createdTotal<50)
        $createdAgg[$name] = $created;
      elseif ($createdTotal < 300 && $createdTotal >=50)
        $createdAgg[$name] = $created/10;
      elseif ($createdTotal < 1000 && $createdTotal >=300)
        $createdAgg[$name] = $created/100;
      else
        $createdAgg[$name] = $created/1000;
    }
    $this->assign('createdArray', $createdArray);
    $this->assign('createdAgg', $createdAgg);

    parent::run();
  }
}
