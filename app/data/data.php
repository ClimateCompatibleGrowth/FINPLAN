<?php
require_once "../../config.php";
require_once CLASS_PATH."Data.class.php";
require_once CLASS_PATH."XmlCollection.php";
require_once CLASS_PATH."XmlData.php";
require_once BASE_PATH."general.php";

$results['startYear'] = $startYear;
$results['endYear'] = $endYear;
$results['baseCurrency'] = $baseCurrency;
$results['curTypeSel'] = $curTypeSel;

$bothCurr = $baseCurrency;
$bothCurrBase = $baseCurrency;

if($curTypeSel){
	$bothCurr = $curTypeSel.','.$baseCurrency;
	$bothCurrBase = $baseCurrency.','.$curTypeSel;
}

$results['bothCurr'] = $bothCurr;
$results['bothCurrBase'] = $bothCurrBase;

$results['casestudyid'] = $caseStudyId;
$results['user_path'] = USER_CASE_PATH;

if(file_exists(USER_CASE_PATH.$caseStudyId."/datanotes.json"))
    $dataNotes= json_decode(file_get_contents(USER_CASE_PATH.$caseStudyId."/datanotes.json"), true);

$action = $_REQUEST['action'];
$id = $_REQUEST['id'];
$results['datanotes']=$dataNotes[$id];
$currencies = Config::getData('currencies');
// echo "CUUR";
// print_r($currencies);
$results['currencies']=$currencies;
switch ($id)
{
    case 'general_information':
        $studyTypes = Config::getData('studytypes');
        $cg = 'geninf_data';
        $dc = new XmlCollection($caseStudyId,$cg);
        $ahData = $dc->getoneById();
        $apd = new XmlData($caseStudyId,'plant_data');
        $plant_data = $apd->getAll();
        $results['plantdata']=$plant_data;
        $results['studtype']=$studyTypes;
        $results['geninf']=$ahData;
        $results['currencies']=$currencies;
    break;

    case 'general_inflation':
        $xml = new Data($caseStudyId, $ajxml);
        if ($action == 'get')
        {
            $ceData = $xml->getByField(1, 'sid');
            $results['ceData'] = $ceData;
        }
    break;

    case 'general_exchangerate':
        $xml = new Data($caseStudyId, $aixml);
        if ($action == 'get')
        {
            $ceData = $xml->getByField(1, 'sid');
            $results['ceData'] = $ceData;
        }
    break;

    case 'taxation_depreciation':
        $TaxType = Config::getData('TaxType');
        $xml = new Data($caseStudyId, $atxml);
        if ($action == 'get')
        {
            $caData = $xml->getRow();
            $results['caData'] = $caData;
            $results['TaxType']=$TaxType;
        }
    break;

    case 'taxation_royalty':
        $xml = new Data($caseStudyId, $btxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'financialmanager_equity':
        $xml = new Data($caseStudyId, $blxml);
        $aad = new Data($caseStudyId,$aaxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1, 'sid');
            $results['ctData'] = $ctData;

            $aaData = $aad->getByField('1','sid');	
            $results['aaData'] = $aaData;
        }
    break;

    case 'financialmanager_loans':
        $xml = new Data($caseStudyId,$loans_data);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'financialmanager_bonds':
        $xml = new Data($caseStudyId,$bmxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'financialmanager_other':
        $bod = new XmlData($caseStudyId,$boxml);
        $cqd = new XmlData($caseStudyId,$cqxml);
        $bnd = new XmlData($caseStudyId,$bnxml);

        if($action=="get"){           
            $cfData = $bod->getByfield(1,'sid');
            $sfData = $cqd->getByfield(1,'sid');
            $bnData = $bnd->getByField(1,'sid');//balace data
            $results['cfData']=$cfData;
            $results['sfData']=$sfData;
            $results['bnData']=$bnData;
        }
    break;

    //case data    
    case 'taxation_royalty':
        $xml = new Data($caseStudyId,$btxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'balancesheet_initial':
        $xml = new Data($caseStudyId,$aaxml);
        if ($action == 'get')
        {
            $ctData = $xml->getRow();
            $results['ctData'] = $ctData;
        }
    break;

    case 'balancesheet_investment':
        $xml = new Data($caseStudyId,$brxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'balancesheet_oldloans':
        $xml = new Data($caseStudyId,$bvxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'balancesheet_oldbonds':
        $xml = new Data($caseStudyId,$bxxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'sales_consumers':
        $xml = new Data($caseStudyId,$bsxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'sales_revenues':
        $xml = new Data($caseStudyId,$cuxml);
        if ($action == 'get')
        {
            $ctData = $xml->getByField(1,'sid');
            $results['ctData'] = $ctData;
        }
    break;

    case 'sales_salepurchasedetail':
        $salepurchaseid=$_COOKIE['salepurchaseid'];
        $productTypes = Config::getData('producttype');
        $results['producttypes']=$productTypes;
        $results['id']=$salepurchaseid;

        $typesp=$_COOKIE['typesp'];
        $xmlfile=$azxml;
        if($typesp=="sales_purchase"){
	        $xmlfile=$arxml;
        }

        $xml = new XmlData($caseStudyId,$xmlfile);

        if($salepurchaseid>0){
            $data = $xml->getById($salepurchaseid);
            $results['data'] = $data;
        }

    break;

    case 'plant_general':
        $productTypes = Config::getData('producttype');
        $apd = new XmlData($caseStudyId,$apxml);
        $plantid=$_COOKIE['plantid'];
        $results['producttypes']=$productTypes;
        $data = $apd->getById($plantid);
        $results['data']=$data;
    break;

    case 'plant_production':
        $productTypes = Config::getData('producttype');
        $apd = new XmlData($caseStudyId,$apxml);
        $xml = new XmlData($caseStudyId,$aqxml);
        $plantid=$_COOKIE['plantid'];
        if ($action == 'get')
        {
            $ceData = $apd->getById($plantid);
            $cfData = $xml->getByfield($plantid,'pid');
            $data = $apd->getAll();
            $results['ceData']=$ceData;
            $results['cfData']=$cfData;
            $results['data']=$data;
            $results['id']=$plantid;
        }
        $results['producttypes']=$productTypes;

        break;

        case 'plant_omcosts':
            $productTypes = Config::getData('producttype');
            $apd = new XmlData($caseStudyId,$apxml);
            $xml = new XmlData($caseStudyId,$aoxml);
            $plantid=$_COOKIE['plantid'];
            if ($action == 'get')
            {
                $ceData = $apd->getById($plantid);
				$cfData = $xml->getByField($plantid,'pid');
                $data = $apd->getAll();
                $results['ceData']=$ceData;
                $results['cfData']=$cfData;
                $results['data']=$data;
                $results['id']=$plantid;
            }
            $results['producttypes']=$productTypes;
    
            break;

            case 'plant_fuelcosts':
                $productTypes = Config::getData('producttype');
                $apd = new XmlData($caseStudyId,$apxml);
                $xml = new XmlData($caseStudyId,$amxml);
                $plantid=$_COOKIE['plantid'];
                if ($action == 'get')
                {
                    $ceData = $apd->getById($plantid);
                    $cfData = $xml->getByField($plantid,'pid');
                    $data = $apd->getAll();
                    $results['ceData']=$ceData;
                    $results['cfData']=$cfData;
                    $results['data']=$data;
                    $results['id']=$plantid;
                }
                $results['producttypes']=$productTypes;
        
                break;

                case 'plant_generalexpenses':
                    $productTypes = Config::getData('producttype');
                    $apd = new XmlData($caseStudyId,$apxml);
                    $xml = new XmlData($caseStudyId,$buxml);
                    $plantid=$_COOKIE['plantid'];
                    if ($action == 'get')
                    {
                        $ceData = $apd->getById($plantid);
                        $cfData = $xml->getByField($plantid,'pid');
                        $data = $apd->getAll();
                        $results['ceData']=$ceData;
                        $results['cfData']=$cfData;
                        $results['data']=$data;
                        $results['id']=$plantid;
                    }
                    $results['producttypes']=$productTypes;
                break;

                case 'plant_investments':
                    $productTypes = Config::getData('producttype');
                    $apd = new XmlData($caseStudyId,$apxml);
                    $xml = new XmlData($caseStudyId,$anxml);
                    $plantid=$_COOKIE['plantid'];
                    if ($action == 'get')
                    {
                        $ceData = $apd->getById($plantid);
                        $cfData = $xml->getByField($plantid,'pid');
                        $data = $apd->getAll();
                        $results['ceData']=$ceData;
                        $results['cfData']=$cfData;
                        $results['data']=$data;
                        $results['id']=$plantid;
                    }
                    $results['producttypes']=$productTypes;
                break;

                case 'plant_depreciation':
                    $productTypes = Config::getData('producttype');
                    $apd = new XmlData($caseStudyId,$apxml);
                    $xml = new XmlData($caseStudyId,$alxml);
                    $plantid=$_COOKIE['plantid'];
                    if ($action == 'get')
                    {
                        $ceData = $apd->getById($plantid);
                        $cfData = $xml->getByField($plantid,'pid');
                        $results['ceData']=$ceData;
                        $results['cfData']=$cfData;
                        $results['id']=$plantid;
                    }
                    $results['producttypes']=$productTypes;
                break;

                case 'plant_decommissioning':
                    $productTypes = Config::getData('producttype');
                    $apd = new XmlData($caseStudyId,$apxml);
                    $xml = new XmlData($caseStudyId,$akxml);

                    $plantid=$_COOKIE['plantid'];
                    if ($action == 'get')
                    {
                        $ceData = $apd->getById($plantid);
                        $cfData = $xml->getByField($plantid,'pid');
                        $results['ceData']=$ceData;
                        $results['cfData']=$cfData;
                        $results['id']=$plantid;
                    }
                    $results['producttypes']=$productTypes;
                break;

                case 'plant_sources':
                    $plantid=$_COOKIE['plantid'];
                    $curr=$_COOKIE['curr'];
                    $iddata=$_COOKIE['iddata'];
                    $fid=$curr;
                    $fid .= '_';
				    $fid .= $plantid;
                    $financeSources = Config::getData('financesource');
                    $apd = new XmlData($caseStudyId,$apxml);
                    $and = new XmlData($caseStudyId,$anxml);
                    $xml = new XmlData($caseStudyId,$asxml);
                 //   $add = new XmlData($caseStudyId,$adxml);

                    if ($action == 'get')
                    {
                        $cfData = $xml->getByField($fid,'fid');
                        $ceData = $apd->getById($plantid);
                        $ciData = $and->getByField($plantid,'pid');
                        $results['financesources']=$financeSources;
                        $results['ceData']=$ceData;
                        $results['ciData']=$ciData;
                        $results['cfData']=$cfData;
                        $results['id']=$plantid;
                    }
                break;

                case 'plant_termsfinancing':
                    $plantid=$_COOKIE['plantid'];
                    $curr=$_COOKIE['curr'];
                    $fs=$_COOKIE['fs'];
                    $fid=$curr.'_'.$plantid.'_'.$fs;
                    $financeSources = Config::getData('financesource');
                    $xml = new XmlData($caseStudyId,$auxml);

                    if ($action == 'get')
                    {
                        $and = new XmlData($caseStudyId,$anxml);
                        $apd = new XmlData($caseStudyId,$apxml);	
                        $asd = new XmlData($caseStudyId,$asxml);
                        $cpData = $apd->getById($plantid);
                        $csData = $asd->getByField($plantid,'pid');
                        $ciData = $and->getByField($plantid,'pid');
                        $cfData = $xml->getByField($fid,'fid');
                        $results['financesources']=$financeSources;
                        $results['ciData']=$ciData;
                        $results['cfData']=$cfData;
                        $results['cpData']=$cpData;
                        $results['csData']=$csData;
                        $results['id']=$plantid;
                    }
                break;

}

if ($action == 'edit')
{
    if($_REQUEST["idaction"]=="general_information"){
        $cg = 'geninf_data';
        $dc = new XmlCollection($caseStudyId,$cg);
        $currencies = Config::getData('currencies');
			$currSel=explode(",", $_POST["CurTypeSel"]);
			array_shift($currSel);
			for($a=0; $a<count($currSel); $a++){
				if($currSel[$a]!=="")
				$key = searchForId($currSel[$a], $currencies);
				$currSelId[]=$key;
			}
			$currSelImplode=implode(",",$currSelId);
			$_POST["CurTypeSel"]=$currSelImplode;
            unset($_POST['action']);
            unset($_POST['idaction']);
			$dc->update($_POST);
			if ($_POST["studyName"] != $caseStudyId)
			{
				rename(USER_CASE_PATH . $caseStudyId, USER_CASE_PATH . $_POST["studyName"]);
				setcookie("titlecs", USER_CASE_PATH . $_POST["studyName"], time() + (86400 * 30) , "/");
			}
    }

    switch ($_REQUEST["id"]){
        case 'financialmanager_other':
            $type=$_REQUEST["type"];
            if($type=='otherfinancial'){
                $bod->deleteById($_POST['other']['id']);
                unset($_POST['other']['id']);
                $bod->add($_POST['other']);	
            }

            if($type=='shareholders'){
                $cqd->deleteById($_POST['other']['id']);
                unset($_POST['other']['id']);
                $cqd->add($_POST['other']);
            }
            if($type=='terms'){
                $bnd->deleteById($_POST['other']['id']);
                unset($_POST['other']['id']);
                $bnd->add($_POST['other']);	
            }
        break;

        case 'plant_production':
        case 'plant_omcosts':
        case 'plant_fuelcosts':
        case 'plant_generalexpenses':
        case 'plant_investments':
        case 'plant_depreciation':
        case 'plant_decommissioning':
            $data=json_decode($_POST['data'], true);
            $pid=$_COOKIE['plantid'];
            $iddata=$_COOKIE['iddata'];
            $data['pid']=$pid;
            unset($data['sid']);
            $xml->deleteById($iddata);
			$xml->add($data);
        break;   

        case 'plant_sources':
            $data=json_decode($_POST['data'], true);
            $plantid=$_COOKIE['plantid'];
            $curr=$_COOKIE['curr'];
            $fid=$curr.'_'.$plantid;
            $data['fid']=$fid;
            $data['cid']=$curr;
            $data['pid']=$plantid;
            unset($data['sid']);
            unset($data['curr']);
            $xml->deleteById($iddata);
			$xml->add($data);
        break;

        case 'plant_termsfinancing':
            $data=json_decode($_POST['data'], true);
            $pid=$_COOKIE['plantid'];
            $iddata=$_COOKIE['iddata'];
            $data['pid']=$pid;
            $curr=$_COOKIE['curr'];
            $fs=$_COOKIE['fs'];
            $fid=$curr.'_'.$plantid.'_'.$fs;
            $data['fid']=$fid;
            unset($data['sid']);
            $xml->deleteById($iddata);
			$xml->add($data);
        break;   

        default:
        if($xml!=null){
            $xml->deleteByField(1, 'sid');
            $xml->add(json_decode($_POST['data'], true));
            
            $dataNotes[$id]=$_POST['datanotes'];
            $json_data = json_encode($dataNotes);
            file_put_contents(USER_CASE_PATH.$caseStudyId."/datanotes.json", $json_data);
        }
        break;
    }
}
else
{
    echo (json_encode($results));
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
