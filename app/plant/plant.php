<?php
require_once "../../config.php";
require_once CLASS_PATH."Data.class.php";
require_once CLASS_PATH."XmlCollection.php";
require_once CLASS_PATH."XmlData.php";
require_once BASE_PATH."general.php";

$plantTypes = Config::getData('planttypes');
$productTypes = Config::getData('producttype');
$apd = new XmlData($caseStudyId,$apxml);
$results['planttypes']=$plantTypes;
$results['producttypes']=$productTypes;

	switch($_POST['action']){	
		case 'get':
			$cg = 'geninf_data';
			$dc = new XmlCollection($caseStudyId,$cg);
			$caseData = $dc->getoneById();
			$data = $apd->getAll();

			$results['caseData']=$caseData;
			$results['data']=$data;
			echo (json_encode($results));
		break;

		case 'getplant':
			$data = $apd->getById($_POST['id']);
			$results['data']=$data;
			echo (json_encode($results));
		break;

		case 'newplant':
			echo (json_encode($results));
		break;

		case 'update':
				if($_POST['data']['id']==""){
					unset($_POST['data']['id']);
					$apd->add($_POST['data']);
				}else{
					$apd->update($_POST['data']);
				}
			break;

		case 'delete':
			if(isset($_POST['id'])){
				$apd->deleteById($_REQUEST['id']);
				$aqd = new XmlData($caseStudyId,$aqxml);
				$aqd->deleteByField($_REQUEST['id'],'pid');
				$akd = new XmlData($caseStudyId,$akxml);
				$akd->deleteByField($_REQUEST['id'],'pid');
				$aod = new XmlData($caseStudyId,$aoxml);
				$aod->deleteByField($_REQUEST['id'],'pid');
				$ald = new XmlData($caseStudyId,$alxml);
				$ald->deleteByField($_REQUEST['id'],'pid');
				$amd = new XmlData($caseStudyId,$amxml);
				$amd->deleteByField($_REQUEST['id'],'pid');
				$and = new XmlData($caseStudyId,$anxml);
				$and->deleteByField($_REQUEST['id'],'pid');
				$bud = new XmlData($caseStudyId,$buxml);
				$bud->deleteByField($_REQUEST['id'],'pid');
				$asd = new XmlData($caseStudyId,$asxml);		// multiple records?
				$aud = new XmlData($caseStudyId,$auxml);
				$financeSources = Config::getData('financesource');
				for($c = 0; $c < count($allChunks); $c++){
					$fldid = $allChunks[$c].'_'.$_REQUEST['id'];
					$asd->deleteByField($fldid,'fid');
					foreach($financeSources as $financesource){
						$fldfid = $fldid.'_'.$financesource['id'];
						$aud->deleteByField($fldfid,'fid');
					}
				}
			}
		break;
						
	}

	function searchForId($id, $array) {
		foreach ($array as $key => $val) {
			if ($val['value'] === $id) {
				return $val['id'];
			}
		}
		return null;
	 }
?>