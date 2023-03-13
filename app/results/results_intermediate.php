<?php
	require_once "../../config.php";
	require_once BASE_PATH."general.php";
	require_once CLASS_PATH."XmlData.php";
	require_once CLASS_PATH."Data.class.php";

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

	$currencies = Config::getData('currencies');
	$results['currencies']=$currencies;
	$results['tableid']=$_REQUEST['id'];
    //Manage files
    switch($_REQUEST['id']){
		case "1.1.":
			$aad = new XmlData($caseStudyId,$aaxml);
			$aaData = $aad->getAll();
			foreach($aaData as $row){
				$tid = $row['id'];
			}
			
			$cod = new XmlData($caseStudyId,$coxml);
			$coData = $cod->getByField('1','sid');
			
			$cad = new XmlData($caseStudyId,$caxml);
			$caData = $cad->getByField(1,'sid');
			
			$cbd = new XmlData($caseStudyId,$cbxml);
			$cbData = $cbd->getByField(1,'sid');

			$data = $aad->getById($tid);
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['currencies']=$currencies;
			$results['caData']=$caData;
			$results['cbData']=$cbData;
			$results['coData']=$coData;
        	echo (json_encode($results));
			break;
		case '2.1.':
		case '2.2.':
			$cad = new XmlData($caseStudyId,$caxml);
			$data = $cad->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;	
		
		case "3.1.":
		case "3.2.":
			$cbd = new XmlData($caseStudyId,$cbxml);
			$data = $cbd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;

		case '4.1.':
			$add = new XmlData($caseStudyId,$adxml);
			$data = $add->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "4.2.":
			$acd = new XmlData($caseStudyId,$acxml);
			$data = $acd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "5.1.": 
			$agd = new XmlData($caseStudyId,$agxml);
			$data = $agd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;

		case "5.2.": 
		case "5.3.": 
			$cnd = new XmlData($caseStudyId,$cnxml);
			$data = $cnd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;

		case "6.1.":
			$_loans = new XmlData($caseStudyId,$loans_data);
			$_cal_loans = new XmlData($caseStudyId,$cal_loans);

			$data = $_loans->getByField(1,'sid');
			$datacal=$_cal_loans->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['resultscal']=$datacal;
        	echo (json_encode($results));
		break;

		case "6.2.":
			$xml = new XmlData($caseStudyId,$afxml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $xml->getAll();
			$results['allyears']=$AllYear;
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
				$cptData = $apd->getById($row['pid']);	
				$results['plants'][]=$cptData;
				$results['rows'][]=$row['id'];
				if($i==0){
					$data = $xml->getById($row['id']);
					$results['results']=$data;
					$results['rowid']=$row['id'];
				}
				$i++;
			}
			}
        	echo (json_encode($results));
		break;

		case "loans":
			$xml = new XmlData($caseStudyId,$afxml);
			$pid=$_POST['pid'];
			$rowid=$_POST['rowid'];
			$data = $xml->getById($rowid);
			$results['results']=$data;
			$results['tableid']='6.2.';
			$results['rowid']=$_POST['rowid'];
        	echo (json_encode($results));
		break;

		case "6.3.":
		case "6.4.":
			$cid = new XmlData($caseStudyId,$cixml);
			$data = $cid->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;

		case "7.1.":
		case "7.2.":
			$ccd = new XmlData($caseStudyId,$ccxml);
			$data = $ccd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
        	echo (json_encode($results));
		break;

		case "8.1.":
			$financeSources = Config::getData('financesource');
			$xml = new XmlData($caseStudyId,$avxml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $xml->getAll();
			$results['allyears']=$AllYear;
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
				$cptData = $apd->getById($row['pid']);	
				$results['plants'][]=$cptData;
				$results['rows'][]=$row['id'];
				if($i==0){
					$data = $xml->getById($row['id']);
					$results['results']=$data;
					$results['rowid']=$row['id'];
				}
				$i++;
				}
			}
			$results['financesources']=$financeSources;
        	echo (json_encode($results));
		break;

		case "exportcredits":
			$financeSources = Config::getData('financesource');
			$xml = new XmlData($caseStudyId,$avxml);
			$pid=$_POST['pid'];
			$rowid=$_POST['rowid'];
			$data = $xml->getById($rowid);
			$results['results']=$data;
			$results['tableid']='8.1.';
			$results['rowid']=$_POST['rowid'];
			$results['pid']=$_POST['pid'];
			$results['financesources']=$financeSources;
        	echo (json_encode($results));
		break;

		case "8.2.":
			$financeSources = Config::getData('financesource');
			$bqd = new XmlData($caseStudyId,$bqxml);
			$data = $bqd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['financesources']=$financeSources;
        	echo (json_encode($results));
		break;

		case "8.3.":
		case "8.4.":
		case "8.5.":
			$financeSources = Config::getData('financesource');
			$chd = new XmlData($caseStudyId,$chxml);
			$data = $chd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['financesources']=$financeSources;
        	echo (json_encode($results));
		break;

		case "9.1.":
			$cdd = new XmlData($caseStudyId,$cdxml);
			$data = $cdd->getByField(1,'sid');
			$cnd = new XmlData($caseStudyId,$cnxml);
			$datacn = $cnd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['resultscn']=$datacn;
        	echo (json_encode($results));
		break;

		case "10.1.":
			$cld = new XmlData($caseStudyId,$clxml);
			$clData = $cld->getByField(1,'sid');
			$ctd = new XmlData($caseStudyId,$ctxml);
			$data = $ctd->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['ctData']=$data;
			$results['clData']=$clData;
        	echo (json_encode($results));
		break;

		case "10.2.":
			$cad = new XmlData($caseStudyId,$caxml);
			$caData = $cad->getByField(1,'sid');
			$cbd = new XmlData($caseStudyId,$cbxml);
			$cbData = $cbd->getByField(1,'sid');
			$ccd = new XmlData($caseStudyId,$ccxml);
			$ccData = $ccd->getByField(1,'sid');
			$chd = new XmlData($caseStudyId,$chxml);
			$chData = $chd->getByField(1,'sid');
			$cdd = new XmlData($caseStudyId,$cdxml);
			$cdData = $cdd->getByField(1,'sid');
			$cid = new XmlData($caseStudyId,$cixml);
			$ciData = $cid->getByField(1,'sid');
				
			$cld = new XmlData($caseStudyId,$clxml);
			$clData = $cld->getByField(1,'sid');

			$results['allyears']=$AllYear;
			$results['caData']=$caData;
			$results['cbData']=$cbData;
			$results['ccData']=$ccData;
			$results['cdData']=$cdData;
			$results['chData']=$chData;
			$results['ciData']=$ciData;
			$results['clData']=$clData;
        	echo (json_encode($results));
		break;

		case "11.1.":
			$xml = new XmlData($caseStudyId,$bcxml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $xml->getAll();
			$results['allyears']=$AllYear;
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
				$cptData = $apd->getById($row['pid']);	
				$results['plants'][]=$cptData;
				$results['rows'][]=$row['id'];
				if($i==0){
					$data = $xml->getById($row['id']);
					$results['results']=$data;
					$results['rowid']=$row['id'];
				}
				$i++;
			}
			}
        	echo (json_encode($results));
		break;

		case "fuels":
			$xml = new XmlData($caseStudyId,$bcxml);
			$pid=$_POST['pid'];
			$rowid=$_POST['rowid'];
			$data = $xml->getById($rowid);
			$results['results']=$data;
			$results['tableid']='11.1.';
        	echo (json_encode($results));
		break;

		case "11.2.":
			$xml = new XmlData($caseStudyId,$bexml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "12.1.":
			$xml = new XmlData($caseStudyId,$bbxml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $xml->getAll();
			$results['allyears']=$AllYear;
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
				$cptData = $apd->getById($row['pid']);	
				$results['plants'][]=$cptData;
				$results['rows'][]=$row['id'];
				if($i==0){
					$data = $xml->getById($row['id']);
					$results['results']=$data;
					$results['rowid']=$row['id'];
				}
				$i++;
			}
			}
        	echo (json_encode($results));
		break;

		case "omcosts":
			$xml = new XmlData($caseStudyId,$bbxml);
			$pid=$_POST['pid'];
			$rowid=$_POST['rowid'];
			$data = $xml->getById($rowid);
			$results['results']=$data;
			$results['tableid']='12.1.';
        	echo (json_encode($results));
		break;

		case "12.2.":
			$xml = new XmlData($caseStudyId,$bdxml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "13.1.":
			$xml = new XmlData($caseStudyId,$bgxml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "13.2.":
			$xml = new XmlData($caseStudyId,$bgxml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $xml->getAll();
			$results['allyears']=$AllYear;
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
				$cptData = $apd->getById($row['pid']);	
				$results['plants'][]=$cptData;
				$results['rows'][]=$row['id'];
				if($i==0){
					$data = $xml->getById($row['id']);
					$results['results']=$data;
					$results['rowid']=$row['id'];
				}
				$i++;
				}
			}
        	echo (json_encode($results));
		break;

		case "depreciation":
			$xml = new XmlData($caseStudyId,$bgxml);
			$pid=$_POST['pid'];
			$rowid=$_POST['rowid'];
			$data = $xml->getByField($pid,'pid');
			$results['results']=$data;
			$results['tableid']='13.2.';
        	echo (json_encode($results));
		break;

		case "14.1.":
			$xml = new XmlData($caseStudyId,$cexml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "14.2.":
			$xml = new XmlData($caseStudyId,$cexml);
			$apd = new XmlData($caseStudyId,$apxml);
			$caData = $apd->getAll();
			$i=0;
			foreach($caData as $row){
				if($row['id']!= null ){
					if($i==0)
					$pid=$row['id'];
				}
				$i++;
			}
			$data=$xml->getByField(1,'sid');
			$results['allyears']=$AllYear;
			$results['plants']=$caData;
			$results['results']=$data;
			$results['pid']=$pid;
        	echo (json_encode($results));
		break;

		case "decomissioning":
			$xml = new XmlData($caseStudyId,$bgxml);
			$pid=$_POST['pid'];
			$data = $xml->getByField($pid,'pid');
			$results['results']=$data;
			$results['tableid']='14.2.';
			$results['pid']=$pid;
        	echo (json_encode($results));
		break;

		case "15.1.":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$ayxml);
			$caData = $xml->getAll();
			if(is_array($caData) && count($caData) > 0){
				$i=0;
				foreach($caData as $row){
					if($i==0){
						$curr=$row['TradeCurrency'];
					}
					if($row['TradeCurrency']==$curr){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
						$results['cid']=$row['TradeCurrency'];
					}
						$i++;
					}
			}
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "salescurrency":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$ayxml);
			$caData = $xml->getAll();
			$cid=$_POST['pid'];
			if(is_array($caData) && count($caData) > 0){
				foreach($caData as $row){
					if($row['TradeCurrency']==$cid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
					}
				}
			}
			$results['results']=$data;
			$results['tableid']='15.1.';
			$results['cid']=$cid;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "15.2.":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$ayxml);
			$caData = $xml->getAll();
			if(is_array($caData) && count($caData) > 0){
				$i=0;
				foreach($caData as $row){
					if($i==0){
						$prid=$row['Name'];
					}
					if($row['Name']==$prid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
						$results['prid']=$prid;
					}
					$i++;
					}
			}
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "salesproduct":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$ayxml);
			$caData = $xml->getAll();
			$prid=$_POST['pid'];
			if(is_array($caData) && count($caData) > 0){
				foreach($caData as $row){
					if($row['Name']==$prid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
					}
				}
			}
			$results['results']=$data;
			$results['tableid']='15.2.';
			$results['prid']=$prid;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "15.3.":
			$xml = new XmlData($caseStudyId,$bkxml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;

		case "16.1.":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$baxml);
			$caData = $xml->getAll();
			if(is_array($caData) && count($caData) > 0){
				$i=0;
				foreach($caData as $row){
					if($i==0){
						$curr=$row['TradeCurrency'];
					}
					if($row['TradeCurrency']==$curr){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
						$results['cid']=$row['TradeCurrency'];
					}
						$i++;
					}
			}
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "purchasecurrency":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$baxml);
			$caData = $xml->getAll();
			$cid=$_POST['pid'];
			if(is_array($caData) && count($caData) > 0){
				foreach($caData as $row){
					if($row['TradeCurrency']==$cid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
					}
				}
			}
			$results['results']=$data;
			$results['tableid']='16.1.';
			$results['cid']=$cid;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "16.2.":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$baxml);
			$caData = $xml->getAll();
			if(is_array($caData) && count($caData) > 0){
				$i=0;
				foreach($caData as $row){
					if($i==0){
						$prid=$row['Name'];
					}
					if($row['Name']==$prid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
						$results['prid']=$prid;
					}
					$i++;
					}
			}
			$results['allyears']=$AllYear;
			$results['results']=$data;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;

		case "purchaseproduct":
			$productTypes = Config::getData('producttype');
			$xml = new XmlData($caseStudyId,$baxml);
			$caData = $xml->getAll();
			$prid=$_POST['pid'];
			if(is_array($caData) && count($caData) > 0){
				foreach($caData as $row){
					if($row['Name']==$prid){
						$data[] = $xml->getByField($row['ClientName'],'ClientName');
					}
				}
			}
			$results['results']=$data;
			$results['tableid']='16.2.';
			$results['prid']=$prid;
			$results['producttypes']=$productTypes;
        	echo (json_encode($results));
		break;


		case "16.3.":
			$xml = new XmlData($caseStudyId,$cgxml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;	

		case "17.1.":
			$xml = new XmlData($caseStudyId,$cvxml);
			$data = $xml->getByField('1','sid');
			$results['allyears']=$AllYear;
			$results['results']=$data;
			echo (json_encode($results));
		break;	

		case "17.2.":
			$ckd = new XmlData($caseStudyId,$ckxml);
			$ckData = $ckd->getByField('1','sid');

			$cnd = new XmlData($caseStudyId,$cnxml);
			$cnData = $cnd->getByField('1','sid');

			$bgd = new XmlData($caseStudyId,$bgxml);
			$bgData = $bgd->getByField('1','sid');

			$cjd = new XmlData($caseStudyId,$cjxml);
			$cjData = $cjd->getByField('1','sid');

			$cld = new XmlData($caseStudyId,$clxml);
			$clData = $cld->getByField('1','sid');

			$results['allyears']=$AllYear;
			$results['ckData']=$ckData;
			$results['cnData']=$cnData;
			$results['bgData']=$bgData;
			$results['cjData']=$cjData;
			$results['clData']=$clData;
			echo (json_encode($results));
		break;

		case "17.3.":
			$cbd = new XmlData($caseStudyId,$cbxml);
			$cbData = $cbd->getByField('1','sid');//	
		
			$ccd = new XmlData($caseStudyId,$ccxml);
			$ccData = $ccd->getByField('1','sid');//	
			
			$cdd = new XmlData($caseStudyId,$cdxml);
			$cdData = $cdd->getByField('1','sid');
			
			$chd = new XmlData($caseStudyId,$chxml);
			$chData = $chd->getByField('1','sid');
			
			$cid = new XmlData($caseStudyId,$cixml);
			$ciData = $cid->getByField('1','sid');
			
			$cmd = new XmlData($caseStudyId,$cmxml);
			$cmData = $cmd->getByField('1','sid');
					
			$cnd = new XmlData($caseStudyId,$cnxml);
			$cnData = $cnd->getByField(1,'sid');
			
			$agd = new XmlData($caseStudyId,$agxml);
			$agData = $agd->getByField('1','sid');
			
			$bsd = new XmlData($caseStudyId,$bsxml);
			$bsData = $bsd->getByField('1','sid');

			$results['allyears']=$AllYear;
			$results['cbData']=$cbData;
			$results['ccData']=$ccData;
			$results['cdData']=$cdData;
			$results['chData']=$chData;
			$results['ciData']=$ciData;
			$results['cmData']=$cmData;
			$results['cnData']=$cnData;
			$results['agData']=$agData;
			$results['bsData']=$bsData;
			echo (json_encode($results));
		break;

		case "17.4.":
		
			$chd = new XmlData($caseStudyId,$chxml);
			$chData = $chd->getByField('1','sid');

			$cmd = new XmlData($caseStudyId,$cmxml);
			$cmData = $cmd->getByField('1','sid');
					
			$cnd = new XmlData($caseStudyId,$cnxml);
			$cnData = $cnd->getByField(1,'sid');
			
			$agd = new XmlData($caseStudyId,$agxml);
			$agData = $agd->getByField('1','sid');

			$results['allyears']=$AllYear;
			$results['chData']=$chData;
			$results['cmData']=$cmData;
			$results['cnData']=$cnData;
			$results['agData']=$agData;
			echo (json_encode($results));
		break;
	}

	?>