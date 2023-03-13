<?php
	require_once "../../config.php";
	require_once BASE_PATH."general.php";
	require_once CLASS_PATH."XmlData.php";
	require_once CLASS_PATH."SimpleXLSXGen.php";
	
	$amd = new Data($caseStudyId,$amxml);
	$amData = $amd->getByField(1,'SID');

	$agd = new XmlData($caseStudyId,$agxml);
	$agData = $agd->getByField('1','sid');//total investement

	$bdd = new XmlData($caseStudyId,$bdxml);
	$bdData = $bdd->getByField('1','sid');//
	
	$bed = new XmlData($caseStudyId,$bexml);
	$beData = $bed->getByField('1','sid');//
	
	$bgd = new XmlData($caseStudyId,$bgxml);
	$bgData = $bgd->getByField('1','sid');//
	
	$bkd = new XmlData($caseStudyId,$bkxml);
	$bkData = $bkd->getByField('1','sid');//
	
	$bzd = new XmlData($caseStudyId,$bzxml);
	$bzData = $bzd->getByField('1','sid');//

	$cdd = new XmlData($caseStudyId,$cdxml);
	$cdData = $cdd->getByField('1','sid');//cal_equity
	
	$ced = new XmlData($caseStudyId,$cexml);
	$ceData = $ced->getByField(1,'sid');//

	$cgd = new XmlData($caseStudyId,$cgxml);
	$cgData = $cgd->getByField('1','sid');//

	$chd = new XmlData($caseStudyId,$chxml);
	$chData = $chd->getByField(1,'sid');//cal_totalexport
	
	$cld = new XmlData($caseStudyId,$clxml);
	$clData = $cld->getByField('1','sid');//

	$ckd = new XmlData($caseStudyId,$ckxml);
	$ckData = $ckd->getByField('1','sid');//
	
	$cnd = new XmlData($caseStudyId,$cnxml);
	$cnData = $cnd->getByField(1,'sid');

	$cod = new XmlData($caseStudyId,$coxml);
	$coData = $cod->getByField('1','sid');//

	$crd = new XmlData($caseStudyId,$crxml);
	$crData = $crd->getByField('1','sid');//
	
	$cvd = new XmlData($caseStudyId,$cvxml);
	$cvData = $cvd->getByField(1,'sid');

	$csd = new XmlData($caseStudyId,$csxml);
	$csData = $csd->getByField(1,'sid');

	$ctd = new XmlData($caseStudyId,$ctxml);
	$ctData = $ctd->getByField('1','sid');//


	//Manage files
	$table= array();
	$row['Item']=$caseStudyId;
	$row['style']=8;
	array_push($table,$row);

	$row=[];
	$row['Item']="";
	array_push($table,$row);

			$row=[];
			$row['Item']="1.1. Operating account in local currency [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;

			array_push($table,$row);
			$row=array();
			$row['item']='Income';
			$row['style']=10;
			array_push($table,$row);

			$row=array();
			$row['item']='Fixed income + other income';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cvData['TOI_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total sales revenues';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bkData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Interest earned';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['SDIL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total income';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bkData['LC_'.$n]+$cnData['SDIL_'.$n]+$cvData['TOI_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Expenditure';
			$row['style']=10;
			array_push($table,$row);

			$row=array();
			$row['item']='General expenses';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bzData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Expenditure on purchases';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cgData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Fuel costs';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($beData['LC_'.$n]);
			}
			array_push($table,$row);
			
			$row=array();
			$row['item']='O&M costs';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bdData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Interest paid';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['TIL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Foreign exchange loss';

			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($clData['PFLY_'.$n]);
			}
			array_push($table,$row);
			$series[]='Foreign exchange loss';

			$row=array();
			$row['item']='Decommissioning expenses';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ceData['TDCL_'.$n]+$ceData['ADFL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Depreciation';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bgData['T_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Royalty';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ckData['RLC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Income tax';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['ITL_'.$n]);
			}
			array_push($table,$row);

			$income=array();
			$total=array();
			$loss=array();
			for($n = $startYear;$n <= $endYear; $n++){
				$income[$n] = formatnumber(
					$bkData['LC_'.$n] + 
					$cnData['SDIL_'.$n]+ 
					$cvData['TOI_'.$n]);

				$total[$n]=formatnumber(
					$cgData['LC_'.$n] + 
					$bdData['LC_'.$n] + 
					$beData['LC_'.$n] + 
					$bzData['LC_'.$n] + 
					$cnData['TIL_'.$n] + 
					$clData['PFLY_'.$n] + 
					$bgData['T_'.$n] + 
					$ckData['RLC_'.$n] + 
					$cnData['ITL_'.$n] + 
					$ceData['ADFL_'.$n] + 
					$ceData['TDCL_'.$n]);

					$loss[$n]=$income[$n]-$total[$n];
			}

			$row=array();
			$row['item']='Total expenses';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($total[$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Profit/loss';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($loss[$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Dividends';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['DivL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Retained earnings';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['REL_'.$n]);
			}
			array_push($table,$row);		

			//1.2. Cash inflows and outflows in local currency
			$row=[];
			$row['Item']="";
			array_push($table,$row);
		
			$row=[];
			$row['Item']="1.2. Cash inflows and outflows in local currency [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;
			array_push($table,$row);

			$series= array();
			$loanDrawdown=array();
			$aVal=array();
			for($n = $startYear;$n <= $endYear; $n++){
				$gVal[$n] = max(0,$cnData['SLL_'.$n]);
				$qrVal[$n] = $cnData['ITL_'.$n] + $ckData['RLC_'.$n];
				$uVal[$n] = max(0,$cnData['SDBL_'.$n]-$cnData['SDBL_'.($n-1)]);
				$loanDrawdown[$n] = formatnumber($chData['LLC_'.$n] + $cnData['TCLL_'.$n]);
				$aVal[$n] = formatnumber($cvData['FR_'.$n] + $bkData['LC_'.$n] + $cvData['OI_'.$n]);
				$tVal[$n] = $agData['GIL_'.$n]+$bdData['OMTD_'.$n]+$beData['LC_'.$n]+$cgData['LC_'.$n]+$bzData['LC_'.$n]+
							$cnData['TIL_'.$n]+$cnData['TRL_'.$n]+$cnData['SLRL_'.$n]+$cnData['TERL_'.$n]+$qrVal[$n]+$cnData['DivL_'.$n]+$uVal[$n];

				$totinflow[$n] = $aVal[$n] + $cnData['SDIL_'.$n] + $cdData['N_'.$n] + $cnData['TBL_'.$n] + $loanDrawdown[$n] + $gVal[$n];
				$flowstd[$n] = max(0,$cnData['SDBL_'.($n-1)]-$cnData['SDBL_'.$n]);
				$totcash[$n] = $totinflow[$n] + $flowstd[$n];
			}

			$row=array();
			$row['item']='Cash available in short term deposits (at end of previous year)';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['SDBL_'.($n-1)]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Inflows';
			$row['style']=10;
			array_push($table,$row);

			$row=array();
			$row['item']='Revenues';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cvData['FR_'.$n] + $bkData['LC_'.$n] + $cvData['OI_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Fixed revenues';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cvData['FR_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Sales';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bkData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Others';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cvData['OI_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Interest earned';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['SDIL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='New equity';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cdData['N_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Bonds issue';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['TBL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Loans drawdowns';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$loanDrawdown;
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Stand-by facility';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$gval[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total inflows';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$totinflow[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Flow from short term deposits';

			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$flowstd[$n];
			}
			array_push($table,$row);
			$series[]='Flow from short term deposits';

			$row=array();
			$row['item']='Total cash available';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$totcash[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Outflows';
			$row['style']=10;
			array_push($table,$row);

			$row=array();
			$row['item']='Investment';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($agData['GIL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='O&M + decommissioning cost';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bdData['OMTD_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Fuel expenses';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($beData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Expenditure on purchases';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cgData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='General expenses';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($bzData['LC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Interest paid';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['TIL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Repayments: loans and bonds';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['TRL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Repayments: stand-by facility';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['SLRL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Equity repayment';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['TERL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Taxes & royalties';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$qrVal[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Dividend';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['DivL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Flow to short term deposits';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$uVal[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total outflows';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=$tVal[$n];
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Cash available (VAT)';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($totcash[$n] - $tVal[$n]);
			}
			array_push($table,$row);

			//1.3. Balance sheet in local currency
			$row=[];
			$row['Item']="";
			array_push($table,$row);
		
			$row=[];
			$row['Item']="1.3. Balance sheet in local currency [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;
			array_push($table,$row);

			$row=array();
			$row['item']='Assets';
			$row['style']=10;
			array_push($table,$row);

			$row=array();
			$row['item']='Gross fixed assets';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['GFA_'.$n]);
			}
			array_push($table,$row);
			$series[]='Gross fixed assets';

			$row=array();
			$row['item']='Less: accumulated depreciation';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['CD_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Less: accumulated consumer contribution';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['CC_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Net Fixed Assets';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NFA_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Work In Progress';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($agData['WPL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Receivables';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['R_'.$n]);
			}
			array_push($table,$row);
			
			$row=array();
			$row['item']='Short term deposits';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['SDBL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NFA_'.$n] + $agData['WPL_'.$n] + $coData['R_'.$n] + $cnData['SDBL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Equity and liabilities';
			$row['style']=10;
			array_push($table,$row);
			
			$row=array();
			$row['item']='Equity';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NEOL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Retained Earnings';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($cnData['AREL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Bonds Outstanding';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NBOL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Net Loans Outstanding';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NLOL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Consumer Deposits + Decommissioning Reserve';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['CDDFL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Current Maturity';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['CML_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Total';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NFA_'.$n] + $agData['WPL_'.$n] + $coData['R_'.$n] + $cnData['SDBL_'.$n]);
			}
			array_push($table,$row);

			//1.4. Shareholders' return in local currency 
			$row=[];
			$row['Item']="";
			array_push($table,$row);
			
			$row=[];
			$row['Item']="1.4. Shareholders' return in local currency [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;
			array_push($table,$row);

			$row=array();
				$row['item']='Initial equity';
				$row[$startYear-1]=formatnumber($cdData['IE'])*-1;
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber(0);
				}
				array_push($table,$row);
				
				$row=array();
				$row['item']='Eq.Increase';
				$row[$startYear-1]=formatnumber(0)*(-1);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($cdData['N_'.$n]);
				}
				array_push($table,$row);

				$row=array();
				$row['item']='Eq.Repayments';
				$row[$startYear-1]=formatnumber(0);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($cdData['E_'.$n]);
				}
				array_push($table,$row);

				$row=array();
				$row['item']='Dividend';
				$row[$startYear-1]=formatnumber(0);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($cnData['DivL_'.$n]);
				}
				array_push($table,$row);

				$row=array();
				$row['item']='Final disposal';
				$row[$startYear-1]=formatnumber(0);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($csData['FD_'.$n]);
				}
				array_push($table,$row);
			
				$row=array();
				$row['item']='Total flow';
				$row['style']=10;
				$row[$startYear-1]=formatnumber($csData['TF_'.($startYear-1)]);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($csData['TF_'.$n]);
				}
				array_push($table,$row);

				$row=array();
				$row['item']='Return on equity (%)';
				$row[$startYear-1]=formatnumber(0);
				for($n = $startYear;$n <= $endYear; $n++){
					$row[$n]=formatnumber($csData['RE_'.$n]);
				}
				array_push($table,$row);

			//1.5. Financial Ratios
			$row=[];
			$row['Item']="";
			array_push($table,$row);
			
			$row=[];
			$row['Item']="1.5. Financial Ratios [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;
			array_push($table,$row);

			$row=array();
			$row1=array();
			$row['item']='Working capital';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R1_'.$n]);
				$row1[$n]=($ctData['R1S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Leverage';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R2_'.$n]);
				$row1[$n]=($ctData['R2S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Equipment renewal';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R3_'.$n]);
				$row1[$n]=($ctData['R3S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Gross profit rate';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R4_'.$n]);
				$row1[$n]=($ctData['R4S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Debt repayment time';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R5_'.$n]);
				$row1[$n]=($ctData['R5S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Exchange risk';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R6_'.$n]);
				$row1[$n]=($ctData['R6S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Breakeven point';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R7_'.$n]);
				$row1[$n]=($ctData['R7S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Interest charge weight';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R8_'.$n]);
				$row1[$n]=($ctData['R8S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row['item']='Global index';
			$row['style']=10;
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['GI_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row1=array();
			$row['item']='Self financing ratio';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R9_'.$n]);
				$row1[$n]=($ctData['R9S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Debt equity ratio';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R10_'.$n]);
				$row1[$n]=($ctData['R10S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='Debt service coverage';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R11_'.$n]);
				$row1[$n]=($ctData['R11S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			$row=array();
			$row1=array();
			$row['item']='ROR on rev assets';
			$row1['item']='';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($ctData['R12_'.$n]);
				$row1[$n]=($ctData['R12S_'.$n]);
			}
			array_push($table,$row);
			array_push($table,$row1);

			//1.6. Project finance analysis in local currency
			$row=[];
			$row['Item']="";
			array_push($table,$row);
			
			$row=[];
			$row['Item']="1.6. Project finance analysis in local currency [Million]";
			for($y = 0; $y < count($AllYear); $y++){ 
				$AY=$AllYear[$y];
				$row[$AY]=$AY;
			}
			$row['style']=8;
			array_push($table,$row);

			$row=array();
			$row['item']='Loans and bonds outstanding';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($coData['NBOL_'.$n] + $coData['NLOL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Cash available during loan term';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['LT_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='PV of cash available during loan term';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['NT_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Maximum project finance during loan term';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['MFLO_'.$n]);
			}
			array_push($table,$row);
			
			$row=array();
			$row['item']='Cash available during project life';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['LL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='PV of cash available during project life';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['NL_'.$n]);
			}
			array_push($table,$row);

			$row=array();
			$row['item']='Maximum project finance during project life';
			for($n = $startYear;$n <= $endYear; $n++){
				$row[$n]=formatnumber($crData['MFLi_'.$n]);
			}
			array_push($table,$row);

			$xlsx = SimpleXLSXGen::fromArray($table);
			$xlsx->saveAs(USER_CASE_PATH.$caseStudyId."/result/Results.xlsx");

function formatnumber($number){
	if(is_nan($number) || $number==0){
		$num=0;
	}else
	{
		$num=number_format($number,15,'.','');
	}
		return $num;
}	
?>