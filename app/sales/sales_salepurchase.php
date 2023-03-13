<?php
require_once "../../config.php";
require_once CLASS_PATH."Data.class.php";
require_once CLASS_PATH."XmlCollection.php";
require_once CLASS_PATH."XmlData.php";
require_once BASE_PATH."general.php";

$plantTypes = Config::getData('planttypes');
$productTypes = Config::getData('producttype');
$currencies = Config::getData('currencies');
$id=$_COOKIE['id'];

if($id=="sales_salepurchasedetail")
	$id=$_COOKIE['typesp'];

$xmlfile=$azxml;
if($id=="sales_purchase"){
	$xmlfile=$arxml;
}

$results['planttypes']=$plantTypes;
$results['producttypes']=$productTypes;
$results['currencies']=$currencies;
$xml = new XmlData($caseStudyId,$xmlfile);

	switch($_POST['action']){	
		case 'get':		
			$ctData = $xml->getAll();
			$results['ctData'] = $ctData;
			echo (json_encode($results));
		break;

		case 'update':
				if($_POST['data']['id']==0){
					unset($_POST['data']['id']);
					$xml->add($_POST['data']);
				}else{
					$xml->deleteById($_POST['data']['id']);	
					unset($_POST['data']['id']);
					$xml->add($_POST['data']);
				}
			break;

		case 'delete':
			if(isset($_POST['id'])){
				$xml->deleteById($_REQUEST['id']);
				}
		break;			
	}
?>