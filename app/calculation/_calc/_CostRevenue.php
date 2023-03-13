<?php

class CostRevenue extends FinplanService {

  public $finplan;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
  }

  /**
   * Calculate Operation and Maintenance Costs
   **/
  public function updateOperationMaintenanceCosts()
  {
    $om_Data = Array();//initialise
  	$om_cpData = $this->finplan->getPlantData();// get Data for all plants in this study
  	$om_infData = $this->finplan->getInflationData();// get inflation index Data
  	if (is_array($om_cpData) && count($om_cpData) > 0) {// check if plant exist
      foreach($om_cpData as $om_row) {
        $startyr = $om_row['Status'] != 'Existing' ? $om_row['FOyear'] : $this->finplan->startYear;
        $plntyr = $startyr + $om_row['Plantlife'] - 1;
        $endyr = $this->finplan->endYear < $plntyr ? $this->finplan->endYear : $plntyr;
        //echo "startyr = ".$startyr.", end year = ".$endyr."<br>";
        $om_idData = $this->finplan->aod->getByField($om_row['id'],'pid');// get OM cost by plant id
        $om_Data = Array();//intialise & clear the Data set
        $om_Data['pid'] = $om_row['id'];// set pid = plant id
        for($om_c = 0; $om_c < count($this->finplan->allChunks); $om_c++){// for each currency
          $om_CX = $this->finplan->allChunks[$om_c];
          for($om_i=$startyr;$om_i <= $endyr; $om_i++){// for each study year
            $om_CY = $om_CX.'_'.$om_i;//currencyid_year
            if($om_idData[$om_CY] ==''){ //if Amount is empty for this year then take value from previous year
              $om_omcost[$om_i] = $om_omcost[$om_i-1];
            }else{
              $om_omcost[$om_i] = $om_idData[$om_CY];// Amount  for this year given by user
            }
            $om_Eom = $om_omcost[$om_i] * $om_infData[$om_CY];//OM cost * inflation index
            $om_ein = 'E_'.$om_CY;
            $om_Data[$om_CY]= $om_omcost[$om_i];//OM cost
            $om_Data[$om_ein]= $om_Eom;	// Esclated OM cost
          }
        }
        $this->finplan->bbd->add($om_Data);
      }
    }
  }

  /**
   * Calculate Fuel Costs
   **/
  public function updateFuelCosts()
  {
    $fc_Data = Array();//initialise
  	$fc_cpData = $this->finplan->getPlantData();// get Data for all plants
  	$fc_infData = $this->finplan->getInflationData();//get inflation index Data

  	if(is_array($fc_cpData) && count($fc_cpData) > 0){// check if plants exist
  	    foreach($fc_cpData as $fc_row){
          $startyr = $fc_row['Status'] != 'Existing' ? $fc_row['FOyear'] : $this->finplan->startYear;
          $plntyr = $startyr + $fc_row['Plantlife'] - 1;
          $endyr = $this->finplan->endYear < $plntyr ? $this->finplan->endYear : $plntyr;

    			$fc_imData = $this->finplan->amd->getByField($fc_row['id'],'pid');// get fuel Data by plant id
    			$fc_Data = Array();//intialize & clear Data set
    			$fc_Data['pid'] = $fc_row['id'];//set pid = plant id
    			for($fc_c = 0; $fc_c < count($this->finplan->allChunks); $fc_c++){// for each currency
    				$fc_CX = $this->finplan->allChunks[$fc_c];
    				for($fc_i=$startyr;$fc_i <= $endyr; $fc_i++){// for each year
    					$fc_CY = $fc_CX.'_'.$fc_i;
    					if($fc_imData[$fc_CY] ==''){ //if Amount is empty for this year then take value from previous year
    						$fc_fuelcost[$fc_i] = $fc_fuelcost[$fc_i-1];
    					}else{
    						$fc_fuelcost[$fc_i] = $fc_imData[$fc_CY];// Amount  for this year given by user
    					}
    					$fc_Efc = $fc_fuelcost[$fc_i] * $fc_infData[$fc_CY];// Escl FuelCost(i)= fuel cost(i) *Inflation index (i)
    					$fc_ein = 'E_'.$fc_CY;
    					$fc_Data[$fc_CY]= $fc_fuelcost[$fc_i];	//FuelCost(i)= fuell cost(i)
    					$fc_Data[$fc_ein]= $fc_Efc;
    				}
    			}
    			$this->finplan->bcd->add($fc_Data);
  		}
  	}
  }

  /**
   * Calculate Total Fuel Costs
   **/
  public function updateTotalFuelCosts()
  {
  	$tfc_cpData = $this->finplan->getPlantData();// get all plant Data
  	$tfc_caData = $this->finplan->bcd->getall();// get calculated fuel costs
  	$tfc_excData = $this->finplan->getExchangeData();// get exch Data
    $tfc_Data = Array();

  	if(is_array($tfc_caData) && count($tfc_caData) > 0){// check if  Calculated fuel costs exist
  		$tfc_Data['sid'] = 1;// set SID for storing Data
  		for($tfc_i=$this->finplan->startYear;$tfc_i <= $this->finplan->endYear; $tfc_i++){// for each year
  			for($tfc_c = 0; $tfc_c < count($this->finplan->allChunks); $tfc_c++){//for each currency
  				$tfc_CX = $this->finplan->allChunks[$tfc_c];
  				$tfc_ETC = 'E_'.$tfc_CX.'_'.$tfc_i;
  				$tfc_TC = $tfc_CX.'_'.$tfc_i;
  				$tfc_CY = $tfc_CX.'_'.$tfc_i;
  				foreach($tfc_caData as $tfc_row){
  					$tfc_Data[$tfc_ETC] = $tfc_Data[$tfc_ETC]+$tfc_row[$tfc_ETC];// Tot_Fuel_cost(c,j) Current
  					$tfc_Data[$tfc_TC] = $tfc_Data[$tfc_TC]+$tfc_row[$tfc_TC];// Tot_Fuel_cost(c,j)  without inflation
  					if($tfc_CX == $this->finplan->baseCurrency){
  						$tfc_share[$tfc_i] = $tfc_row[$tfc_ETC];//local currency
  					}else{
  						$tfc_share[$tfc_i] = $tfc_row[$tfc_ETC] * $tfc_excData[$tfc_CY];//foreign currency
  					}
  					$tfc_Data['LC_'.$tfc_i] = $tfc_Data['LC_'.$tfc_i] + $tfc_share[$tfc_i];// Tot_Fuel_Cost_LC
  				}
  			}
  		}

  		$this->finplan->bed->add($tfc_Data);
  	}

    return $tfc_Data;
  }

  /**
   * Calculate General Expense Costs
   **/
  public function updateGeneralExpenseCosts()
  {
    $ge_cpData = $this->finplan->getPlantData();// get Data for all plants in this study
    $ge_infData = $this->finplan->getInflationData();// get inflation index Data
    $ge_Data = Array();

    if (is_array($ge_cpData) && count($ge_cpData) > 0) {// check if plant exist
      foreach($ge_cpData as $ge_row) {
        $startyr = $ge_row['Status'] != 'Existing' ? $ge_row['FOyear'] : $this->finplan->startYear;
        $plntyr = $startyr + $ge_row['Plantlife'] - 1;
        $endyr = $this->finplan->endYear < $plntyr ? $this->finplan->endYear : $plntyr;

        $ge_idData = $this->finplan->bud->getByField($ge_row['id'],'pid');// get generalexpense by plant id
        $ge_Data = Array();//intialise & clear the Data set
        $ge_Data['pid'] = $ge_row['id'];// set pid = plant id

        for($ge_c = 0; $ge_c < count($this->finplan->allChunks); $ge_c++) {// for each currency
          $ge_CX = $this->finplan->allChunks[$ge_c];
          for ($ge_i=$startyr;$ge_i <= $endyr; $ge_i++) {// for each study year
            $ge_CY = $ge_CX.'_'.$ge_i;//currencyid_year
            if ($ge_idData[$ge_CY] =='') { //if Amount is empty for this year then take value from previous year
              $ge_genexpense[$ge_i] = $ge_genexpense[$ge_i-1];
            } else {
              $ge_genexpense[$ge_i] = $ge_idData[$ge_CY];// Amount  for this year given by user
            }
            $ge_Ege = $ge_genexpense[$ge_i] * $ge_infData[$ge_CY];//generalexpense * inflation index
            $ge_ein = 'E_'.$ge_CY;
            $ge_Data[$ge_CY]= $ge_genexpense[$ge_i];//generalexpense
            $ge_Data[$ge_ein]= $ge_Ege;	// Esclated generalexpense
          }
        }
        $this->finplan->byd->add($ge_Data);
      }
    }
  }
  //Note:: find example where this is not null

  /**
   * Calculate Total General Expense Costs
   **/
  public function updateTotalGeneralExpenseCosts()
  {
  	$tge_cpData = $this->finplan->getPlantData();// get all Data for plants
  	$tge_caData = $this->finplan->byd->getall();//get all calculated OM Data
  	$tge_excData = $this->finplan->getExchangeData();// get exch Data
    $tge_Data = Array();

  	if(is_array($tge_caData) && count($tge_caData) > 0){// check if calculated om Data exist
  		$tge_Data['sid'] = 1;//set SID for storing Data
  		for($tge_i=$this->finplan->startYear;$tge_i <= $this->finplan->endYear; $tge_i++){// for each study year

  			for($tge_c = 0; $tge_c < count($this->finplan->allChunks); $tge_c++){// for each currency
  				$tge_CX = $this->finplan->allChunks[$tge_c];
  				$tge_ETC = 'E_'.$tge_CX.'_'.$tge_i;
  				$tge_TC = $tge_CX.'_'.$tge_i;

  				foreach($tge_caData as $tge_row){
  					$tge_Data[$tge_ETC] = $tge_Data[$tge_ETC]+$tge_row[$tge_ETC];// Tot_Gen_Exp(C,j). Total generalexpense
  				    //tge_Data[$tge_TC] = $tge_Data[$tge_TC]+$tge_row[$tge_TC];// Total generalexpense without inflation
  					if($tge_CX == $this->finplan->baseCurrency){
  						$tge_share[$tge_i] = $tge_row[$tge_ETC];//local currency
  					}else{
  						$tge_share[$tge_i] = $tge_row[$tge_ETC]* $tge_excData[$tge_TC];//foreign currency

  					}
  					$tge_Data['LC_'.$tge_i] = $tge_Data['LC_'.$tge_i] + $tge_share[$tge_i];// Tot_Gen_Exp_LC
  				}
  			}

  		}
  		$this->finplan->bzd->add($tge_Data);
  	}
    return $tge_Data;
  }
  //Note:: find a case study where this is not null

  /**
   * Calculate Fixed Revenues
   **/
  public function updateFixedRevenues()
  {
  	$fri_cuData = $this->finplan->cud->getByfield(1,'sid');
    $fri_Data = Array();//initialise

  	for ($fri_i=$this->finplan->startYear;$fri_i <= $this->finplan->endYear; $fri_i++) {// for each study year
  		$fri_ii = $fri_i-1;
  		$fri_Fixed_Reveneus_initial = $fri_cuData['Fixed_Reveneus_initial'];
  		$fri_Grate_FRevenues = $fri_cuData['Grate_FRevenues'];
  		$fri_Other_Income_initial = $fri_cuData['Other_Income_initial'];
  		$fri_Grate_OIncome = $fri_cuData['Grate_OIncome'];
  		if($fri_i==$this->finplan->startYear){
        //Fixed_Revenues (j) = Fixed_Reveneus_initial � (1+ Grate_FRevenues/100)
  			$fri_Fixed_Reveneus[$fri_i] = $fri_Fixed_Reveneus_initial * (1+$fri_Grate_FRevenues/100);
        //Other_Income (j) = Other_Income_initial � (1+ Grate_OIncome /100)
  			$fri_Other_Income[$fri_i] = $fri_Other_Income_initial * (1+$fri_Grate_OIncome/100);
  		}else{
        //Fixed_Revenues (j) = Fixed_Revenues (j-1) � (1+ Grate_FRevenues/100)
  			$fri_Fixed_Reveneus[$fri_i] = $fri_Fixed_Reveneus[$fri_ii] * (1+$fri_Grate_FRevenues/100);
        //Other_Income (j) = Other_Income (j-1)� (1+ Grate_OIncome /100)
  			$fri_Other_Income[$fri_i] = $fri_Other_Income[$fri_ii] * (1+$fri_Grate_OIncome/100);
  		}
  		$fri_data['FR_'.$fri_i]=$fri_Fixed_Reveneus[$fri_i];
  		$fri_data['OI_'.$fri_i] = $fri_Other_Income[$fri_i];
  		//Tot_Other_Income_LC(j)= Fixed_Revenues (j)+ Other_Income (j)
  		$fri_Tot_Other_Income_LC[$fri_i] = $fri_Fixed_Reveneus[$fri_i] + $fri_Other_Income[$fri_i];
  		$fri_data['TOI_'.$fri_i] = $fri_Tot_Other_Income_LC[$fri_i];
  	}

    $fri_data['sid'] = 1;//setting sid=1
    $this->finplan->cvd->add($fri_data);
  }
  //Note:: find an example where this is not null

  /**
   * Calculate Global Investments
   **/
  public function updateGlobalInvestments()
  {
  	$gi_cpData = $this->finplan->getPlantData(); //get plant list
  	$gi_excData = $this->finplan->getExchangeData();//get exchage rate list
    $gi_Data = Array();

  	if (is_array($gi_cpData) && count($gi_cpData) > 0) {// check if plant Data exist
      foreach ($gi_cpData as $gi_row) {// for each plant
        $gi_invData = $this->finplan->aed->getByField($gi_row['id'],'pid');//get investment Data by plant id
  			$gi_Data = Array();// intialize & clear Data if any
  			$gi_Data['pid'] = $gi_row['id'];// set pid equal to plant id
  			$gi_GFA[$this->finplan->startYear-1]=0;// setting GFA for startyear-1 = zero
  			$gi_TGI = 0;//setting initial Tot Global Investment to zero
  			for($gi_i=$this->finplan->startYear;$gi_i <= $this->finplan->endYear; $gi_i++){// for each year
  				$gi_globinv = 'GI_'.$gi_i;
  				$gi_fxdasset = 'FA_'.$gi_i;
  				$gi_Data[$gi_globinv] = 0;//set start value to zero
  				for($gi_c = 0; $gi_c < count($this->finplan->allChunks); $gi_c++){
  					$gi_CX = $this->finplan->allChunks[$gi_c];
  					$gi_escinval = 'EI_'.$gi_CX.'_'.$gi_i;
  					$gi_escinv = $gi_invData[$gi_escinval];// get esc investment value for this year
  					if($gi_CX == $this->finplan->baseCurrency){ // if currency is base
  						$gi_Data[$gi_globinv] = $gi_Data[$gi_globinv]+$gi_escinv;
  					}else{ //if currency is foreign then multiply with exchange rate
  						$gi_excval = $gi_CX.'_'.$gi_i;
  						$gi_exch = $gi_excData[$gi_excval];//get exchage rate for this currency for this year
  						$gi_Data[$gi_globinv] = $gi_Data[$gi_globinv]+($gi_escinv * $gi_exch);// foreign currency * exchange rate
  					}
  				}
  				$gi_GFA[$gi_i] = $gi_GFA[$gi_i-1] + $gi_Data[$gi_globinv];// FA= FA(i-1)+GlobInvestment
  				$gi_Data[$gi_fxdasset] = $gi_GFA[$gi_i];//fixed asset value parsed to Data
  				$gi_TGI = $gi_TGI + $gi_Data[$gi_globinv];//Total Global Investment
  			}
  			$gi_Data['TGI']=$gi_TGI;
  			$this->finplan->bfd->add($gi_Data);
  		}
  	}
  }

  /**
   * Calculate Depreciation
   **/
  public function updateDepreciation($fixedAssets)
  {
  	$dp_cpData = $this->finplan->getPlantData(); //get plant list
  	$dp_excData = $this->finplan->getExchangeData();//get exchage rate list
    $dp_Data2 = Array();

  	if (is_array($dp_cpData) && count($dp_cpData) > 0) {
  	    foreach($dp_cpData as $dp_row) {
  			$dp_invData = $this->finplan->aed->getByField($dp_row['id'],'pid');//get global investment by plant id
  			$dp_deprData = $this->finplan->ald->getByField($dp_row['id'],'pid');//get depreciation Data by plant id
  			$dp_Data = Array();
  			$dp_Data['pid'] = $dp_row['id'];
  			$dp_GFA[$this->finplan->startYear-1]=0;// setting GFA for startyear-1 = zero
  			$dp_pworth = $dp_invData['PW_'.$dp_row['id']];
  			if ($dp_deprData['Type']=='L') {// If option is linear
  				$dp_noyrs = $dp_deprData['LinearYears'];
  				$dp_instalment = $dp_pworth/$dp_noyrs;// pworth/no of years
  				$dp_strtyr = $dp_row['FOyear'];
  				$dp_endyr = $dp_row['FOyear'] + $dp_noyrs -1 ;// FOYEAR+LinearYears-1
  				for ($dp_i=$dp_strtyr;$dp_i <= $dp_endyr; $dp_i++) {
  					$dp_depreciation[$dp_i] = $dp_instalment;
  					$dp_Data['D_'.$dp_i] = $dp_depreciation[$dp_i];
  				}
  			} elseif ($dp_deprData['Type']=='DB') {// If option is Declining balance
  				$dp_deprate = $dp_deprData['DepreciationRate'];
  				$dp_strtyr = $dp_row['FOyear'];
  				$dp_netinvest[$dp_strtyr-1] = $dp_pworth;
  				for ($dp_i=$dp_strtyr;$dp_i <= $this->finplan->endYear; $dp_i++) {
  					$dp_depreciation[$dp_i] = $dp_netinvest[$dp_i-1] * $dp_deprate/100;
  					$dp_netinvest[$dp_i] = $dp_netinvest[$dp_i-1] - $dp_depreciation[$dp_i];
  					$dp_Data['D_'.$dp_i] = $dp_depreciation[$dp_i];
  				}
  			} elseif($dp_deprData['Type']=='SY') { //If option is Sum of the years digits
  				$dp_sumyr = $dp_deprData['SumYears'];
  				$dp_strtyr = $dp_row['FOyear'];
  				$dp_endyr = $dp_row['FOyear'] + $dp_sumyr -1;
  				$dp_t = 1;
  				for ($dp_i=$dp_strtyr;$dp_i <= $dp_endyr; $dp_i++) {
  					$dp_sumvalue[$dp_t] = (2 * ($dp_sumyr - $dp_t + 1))/($dp_sumyr * ($dp_sumyr +1));
  					$dp_depreciation[$dp_i] = $dp_sumvalue[$dp_t] * $dp_pworth ;
  					$dp_Data['D_'.$dp_i] = $dp_depreciation[$dp_i];
  					$dp_t ++;
  				}
  			} elseif ($dp_deprData['Type']=='DS') { //If option is Declining switching to linear
  					$dp_declyrs = $dp_deprData['DecliningYears']; //get value for declining years
  					$dp_declrate = $dp_deprData['DecliningRate'];// get value for declining rate
  					$dp_strtyr = $dp_row['FOyear'];//define first yr for depreciation
  					$dp_endyr = $dp_strtyr + $dp_declyrs -1;
  					$dp_netinvest2[$dp_strtyr-1] = $dp_pworth;
  				for ($dp_i = $dp_strtyr;$dp_i <= $dp_endyr; $dp_i++) {
  					$dp_userate = 1/($dp_strtyr + $dp_declyrs - $dp_i);
  					$dp_rate[$dp_i]	= max($dp_declrate,$dp_userate*100);
  					$dp_depreciation[$dp_i] = $dp_netinvest2[$dp_i-1] * ($dp_rate[$dp_i]/100);
  					$dp_netinvest2[$dp_i] = $dp_netinvest2[$dp_i-1] - $dp_depreciation[$dp_i];
  					$dp_Data['D_'.$dp_i] = $dp_depreciation[$dp_i];
  				}
  			}
  			for ($dp_j=$this->finplan->startYear;$dp_j <= $this->finplan->endYear; $dp_j++) {
  				$dp_Data2['TN_'.$dp_j] = $dp_Data2['TN_'.$dp_j] + $dp_Data['D_'.$dp_j];//Tot_Dep_LC(j) ~~Tot_Dep_Newplant_LC(j)
  			}
  			$this->finplan->bgd->add($dp_Data);
  		}
  	}
    $dp_Data2['sid'] = 1;//setting sid=1 to find Tot_Dep_LC(j) from xml when required
    for ($dp_k=$this->finplan->startYear;$dp_k <= $this->finplan->endYear; $dp_k++) {
      $dp_Data2['T_'.$dp_k] = $fixedAssets['old'][$dp_k] + $fixedAssets['committed'][$dp_k] + $dp_Data2['TN_'.$dp_k];
    }
    $this->finplan->bgd->add($dp_Data2);
    return $dp_Data2;
  }

  /**
   * Calculate Decommissioning
   **/
  public function updateDecommissioning()
  {
  	$de_cpData = $this->finplan->getPlantData();// get Data for all plants in this study
  	$de_infData = $this->finplan->getInflationData();// get inflation index Data
  	$de_excData = $this->finplan->getExchangeData();// get exch Data
    $de_Data = Array();//initialise

  	if (is_array($de_cpData) && count($de_cpData) > 0) {// check if plant exist
      $de_Data = Array();//intialise & clear the Data set
      $de_Data['sid'] = 1;// set pid = plant id
      foreach ($de_cpData as $de_row) {
        $de_idData = $this->finplan->akd->getByField($de_row['id'],'pid');// get generalexpense by plant id
  			$de_pid = $de_row['id'];
  			$de_fund = $de_idData['fund'];
  			$de_syear = $de_idData['startyear'];
  			$de_fyear = $de_idData['deccyear'];
  			$de_dyear = $de_fyear - $de_syear+1;

  			if ($de_fund == 'F') {


          for ($de_c = 0; $de_c < count($this->finplan->allChunks); $de_c++) {// for each currency [0]=>USD [1]=>JOD
  					$de_CX = $this->finplan->allChunks[$de_c];
  					$de_CY = $de_CX.'_'.$de_syear;
  					$de_PC = $de_pid.'_'.$de_CX;
  					$de_famount[$de_pid.'_'.$de_CX] = $de_idData[$de_CX.'_famount'];
  					$de_decost[$de_pid.'_'.$de_CY] = $de_famount[$de_PC] * $de_infData[$de_CY];
  					$de_delcost[$de_pid.'_'.$de_fyear] = $de_decost[$de_pid.'_'.$de_CY] * $de_excData[$de_CY]; //ne koristi se u ovoj rutinii
  					$de_Data['DC_'.$de_pid.'_'.$de_CY] = $de_decost[$de_pid.'_'.$de_CY];//Decom_Costs
  					$de_Data['TDC_'.$de_CY] = $de_Data['TDC_'.$de_CY] + $de_Data['DC_'.$de_pid.'_'.$de_CY];//Tot_Decom_Costs
  					$de_fundyear = $de_syear;

  					for ($de_d = 1; $de_d <= $de_dyear; $de_d++) {// for Decomm years
  						$de_factor = $de_d/($de_dyear);
  						$de_Data['DF_'.$de_pid.'_'.$de_CX.'_'.$de_fundyear] = $de_famount[$de_pid.'_'.$de_CX] * $de_infData[$de_CX.'_'.$de_fundyear] * $de_factor;// decomm_funds(p,c,j)
  						$de_Data['TDF_'.$de_CX.'_'.$de_fundyear] =
              $de_Data['TDF_'.$de_CX.'_'.$de_fundyear] + ($de_famount[$de_pid.'_'.$de_CX] * $de_infData[$de_CX.'_'.$de_fundyear] * $de_factor);//Tot_Decom_Funds
              $de_fundyear2 = $de_fundyear-1;
  						$de_Data['ADF_'.$de_CX.'_'.$de_fundyear] = $de_Data['TDF_'.$de_CX.'_'.$de_fundyear]*$de_excData[$de_CX.'_'.$de_fundyear];
              $de_Data['ADF_'.$de_CX.'_'.$de_fundyear] -= $de_Data['TDF_'.$de_CX.'_'.$de_fundyear2]*$de_excData[$de_CX.'_'.$de_fundyear2] ;
              //Ann_Decom_Funds mora se uraditi konverzija prije nego se uradi oduzimanje od prethodne god da bi se koristio exchange index od prosle god
              $de_Data['DF_'.$de_pid.'_'.$de_fundyear] = $de_Data['DF_'.$de_pid.'_'.$de_fundyear] + ($de_Data['DF_'.$de_pid.'_'.$de_CX.'_'.$de_fundyear]* $de_excData[$de_CX.'_'.$de_fundyear]);
  						$de_adfl[$de_fundyear] = $de_Data['ADF_'.$de_CX.'_'.$de_fundyear]; //* $de_excData[$de_CX.'_'.$de_fundyear];
  						$de_Data['ADFL_'.$de_fundyear] = $de_Data['ADFL_'.$de_fundyear] + $de_adfl[$de_fundyear];//Ann_Decom_Funds_LC
  						$de_fundyear ++;

  					}
  				}
  			} else {	// if trust for decommissioning costs
  				for ($de_e = $de_syear; $de_e <= $de_fyear; $de_e++) {// for Decomm years
  					for($de_c = 0; $de_c < count($this->finplan->allChunks); $de_c++) {
  						$de_CX = $this->finplan->allChunks[$de_c];
  						$de_CY = $de_CX.'_'.$de_e;
  						$de_PC = $de_pid.'_'.$de_CX;
  						$de_famount[$de_pid.'_'.$de_CX] = $de_idData[$de_CX.'_famount'];
  						$de_decost[$de_pid.'_'.$de_CY] = ($de_famount[$de_PC] * $de_infData[$de_CY])/$de_dyear;
              //echo $de_decost[$de_pid.'_'.$de_CY]."<BR>";
  						if($de_CX == $this->finplan->baseCurrency){
  							$de_dclc[$de_pid.'_'.$de_e] = $de_decost[$de_pid.'_'.$de_CY];
  						}else{
  							$de_dclc[$de_pid.'_'.$de_e] = $de_decost[$de_pid.'_'.$de_CY] * $de_excData[$de_CY];
  						}
  						$de_Data['DCL_'.$de_pid.'_'.$de_e] = $de_Data['DCL_'.$de_pid.'_'.$de_e] + $de_dclc[$de_pid.'_'.$de_e];//Decom_Costs_lc when trust option is set
  					}
  				}

  			}
  			for($de_i=$this->finplan->startYear;$de_i <= $this->finplan->endYear; $de_i++){// for each year
  				$de_Data['TDCL_'.$de_i] = $de_Data['TDCL_'.$de_i] + $de_Data['DCL_'.$de_pid.'_'.$de_i];//Total_decom_costs_lc
  			}
  		}
      $this->finplan->ced->add($de_Data);
      //echo "Decommissioning = <br>";
     // print_r($de_Data);
  	}
    return $de_Data;
  }
  //Note:: find an example where this is not null

  /**
   * Calculate Total Operation and Maintenance Costs
   **/
  public function updateTotalOperationMaintenanceCosts()
  {
  	$ced_Data = $this->finplan->ced->getByfield(1,'sid');
  	$tom_cpData = $this->finplan->getPlantData();// get all Data for plants
  	$tom_caData = $this->finplan->bbd->getall();//get all calculated OM Data
  	$tom_excData = $this->finplan->getExchangeData();// get exch Data
    $tom_decData = $this->finplan->ced->getByField(1,'sid');

    $tom_Data = Array();

  	if (is_array($tom_caData) && count($tom_caData) > 0) {// check if calculated om Data exist
  		$tom_Data['sid'] = 1;//set SID for storing Data
  		for ($tom_i=$this->finplan->startYear;$tom_i <= $this->finplan->endYear; $tom_i++) {// for each study year
  			for($tom_c = 0; $tom_c < count($this->finplan->allChunks); $tom_c++){// for each currency
  				$tom_CX = $this->finplan->allChunks[$tom_c];
  				$tom_ETC = 'E_'.$tom_CX.'_'.$tom_i;
  				$tom_CY = $tom_CX.'_'.$tom_i;
  				foreach($tom_caData as $tom_row){
  					$tom_Data[$tom_ETC] = $tom_Data[$tom_ETC]+$tom_row[$tom_ETC];// Tot_OM_cost(c,j) Current
  					$tom_Data[$tom_CY] = $tom_Data[$tom_CY]+$tom_row[$tom_CY];// Tot_ OM_cost(c,j) without inflation
  					if($tom_CX == $this->finplan->baseCurrency){
  						$tom_share[$tom_i] = $tom_row[$tom_ETC];
  					}else{
  						$tom_share[$tom_i] = $tom_row[$tom_ETC] * $tom_excData[$tom_CY];
  					}
  					$tom_Data['LC_'.$tom_i] = $tom_Data['LC_'.$tom_i] + $tom_share[$tom_i];// Tot_O&M_Cost_LC
  				}
  			}
  			//O&M & Trust Decom
  			$tom_Data['OMTD_'.$tom_i] = $tom_Data['LC_'.$tom_i] + $ced_Data['TDCL_'.$tom_i];
  		}

      $tom_cpData = $this->finplan->getPlantData();// get Data for all plants in this study

    	if (is_array($tom_cpData) && count($tom_cpData) > 0) {// check if plant exist
        foreach ($tom_cpData as $de_row) {
          $de_idData = $this->finplan->akd->getByField($de_row['id'],'pid');// get generalexpense by plant id
          $de_syear = $de_idData['startyear'];
    			$de_fyear = $de_idData['deccyear'];
          $de_fund = $de_idData['fund'];
          $totalDecom = 0;
          if ($de_fund == 'F') {
            $decomPrefix = 'ADFL_';
          } elseif ($de_fund == 'T') {
            $decomPrefix = 'TDCL_';
          }
          for ($i=$de_syear; $i<=$de_fyear; $i++) {
            $totalDecom += $tom_decData[$decomPrefix.$i];
          }
          if ($de_fund == 'F') {
            //only applicable to 'Fund' Decom option
            //$tom_Data['OMTD_'.($de_fyear+1)] += $totalDecom;
            //commented out - Irej request
          }
        }
      }

  		$this->finplan->bdd->add($tom_Data);
  	}
    return $tom_Data;
  }

  /**
   * Calculate Total Revenue
   **/
  public function updateTotalRevenue()
  {
  	$sl_bkData = $this->finplan->bkd->getByField('1','sid');//get total Data
  	$sl_cgData = $this->finplan->cgd->getByField('1','sid');
    $sl_bbData = $this->finplan->bbd->getByField('1','sid');
  	$sl_bdData = $this->finplan->bdd->getByField('1','sid');
  	$sl_beData = $this->finplan->bed->getByField('1','sid');
  	$sl_bzData = $this->finplan->bzd->getByField('1','sid');
  	$sl_ceData = $this->finplan->ced->getByField('1','sid');
    $rev_Data = Array();

  	for ($rev_i=$this->finplan->startYear;$rev_i <= $this->finplan->endYear; $rev_i++) {
  		//Tot_Revenues_LC(j) = Revenue_Sale_LC(j) + Other_Income_LC(j).   OLD EQ
  		//Tot_Revenues_LC(j) = Revenue_Sale_LC(j) + Tot_Other_Income_LC(j). NEW EQ
  		$rev_Data['R_'.$rev_i] = $sl_bkData['LC_'.$rev_i] + $fri_Tot_Other_Income_LC[$rev_i];

  		//Tot_Optcost_LC (j) = Tot_Expenses_Purchase_LC(j)+ Tot_O&M_Cost_LC(j)+ Tot_Fuel_Cost_LC(j)+ Tot_Gen_Exp_LC(j) +Tot_Decom_Costs_LC(j).
  		$rev_Data['O_'.$rev_i] = $sl_cgData['LC_'.$rev_i] + $sl_bdData['LC_'.$rev_i] + $sl_beData['LC_'.$rev_i] + $sl_bzData['LC_'.$rev_i] + $sl_ceData['TDCL_'.$rev_i]; // total OM+ De OMTD was LC before
  	}
    //_r($sl_bdData);
  	$rev_Data['sid']=1;
  	$this->finplan->cjd->add($rev_Data);
  }

  /**
   * Calculate Royalties
   **/
  public function updateRoyalties()
  {
  	$ryl_cjData = $this->finplan->cjd->getByField('1','sid'); //get total revenue Data
  	$ryl_btData = $this->finplan->btd->getByField('1','sid'); //getroyaltyData
    $ryl_Data = Array();

  	for ($ryl_i=$this->finplan->startYear; $ryl_i <= $this->finplan->endYear; $ryl_i++) {
      $ryl_ii= $ryl_i-1;
  		if ($ryl_btData['C_'.$ryl_i] =='') {
  			$ryl_cost[$ryl_i] = $ryl_cost[$ryl_ii] ;
  		} else {
  			$ryl_cost[$ryl_i] = $ryl_btData['C_'.$ryl_i];
  		}
  		$ryl_tot = $ryl_cjData['R_'.$ryl_i] - (($ryl_cost[$ryl_i]/100) *  $ryl_cjData['O_'.$ryl_i]);//Tot_Revenues_LC(j) - [Roy_%Cost (j) �Tot_Optcost_LC(j)]
  		$ryl_Data['NR_'.$ryl_i] = max(0,$ryl_tot);//Net _Revenues (j) = Maximum {0, Tot_Revenues_LC(j) - [Roy_%Cost (j) �Tot_Optcost_LC(j)]}.

  		if ($ryl_btData['R_'.$ryl_i] =='') {
  			$ryl_royalty[$ryl_i] = $ryl_royalty[$ryl_ii] ;
  		} else {
  			$ryl_royalty[$ryl_i] = $ryl_btData['R_'.$ryl_i];
  		}
  		$ryl_Data['RLC_'.$ryl_i] = ($ryl_royalty[$ryl_i]/100) * $ryl_Data['NR_'.$ryl_i];// Royalty_LC(j) = Roy_Rate(j)�  Net _Revenues (j)
  	}
  	$ryl_Data['sid']=1;
  	$this->finplan->ckd->add($ryl_Data);
  }

  /**
   * Calculate Production
   **/
  public function updateProduction()
  {
  	$pro_apData = $this->finplan->getPlantData();
    $pro_Data = Array();

  	if (is_array($pro_apData) && count($pro_apData) > 0) {
  		foreach ($pro_apData as $pro_row) {
  			$pro_pid = $pro_row['id'];
  			$pro_prod = $pro_row['CurTypeSel'];//
  			$ProChunks = explode(",", $pro_prod);
  			$pro_aqData = $this->finplan->aqd->getByField($pro_row['id'],'pid');
  			for ($pro_i=$this->finplan->startYear;$pro_i <= $this->finplan->endYear; $pro_i++) {
  				for ($pro_c = 0; $pro_c < count($ProChunks); $pro_c++) {
            $pro_key = XmlKey::getKey(Array($ProChunks[$pro_c], $pro_i), true);
  					$pro_pamount = $pro_aqData[$pro_key];
  					$pro_Data[$pro_key] = $pro_Data[$pro_key]+$pro_pamount;
  				}
  			}
  		}
  		$this->finplan->cfd->add($pro_Data);
  	}
  }

}

?>
