<?php

class PlantInvestment extends FinplanService {

  public $finplan;
  public $dep_Dep_Comm_Fixed_Assets_LC;
  public $dep_Dep_Old_Fixed_Asset;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
  }

  /**
   * 2.2.7.	Investment module
   * Update Committed and Existing Investments
   **/
  public function updateIvestmentInformation()
  {
    $inv_Data = Array();//initialise
    $inv_cpData = $this->finplan->getPlantData();
  	$inv_excData = $this->finplan->getExchangeData();
    $inv_infData = $this->finplan->getInflationData();

    // echo "inv_excData = ";
    // print_r($inv_excData);
    // echo "<br>";

    if (is_array($inv_cpData) && count($inv_cpData) > 0) { // check if plants exist

      foreach ($inv_cpData as $inv_row) {
  			$inv_idData = $this->finplan->and->getByField($inv_row['id'],'pid'); // get investment Data for each plant
  			$inv_Data = Array(); //clear Data if any
  			$inv_pid = $inv_row['id'];
  			$inv_Data['pid'] = $inv_pid; // set pid equal to id of this plant id
  			$inv_Data['PW_'.$inv_pid] = 0;
  			for($inv_c = 0; $inv_c < count($this->finplan->allChunks); $inv_c++) { // For each currency
  				$inv_CX = $this->finplan->allChunks[$inv_c];
  				$inv_tot = 'Tot_'.$inv_CX;
  				$inv_total = $inv_idData[$inv_tot]; //get total investment for this plant
  				$inv_cYear = $inv_row['FOyear']-$inv_row['CPeriod'];	//1st construction year  = 1st oper year- construction period

  				for ($inv_i=1; $inv_i <= $inv_row['CPeriod']; $inv_i++) { // for number of construction years
  					$inv_CY = $inv_CX.'_'.$inv_i;
  					$inv_IX = $inv_CX.'_'.$inv_cYear;

  					$inv_Inv = $inv_total * $inv_idData[$inv_CY]/100; // investment = total investment for this currency * percentage for this year (  CInvest_Costs(plant name, currency name)�Invest_Cost_Dist(plant name, currency name,j )  )
            $inv_EInv = $inv_Inv; //
  					$inv_EInv_dp = $inv_Inv * $inv_infData[$inv_IX];
            // echo "inv_EInv_dp = ".$inv_EInv_dp."<br>";
            // echo "inv_excData = ".$inv_excData[$inv_IX]."<br>";
  					$inv_ein = 'EI_'.$inv_IX; //Current_Invest_Costs(plant name, currency name,j)
  					$inv_Data[$inv_ein]= $inv_EInv;
  					if($inv_CX == $this->finplan->baseCurrency) {
  						$inv_excinvst[$inv_i] = $inv_EInv_dp/* * $inv_excData[$inv_IX]*/;  //added july 29
  					}else{
  						$inv_excinvst[$inv_i] = $inv_EInv_dp * $inv_excData[$inv_IX];  // added july 29
  					}
            //echo "inv_Data[C] = ".$inv_CX." = "." CYear = ".$inv_cYear.", = ".$inv_Data['C_'.$inv_i]."<br>";
  					$inv_Data['C_'.$inv_i] = $inv_Data['C_'.$inv_i] + $inv_excinvst[$inv_i];//Current_Invest_Costs_LC(p,k)
  					$inv_cYear++;
  					$inv_pws['PW_'.$inv_pid] = $inv_pws['PW_'.$inv_pid] + $inv_excinvst[$inv_i];
  				}
  				$inv_Data['PW_'.$inv_pid] = $inv_pws['PW_'.$inv_pid];
  			}
  			$this->finplan->aed->add($inv_Data);
  		}
  	}
  }

  /**
   * Calculates VAT
   **/
  public function updateVat()
  {
    $vat_Data = Array();
  	$vat_cpData = $this->finplan->getPlantData();// get Data for all plant s
  	$vat_atData = $this->finplan->atd->getByField('1','sid');
  	$vat_rateinv = $vat_atData['VATRateInvestment'];
  	$vat_coverinv = $vat_atData['VAT_Cover_Inv'];

  	foreach($vat_cpData as $vat_row) {
  		$vat_aeData = $this->finplan->aed->getByField($vat_row['id'],'pid');// get investment Data for each plant
  		$vat_Data = Array();//clear Data if any
  		$vat_pid = $vat_row['id'];
  		$vat_fYear = $vat_row['FOyear']; // 1st oper year
  		$vat_cYear = $vat_row['FOyear']-$vat_row['CPeriod'];
  		for($vat_i=1;$vat_i <= $vat_row['CPeriod']; $vat_i++) { // for number of construction years
  			$vat_IX = 'C_'.$vat_i;
  			$vat_Data['I_'.$vat_pid.'_'.$vat_i] = $vat_rateinv/100 * $vat_coverinv/100 * $vat_aeData[$vat_IX] ;
  			$vat_Data['TI_'.$vat_cYear] = $vat_Data['TI_'.$vat_cYear] + $vat_Data['I_'.$vat_pid.'_'.$vat_i]; //Tot_VAT_Inv_LC(j)
  			$vat_Data['T_'.$vat_pid.'_'.$vat_fYear] = $vat_Data['T_'.$vat_pid.'_'.$vat_fYear] + $vat_Data['I_'.$vat_pid.'_'.$vat_i];//VAT_Inv_TBR_LC(p,k)
  			$vat_cYear++;
  		}
  		$vat_Data['T_'.$vat_fYear] = $vat_Data['T_'.$vat_fYear] + $vat_Data['T_'.$vat_pid.'_'.$vat_fYear];
  	}

  	for($vat_j=$this->finplan->startYear;$vat_j <= $this->finplan->endYear; $vat_j++) {
  		$vat_Data['TT_'.$vat_j] = $vat_Data['TT_'.$vat_j] + $vat_Data['TI_'.$vat_j] - $vat_Data['T_'.$vat_j];
  	}
  	$vat_Data['sid'] = 1;
  	$this->finplan->cmd->add($vat_Data);
  }
  //note:: this returns int instead of float when 0

  /**
   * Calculates Total Investments
   * Updates Committed Investments
   **/
  public function updateTotals()
  {
    $tin_Data = Array(); //initialise
    //$agd = new XmlData($this->finplan->getCaseStudyId(), 'cal_TotalInvestment');

  	$tin_cpData = $this->finplan->getPlantData();// get Data for all plant s
  	$tin_caData = $this->finplan->aed->getall();// get Data for Investments
  	$tin_infData = $this->finplan->getInflationData();// get Data for inflation index
  	$tin_brData = $this->finplan->brd->getByField('1','sid');// get Data for committed investment
  	$tin_excData = $this->finplan->getExchangeData();// get exch Data

    $tin_Data['sid'] = 1;// setting unique id for storing Data
    for($tin_i=$this->finplan->startYear; $tin_i <= $this->finplan->endYear; $tin_i++) {
      $tin_ii = $tin_i-1;
      for($tin_c = 0; $tin_c < count($this->finplan->allChunks); $tin_c++) {
        $tin_CX = $this->finplan->allChunks[$tin_c];
        $tin_CY = $tin_CX.'_'.$tin_i;
        $tin_TI = 'EI_'.$tin_CY;//Esclated/Current Investment
        $tin_CI = 'C_'.$tin_CY;// Committed investment
        $tin_execomminvst[$tin_i] = $tin_brData[$tin_CI] * $tin_excData[$tin_CY];
        $tin_Commit_Invest_Cost_LC['CL_'.$tin_i] = $tin_Commit_Invest_Cost_LC['CL_'.$tin_i] + $tin_execomminvst[$tin_i];// Total committed investment in local currency

        foreach($tin_caData as $tin_row) {// fetching investment for each year from investment file
          $tin_pid = $tin_row['pid'];// plant id
          $tin_Data[$tin_TI] = $tin_Data[$tin_TI] + $tin_row[$tin_TI];// adding in total(i) = investment for all plants(i)
          $tin_pltData = $this->finplan->apd->getByField($tin_pid,'id'); // get plant data for this plant
          $tin_exeinvst[$tin_i] = $tin_row[$tin_TI] * $tin_excData[$tin_CY] * $tin_infData[$tin_CY];// Total  investment X  exchange rate
          $tin_Current_Invest_Costs['IL_'.$tin_pid.'_'.$tin_i] = $tin_Current_Invest_Costs['IL_'.$tin_pid.'_'.$tin_i] + $tin_exeinvst[$tin_i];
        }
      }
      $tin_Data['CL_'.$tin_i] = $tin_Commit_Invest_Cost_LC['CL_'.$tin_i];// Tot_Commit_Invest_Cost_LC (j) ,
    }

    foreach($tin_cpData as $tin_row){
      $tin_CP = $tin_CX.'_'.$tin_row['id'];
      $tin_FOyear = $tin_row['FOyear']+1;// get values for FOyear
      $tin_pid = $tin_row['id'];
      for($tin_i=$this->finplan->startYear;$tin_i <= $this->finplan->endYear; $tin_i++) {
        $tin_ii = $tin_i-1;
        $tin_iii = $tin_i+1;
        $tin_Data['IL_'.$tin_i] = $tin_Data['IL_'.$tin_i] + $tin_Current_Invest_Costs['IL_'.$tin_pid.'_'.$tin_i];// Tot_New_Invest_Costs_LC(j)  ,Total new investment in local currency

        if($tin_FOyear>$tin_iii) {
          $tin_Data['WPLP_'.$tin_pid.'_'.$tin_i] = $tin_Data['WPLP_'.$tin_pid.'_'.$tin_ii] + $tin_Current_Invest_Costs['IL_'.$tin_pid.'_'.$tin_i];// Work_inprog_LC_Plant(p,k)
        } else {
          $tin_Data['WPLP_'.$tin_pid.'_'.$tin_i] = 0 ;
        }
        $tin_Data['WPL_'.$tin_i] += $tin_Data['WPLP_'.$tin_pid.'_'.$tin_i];// Work_inprog_LC(j)
      }

    }

    for($tin_i=$this->finplan->startYear;$tin_i <= $this->finplan->endYear; $tin_i++) {
      $tin_Data['GIL_'.$tin_i] = $tin_Data['CL_'.$tin_i] + $tin_Data['IL_'.$tin_i];// Global_Invest_LC(j)= Tot_New_Invest_Costs_LC(j)+ Tot_Commit_Invest_Costs_LC(j)
    }

    $this->finplan->agd->add($tin_Data);
    //print_r($tin_Data);
    return $tin_Data;
  }

  /**
   * Calculates Depreciation
   * of existing and committed investments
   **/
   public function updateDepreciation($tin_Data)
   {
   		$dep_atData = $this->finplan->atd->getByField('1','sid');
   		$dep_aaData = $this->finplan->aad->getByField('1','sid');//balace Data
      $dep_Dep_Comm_Fixed_Assets_LC = Array();
      $dep_Dep_Old_Fixed_Asset = Array();

   		for($dep_i=$this->finplan->startYear;$dep_i <= $this->finplan->endYear; $dep_i++) {
   			$dep_ii = $dep_i-1;
   			if ($dep_i == $this->finplan->startYear) {
   				$dep_Old_Net_Fixed_Asset[$dep_ii] = $dep_aaData['NetFxdAsst'];
   				$dep_Dep_Commit_Fixed_Assets[$dep_i] = 0;
   				$dep_Commit_Fixed_Asset[$dep_i] = $tin_Data['CL_'.$dep_i] + $dep_aaData['WorkProgress'];
   				$dep_Dep_Old_Fixed_Asset[$dep_i] =  $dep_Old_Net_Fixed_Asset[$dep_ii] * $dep_atData['YearlyDepreciationRate']/100;
   				$dep_Old_Net_Fixed_Asset[$dep_i] = $dep_Old_Net_Fixed_Asset[$dep_ii] - $dep_Dep_Old_Fixed_Asset[$dep_i];
   			} else {
   				$dep_Dep_Old_Fixed_Asset[$dep_i] =  $dep_Old_Net_Fixed_Asset[$dep_ii] * $dep_atData['YearlyDepreciationRate']/100;
   				$dep_Old_Net_Fixed_Asset[$dep_i] = $dep_Old_Net_Fixed_Asset[$dep_ii] - $dep_Dep_Old_Fixed_Asset[$dep_i];
   				$dep_Commit_Fixed_Asset[$dep_i] = $tin_Data['CL_'.$dep_i] + $tin_Data['CL_'.$dep_ii];
   				$dep_Dep_Comm_Fixed_Assets_LC[$dep_i] =( $dep_Commit_Fixed_Asset[$dep_ii] - $dep_Dep_Comm_Fixed_Assets_LC[$dep_ii] ) * $dep_atData['YearlyDepreciationRate']/100;
   			}
   		}
      $fixedAssetData = Array(
        'committed'   =>  $dep_Dep_Comm_Fixed_Assets_LC,
        'old'         =>  $dep_Dep_Old_Fixed_Asset
      );
      return $fixedAssetData;
   }
   //Note:: haven't tested yet

   public function getDepreciationFixedAssets()
   {
     return $dep_Dep_Comm_Fixed_Assets_LC;
   }

   public function getOldDepreciationFixedAssets()
   {
     return $dep_Dep_Old_Fixed_Asset;
   }

   /**
    * Calculates Draw Down
    **/
   public function updateDrawDown()
   {
     $dd_Data2 = Array();//initialise
     $dd_suData = $this->finplan->asd->getall();// get Data for finance sources
     if(is_array($dd_suData) && count($dd_suData) > 0) {// check if Data exist for this sources
       foreach($dd_suData as $dd_row) {
         $dd_Data = Array();//clear Data if any
         $dd_fid = $dd_row['fid'];// get fid for the Data
         $dd_fidChunks = explode("_", $dd_fid);// split the fid
         $dd_Data['fid'] = $dd_fid;// set fid for storing Data
         $dd_cid = $dd_fidChunks[0];// currency id
         $dd_pid = $dd_fidChunks[1];// plant id
         $dd_Data['pid'] = $dd_pid;// set pid for storing Data
         $dd_cpData = $this->finplan->apd->getById($dd_pid);// get plant Data for pid
         $dd_caData = $this->finplan->aed->getByField($dd_pid,'pid');// get investment Data for this plant

         foreach($this->finplan->financeSources as $financesource) {
           $dd_cYear = $dd_cpData['FOyear']-$dd_cpData['CPeriod'];//calculating first construction year
           $dd_totddval = 'Tot_'.$financesource['id'];
           $dd_Data[$dd_totddval] = 0;//set total Data to 0 to intialize
           for($dd_i=1;$dd_i <= $dd_cpData['CPeriod']; $dd_i++) {
             $dd_finfval = $financesource['id'].'_'.$dd_i;
             $dd_finfval2 = $financesource['id'].'_'.$dd_cid.'_'.$dd_cYear;// this  has to be removed but first need to sort do I need to save by year or serial no
             $dd_value = $dd_row[$dd_finfval]; // percentage of investment for this year for this finance type
             $dd_invval = 'EI_'.$dd_cid.'_'.$dd_cYear;
             $dd_eival = $dd_caData[$dd_invval];//esclated Investment for this year for this currency type
             $dd_drawval = $dd_value/100 * $dd_eival;// drawdown = investment percent/100 * EsclatedInvestment (for each year)
             $dd_Data[$dd_finfval] = $dd_drawval;	//DDown equation for each finance source per year
             $dd_Data2[$dd_finfval2] = $dd_Data2[$dd_finfval2]+ $dd_drawval;// this  has to be removed but first need to sort do I need to save by year or serial no
             $dd_Data[$dd_totddval] = $dd_Data[$dd_totddval] + $dd_drawval;//Total DDown equation for each finance source
             $dd_cYear++;
           }
   			}
   			$this->finplan->abd->add($dd_Data);
   		}
   		$dd_Data2['sid'] = 1;
   		$this->finplan->bpd->add($dd_Data2);
   	}

   }
}

?>
