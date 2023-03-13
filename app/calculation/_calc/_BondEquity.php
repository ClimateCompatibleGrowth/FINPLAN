<?php

class BondEquity extends FinplanService {

  public $finplan;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
  }

  /**
   * Calculate Old Bonds
   **/
  public function updateOldBonds($ol_Data)
  {
    $ob_Data = Array(); //initialise
  	$ob_bxData = $this->finplan->bxd->getByField('1','sid');
  	$ob_startyear = $this->finplan->startYear; // set value of start year to variable
  	$ob_Data['sid']=1; // set the sid to 1 for storing values
  	$ob_excData = $this->finplan->getExchangeData();

  	for($ob_c = 0; $ob_c < count($this->finplan->allChunks); $ob_c++) {// loop to get each currency id
  		$ob_CX = $this->finplan->allChunks[$ob_c];// get currency id
  		$ob_rtype = 'RateType_'.$ob_CX;
  		$ob_rattype = $ob_bxData[$ob_rtype];// get the inflation type given by user
  		$ob_oldval = 'OB_'.$ob_CX;
  		$ob_startyear1 = $ob_startyear-1;// setting year for old bonds outstanding
  		$ob_oldoutval[$ob_CX.'_'.$ob_startyear1] = $ob_bxData[$ob_oldval];// Init_Bonds(c)
  		$ob_initial['IBB'] = $ob_initial['IBB'] +($ob_bxData[$ob_oldval] * $ob_excData[$ob_CX.'_'.$ob_startyear1]);// Initial Bonds for Intialbalancesheet
  		for($ob_i=$ob_startyear;$ob_i <= $this->finplan->endYear; $ob_i++){
  			$ob_bon = 'B_'.$ob_CX.'_'.$ob_i;
  			$ob_rep = 'R_'.$ob_CX.'_'.$ob_i;
  			$ob_g = $ob_i -1;
  			$ob_CY = $ob_CX.'_'.$ob_i;
  			$ob_CYY = $ob_CX.'_'.$ob_g;
  			$ob_bonval[$ob_CY] = $ob_bxData[$ob_bon];
  			$ob_repval[$ob_CY] = $ob_bxData[$ob_rep];
  			//Old_Bonds_outstand(c,j) = Old_Bonds_outstand(c,j-1) + Old_Bonds(c,j) - Old_Bonds_Repay(c,j),
  			$ob_oldoutval[$ob_CY] = $ob_oldoutval[$ob_CYY] + $ob_bonval[$ob_CY] - $ob_repval[$ob_CY];

  			if($ob_rattype =="SR"){ //if average rate
  				$ob_int = 'Avg_'.$ob_CX;
  				$ob_intr = $ob_bxData[$ob_int];
  				$ob_intval[$ob_CY] = ($ob_intr/100) * $ob_oldoutval[$ob_CYY];//Old_Bonds_Return (c,j)= Av_Return( c) �Old_Bonds_outstand(c,j-1).
  			}elseif($ob_rattype =="YR"){ // if Yearly interest payment
  				$ob_intp = XmlKey::KEY_I.'_'.$ob_CY;
  				$ob_intval[$ob_CY] = $ob_bxData[$ob_intp];
  			}


  			$ob_exeouts[$ob_CY] = $ob_oldoutval[$ob_CY] * $ob_excData[$ob_CY];
  			$ob_exeint[$ob_CY] = $ob_intval[$ob_CY] * $ob_excData[$ob_CY];
  			$ob_exeintn[$ob_CY] = $ob_intval[$ob_CY] * $ob_excData[$ob_CYY];
  			$ob_exerep[$ob_CY] = $ob_repval[$ob_CY] * $ob_excData[$ob_CY];
  			$ob_exebon[$ob_CY] = $ob_bonval[$ob_CY] * $ob_excData[$ob_CY];

  			$ob_Data['BD_'.$ob_CY] = $ob_bonval[$ob_CY];//Old_Bonds(c,j)
  			$ob_Data['BDL_'.$ob_i] = $ob_Data['BDL_'.$ob_i] + $ob_exebon[$ob_CY];//Old_Bonds_LC

  			$ob_Data['B_'.$ob_CY] = $ob_oldoutval[$ob_CY];//Old_Bonds_outstand(c,j)
  			$ob_Data['BL_'.$ob_i] = $ob_Data['BL_'.$ob_i] + $ob_exeouts[$ob_CY];//Old_Bonds_Outstand_LC

  			$ob_Data[XmlKey::KEY_I.'_'.$ob_CY] = $ob_intval[$ob_CY];//Old_Bonds_Repay(c,j)
  			$ob_Data['IL_'.$ob_i] = $ob_Data['IL_'.$ob_i] + $ob_exeint[$ob_CY];//Old_Bonds_Repay_LC
  			$ob_Data['ILN_'.$ob_i] = $ob_Data['ILN_'.$ob_i] + $ob_exeintn[$ob_CY];//Old_Bonds_Repay_LC for current maturity

  			$ob_Data['R_'.$ob_CY] = $ob_repval[$ob_CY];//Old_Bonds_Return(c,j)
  			$ob_Data['RL_'.$ob_i] = $ob_Data['RL_'.$ob_i] + $ob_exerep[$ob_CY];//Old_Bonds_Return_LC


  		}
  		$ob_Data['IO_'.$ob_CX] = $ob_bxData[$ob_oldval] - $ob_Data[XmlKey::KEY_I.'_'.$ob_CX.'_'.$ob_startyear];// Init_Net_Bonds_outstand (c ) = Init_Bonds (c ) - Old_Bonds_Repay_LC(c, First_year)
  		$ob_Data['IOLC'] = $ob_Data['IOLC'] +( $ob_Data['IO_'.$ob_CX] * $ob_excData[$ob_CX.'_'.$ob_startyear]);// Init_Net_Bonds_outstand_LC
  		$ob_Data['ICM'] = $ob_Data['RL_'.$ob_startyear] + $ol_Data['RL_'.$ob_startyear];// Init_Current_Maturity = Old_Bonds_Repay_LC(First_year) + Old_Loans_Repay_LC(First_year).

  	}
  	$ob_Data['IBB'] = $ob_initial['IBB'];
    //$ob_Data['IBB'] = $ob_Data['BL_'.$ob_startyear] ;
  	$this->finplan->cbd->add($ob_Data);
    return $ob_Data;
  }
  //Note:: find a case study where this is not null

  /**
   * Calculate New Bonds
   **/
  public function updateNewBonds()
  {
    $bo_Data = Array(); //initialise
  	$bo_bmData = $this->finplan->bmd->getByField('1','sid');
  	$bo_startyear = $this->finplan->startYear; // set value of start year to variable
  	$bo_Data['sid']=1; // set the sid to 1 for storing values
  	$bo_excData = $this->finplan->getExchangeData();// get exch Data

  	for($bo_c = 0; $bo_c < count($this->finplan->allChunks); $bo_c++) {// loop to get each currency id
  		$bo_CX = $this->finplan->allChunks[$bo_c];// get currency id
  		$bo_rate = 'ER_'.$bo_CX;
  		$bo_rateval = $bo_bmData[$bo_rate];// get the rate
  		$bo_trm = 'BT_'.$bo_CX;
  		$bo_trmval = $bo_bmData[$bo_trm];// get the bond term
  		$bo_bfirst = 'B_'.$bo_CX.'_'.$bo_startyear;

  		for($bo_i=$bo_startyear;$bo_i <= $this->finplan->endYear; $bo_i++) {
  			$bo_CY = $bo_CX.'_'.$bo_i;
  			$bo_bon = 'B_'.$bo_CY;
  			$bo_j = $bo_i + $bo_trmval;
  			$bo_g = $bo_i-1;
  			$bo_CYY = $bo_CX.'_'.$bo_g;
  			$bo_bonval[$bo_CY] = $bo_bmData[$bo_bon];
  			if ($bo_i==$this->finplan->startYear) {
  				$bo_outval[$bo_CX.'_'.$bo_startyear] = $bo_bmData[$bo_bfirst];
  				$bo_repval[$bo_CX.'_'.$bo_j] = $bo_outval[$bo_CX.'_'.$bo_startyear];//setting repayment by adding i+term
  			} else {
  				$bo_repval[$bo_CX.'_'.$bo_j] = $bo_bonval[$bo_CY];//setting repayment by adding i+term
  				$bo_outval[$bo_CY] = $bo_outval[$bo_CX.'_'.$bo_g] + $bo_bonval[$bo_CY] -$bo_repval[$bo_CY];// bondOuts(i-1 ) +bond(i)-repayment
  				$bo_intval[$bo_CY] = $bo_outval[$bo_CX.'_'.$bo_g] * ($bo_rateval/100); //calculate interest = bondOuts(i-1 ) * exp rate
  				$bo_retval[$bo_CY] = $bo_outval[$bo_CY] * ($bo_rateval); //New_bonds_Return(c,j)= Bonds_Outstand(c,j)� Expeted_Rate(c).
  			}
  			$bo_Data['B_'.$bo_CY] = $bo_bonval[$bo_CY];//Bonds (currency name,k)
  			$bo_Data['R_'.$bo_CY] = $bo_repval[$bo_CY];//Bonds_Repayment (currency name,k)
  			$bo_Data['O_'.$bo_CY] = $bo_outval[$bo_CY];//Bonds_Outstand(currency name,j)
  			$bo_Data[XmlKey::KEY_I.'_'.$bo_CY] = $bo_intval[$bo_CY];//Bonds_Interest(currency name,j)
  			$bo_Data['N_'.$bo_CY] = $bo_retval[$bo_CY];//New_bonds_Return(c,j)

  			$bo_newbrepay[$bo_CY] = $bo_repval[$bo_CY]  * $bo_excData[$bo_CY];//New_Bonds_Repay_LC(j)  = bondrepay(j ) * exchange rate
  			$bo_newbrepayn[$bo_CY] = $bo_repval[$bo_CY]  * $bo_excData[$bo_CYY];//New_Bonds_Repay_LC(j)  = bondrepay(j ) * exchange rate for current maturity
  			$bo_newbout[$bo_CY] = $bo_outval[$bo_CY]  * $bo_excData[$bo_CY];//New_Bonds_Out_LC(j)  = bondoutstanding(j ) * exchange rate
  			$bo_newbond[$bo_CY] = $bo_bonval[$bo_CY]  * $bo_excData[$bo_CY];//New_Bonds_LC(j)  = bond(j ) * exchange rate
  			$bo_newbreturn[$bo_CY] = $bo_retval[$bo_CY]  * $bo_excData[$bo_CY];//New_Bonds_Return_LC(j)  = New_Bonds_Return(j) * exchange rate
  			$bo_newbint[$bo_CY] = $bo_intval[$bo_CY] * $bo_excData[$bo_CY];//New_Bonds_Int_LC(j)

  			$bo_Data['RLC_'.$bo_i] = $bo_Data['RLC_'.$bo_i] + $bo_newbrepay[$bo_CY];//New_Bonds_Repayment_LC(j)
  			$bo_Data['RLCN_'.$bo_i] = $bo_Data['RLCN_'.$bo_i] + $bo_newbrepayn[$bo_CY];//New_Bonds_Repayment_LC(j)
  			$bo_Data['OLC_'.$bo_i] = $bo_Data['OLC_'.$bo_i] + $bo_newbout[$bo_CY];//New_Bonds_Outstand_LC(j)
  			$bo_Data['BLC_'.$bo_i] = $bo_Data['BLC_'.$bo_i] + $bo_newbond[$bo_CY];//New_Bonds_LC(j)
  			$bo_Data['NLC_'.$bo_i] = $bo_Data['NLC_'.$bo_i] + $bo_newbreturn[$bo_CY];//New_Bonds_Return_LC(j)
  			$bo_Data['ILC_'.$bo_i] = $bo_Data['ILC_'.$bo_i] + $bo_newbint[$bo_CY];//New_Bonds_Int_LC(j)
  		}
  	}
  	$this->finplan->ccd->add($bo_Data);
    $bondsData = Array(
      'bo_Data'   =>  $bo_Data,
      'bo_CY'     =>  $bo_CY  //Why is this used at a later stage?
    );
    return $bondsData;
  }

  /**
   * Calculate Equity
   **/
  public function updateEquity()
  {
    $eq_Data = Array();
    $eq_totout = Array();
  	$eq_aaData = $this->finplan->aad->getByField('1','sid');
  	$eq_excData = $this->finplan->getExchangeData();//get exchage rate list
  	$eq_infData = $this->finplan->getInflationData();//get inflation rate list
  	$eq_blData = $this->finplan->bld->getByField('1','sid');
  	$eq_startyear = $this->finplan->startYear; // set value of start year to variable
  	$eq_Data['sid']=1; // set the sid to 1 for storing values
  	$eq_totout[$eq_startyear] = 0;

  	for ($eq_c = 0; $eq_c < count($this->finplan->baseChunks); $eq_c++) {// loop to get each currency id
  		$eq_CX = $this->finplan->baseChunks[$eq_c];// get currency id
  		$eq_drate = 'DR_'.$eq_CX;
  		$eq_drateval = $eq_blData[$eq_drate];// get the rate
  		//$eq_ini = 'EQUITY'.$eq_CX;
  		$eq_inival = $eq_aaData['Equity'];// get the intital equity
  		$eq_efirst = 'E_'.$eq_CX.'_'.$eq_startyear;
  		$eq_erfirst = 'ER_'.$eq_CX.'_'.$eq_startyear;
  		$eq_Data['IE'] = $eq_Data['IE'] +($eq_inival * $eq_excData[$eq_CX.'_'.$eq_startyear]);//  Init_Equity_LC
  		$eq_Data['IE_'.$eq_CX] = $eq_inival ;// Init_Equity(C)
  		for ($eq_i=$eq_startyear;$eq_i <= $this->finplan->endYear; $eq_i++) {
        $eq_CY = $eq_CX.'_'.$eq_i;
  			$eq_ii = $eq_i -1;
  			$eq_CYY = $eq_CX.'_'.$eq_ii;
  			$eq_equ = 'E_'.$eq_CY;
  			$eq_equr = 'ER_'.$eq_CY;
  			$eq_equval[$eq_i] = $eq_blData[$eq_equ];//get equity
  			$eq_returnval[$eq_i] = $eq_blData[$eq_equr];//get equity returned

  			if($eq_i == $eq_startyear){
  				$eq_outval[$eq_startyear] = ($eq_inival + $eq_blData[$eq_efirst])- $eq_blData[$eq_erfirst];//Equity_Outstand(c,First_year)
  			}else{
  				$eq_outval[$eq_i] = $eq_outval[$eq_i-1] + $eq_equval[$eq_i] -$eq_returnval[$eq_i];// Equity_Outstand(currency name,j) = equOuts(i-1 ) +equ(i)-repayment
  			}

  			$eq_Data['CE_'.$eq_CY] = $eq_equval[$eq_i] * $eq_infData[$eq_CY];//Equity Current(currency name,j)
  			$eq_Data['ED_'.$eq_CY] = $eq_blData[$eq_equ];//Equity Drawdown(currency name,j)
  			$eq_Data['E_'.$eq_CY] = $eq_outval[$eq_i];//Equity_Outstand(currency name,j)
  			$eq_Data['R_'.$eq_CY] = $eq_returnval[$eq_i];//Equity_Returned(currency name,j)
  			$eq_Data['D_'.$eq_CY] = $eq_divval[$eq_i];//Dividend _Due(currency name, j)

  			$eq_toteq[$eq_i] = $eq_returnval[$eq_i] * $eq_excData[$eq_CY];// Equity_Returned(currency name,j)   * Exch Rate
  			$eq_toteqn[$eq_i] = $eq_returnval[$eq_i] * $eq_excData[$eq_CYY];// Equity_Returned(currency name,j)   * Exch Rate(j+1) change for current maturity
  			$eq_neweq[$eq_i] = $eq_equval[$eq_i] * $eq_excData[$eq_CY];//New_Equity_LC(j)

  			$eq_Data['E_'.$eq_i] = $eq_Data['E_'.$eq_i] + $eq_toteq[$eq_i];//Tot_Equity_Repay_LC (j)
  			$eq_Data['EN_'.$eq_i] = $eq_Data['EN_'.$eq_i] + $eq_toteqn[$eq_i];//Tot_Equity_Repay_LC (j)  for current maturity
  			$eq_Data['N_'.$eq_i] = $eq_Data['N_'.$eq_i] + $eq_neweq[$eq_i];//New_Equity_LC(j)
  		}
  	}

    //Calculate Total Equity Outst Localcurrency
    for($eq_i=$eq_startyear;$eq_i <= $this->finplan->endYear; $eq_i++) {
      for($eq_c = 0; $eq_c < count($this->finplan->baseChunks); $eq_c++) {// loop to get each currency id
        $eq_CX = $this->finplan->baseChunks[$eq_c];
        $eq_CY = $eq_CX.'_'.$eq_i;
        if($eq_CX == $this->finplan->baseCurrency) {
          $eq_totout[$eq_i] = $eq_totout[$eq_i] + $eq_Data['E_'.$eq_CY];
        } else {
          $eq_totout[$eq_i] = $eq_totout[$eq_i] + ($eq_Data['E_'.$eq_CY] * $eq_excData[$eq_CY]);//Tot_Equity_Outstand_LC = EOutstd * Exch Rate
        }
      }
      $eq_Data['T_'.$eq_i] = $eq_totout[$eq_i];//Tot_Equity_Outstand_LC = EOutstd * Exch Rate
    }
    $this->finplan->cdd->add($eq_Data);

    $equityData = Array(
      'eq_Data'       =>  $eq_Data,
      'eq_totout'     =>  $eq_totout,
      'eq_drateval'   =>  $eq_drateval
    );
    return $equityData;
  }

}
?>
