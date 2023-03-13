<?php

//@todo update Sales & Purchases (very similar)
//refactor into two smaller functions

class PurchaseLoan extends FinplanService {

  public $finplan;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
  }


  /**
   * Calculate Total Project Loan
   **/
  public function updateTotalProjectLoan()
  {
    $tcl_Data = Array();
  	$tcl_cpData = $this->finplan->getPlantData();// get all Data for plants
  	$tcl_excData = $this->finplan->getExchangeData();// get exch Data
  	$tcl_Data['sid'] = 1;//set SID for storing Data
  	foreach($tcl_cpData as $tcl_row) {
  		$tcl_CP = $tcl_CX.'_'.$tcl_row['id'];
  		$tcl_bdData = $this->finplan->afd->getByField($tcl_row['id'],'pid');// get values from cal_balance
  		$tcl_pid = $tcl_row['id'];
  		foreach($this->finplan->financeSources as $financesource) {
        if($financesource['type'] == 'C') {
          $tcl_FS = $financesource['id'];
  				for($tcl_c = 0; $tcl_c < count($this->finplan->allChunks); $tcl_c++) {
  					$tcl_CX = $this->finplan->allChunks[$tcl_c];
  					for($tcl_i=$this->finplan->startYear;$tcl_i <= $this->finplan->endYear; $tcl_i++) {// for each study year
              $tcl_ii = $tcl_i -1;
  						$tcl_CY = $tcl_CX.'_'.$tcl_i;
  						$tcl_CYY = $tcl_CX.'_'.$tcl_ii;
  						$tcl_ddval = 'DD_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;//for comm loan
  						$tcl_repval = 'Repy_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i.'_'.$tcl_pid;// for comm loan repay
  						$tcl_balval = 'Bal_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;// for comm loan Balance
  						$tcl_intrval = 'Int_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;// for comm loan Interest
  						$tcl_tec = 'L_'.$tcl_CY;//New_comm_loans(c,j)
  						$tcl_tecr = 'R_'.$tcl_CY;//New_comm_loans_Repay(c,j)
  						$tcl_tecb = 'B_'.$tcl_CY;//New_comm_loans_Balance(c,j)
  						$tcl_teci = XmlKey::KEY_I.'_'.$tcl_CY;//New_comm_loans_Interest(c,j) or New_Loans_Interest(c, j)
  						$tcl_totufval = 'TotUF_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;
  						$tcl_tototval = 'TotOT_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;
  						$tcl_totdfval = 'TotDF_'.$tcl_CX.'_'.$tcl_FS.'_'.$tcl_i;
  						//calacualte New_comm_loans(c,j),New_comm_loans_Repay(c,j),New_comm_loans_Balance(c,j),New_comm_loans_Interest(c,j)
  						$tcl_Data[$tcl_tec] = $tcl_Data[$tcl_tec] +  $tcl_bdData[$tcl_ddval];//commloan+commloanIDC_int
  						$tcl_Data[$tcl_tecr] = $tcl_Data[$tcl_tecr] + $tcl_bdData[$tcl_repval] ;//commloan_repay+commloanIDC_repay
  						$tcl_Data[$tcl_tecb] = $tcl_Data[$tcl_tecb] + $tcl_bdData[$tcl_balval] ;//commloan_bal+commloanIDC_bal
  						$tcl_Data[$tcl_teci] = $tcl_Data[$tcl_teci] + $tcl_bdData[$tcl_intrval] ;//commloan_int+commloanIDC_int
  						$tcl_teclc[$tcl_i] = $tcl_bdData[$tcl_ddval] * $tcl_excData[$tcl_CY];
  						$tcl_tecrlc[$tcl_i] = $tcl_bdData[$tcl_repval]  * $tcl_excData[$tcl_CY];
  						$tcl_tecrlcn[$tcl_i] = $tcl_bdData[$tcl_repval]  * $tcl_excData[$tcl_CYY];
  						$tcl_tecblc[$tcl_i] = $tcl_bdData[$tcl_balval]  * $tcl_excData[$tcl_CY];
  						$tcl_tecilc[$tcl_i] = $tcl_bdData[$tcl_intrval]  * $tcl_excData[$tcl_CY];
  						$tcl_tecuflc[$tcl_i] = $tcl_bdData[$tcl_totufval] * $tcl_excData[$tcl_CY];
  						$tcl_tecotlc[$tcl_i] = $tcl_bdData[$tcl_tototval] * $tcl_excData[$tcl_CY];
  						$tcl_tecdflc[$tcl_i] = $tcl_bdData[$tcl_totdfval] * $tcl_excData[$tcl_CY];
  						$tcl_Data['LLC_'.$tcl_i] = $tcl_Data['LLC_'.$tcl_i] + $tcl_teclc[$tcl_i];// New_comm_loan_LC
  						$tcl_Data['RLC_'.$tcl_i] = $tcl_Data['RLC_'.$tcl_i] + $tcl_tecrlc[$tcl_i];// New_comm_loan_Repay_LC
  						$tcl_Data['RLCN_'.$tcl_i] = $tcl_Data['RLCN_'.$tcl_i] + $tcl_tecrlcn[$tcl_i];// New_comm_loan_Repay_LC for current maturity
  						$tcl_Data['BLC_'.$tcl_i] = $tcl_Data['BLC_'.$tcl_i] + $tcl_tecblc[$tcl_i];// New_comm_loan_Balance_LC
  						$tcl_Data['ILC_'.$tcl_i] = $tcl_Data['ILC_'.$tcl_i] + $tcl_tecilc[$tcl_i];// New_comm_loan_Int_LC or  New_Loans_Interest_LC
  						$tcl_Data['OTLC_'.$tcl_i] = $tcl_Data['OTLC_'.$tcl_i] + $tcl_tecotlc[$tcl_i];// New_comm_loan_FEE_OTI_LC
  						$tcl_Data['DFLC_'.$tcl_i] = $tcl_Data['DFLC_'.$tcl_i] + $tcl_tecdflc[$tcl_i];// New_comm_loan_FEE_DD_LC
  						$tcl_Data['UFLC_'.$tcl_i] = $tcl_Data['UFLC_'.$tcl_i] + $tcl_tecuflc[$tcl_i];// New_comm_loan_FEE_UF_LC
  						$tcl_Data['TFEE_'.$tcl_CY] = $tcl_Data['TFEE_'.$tcl_CY] + $tcl_bdData[$tcl_totufval] + $tcl_bdData[$tcl_tototval] +$tcl_bdData[$tcl_totdfval];//New_comm_loan_fee(c,j)
  						$tcl_Data['FLC_'.$tcl_i] = $tcl_Data['FLC_'.$tcl_i] + $tcl_tecotlc[$tcl_i] + $tcl_tecdflc[$tcl_i] + $tcl_tecuflc[$tcl_i];//New_comm_loan_fee_lc(c,j)
  					}
  				}
  			}
  		}
  	}
  	$this->finplan->cid->add($tcl_Data);
    return $tcl_Data;
  }
  //Note:: find case study where this is not null

  /**
   * Calculate Sales
   **/
  public function updateSales()
  {
    $sl_excData = $this->finplan->getExchangeData();//get exchage rate list
  	$sl_azData = $this->finplan->azd->getall();// get sale Data
  	$sl_adData = $this->finplan->getInflationData();//get inflation index Data

    $sl_Data = Array();
    $sl_Data2 = Array();
    $sl_PRC = Array();

  	if (is_array($sl_azData) && count($sl_azData) > 0) {
      foreach($sl_azData as $sl_row) {
        $sl_Data = Array();//intialise the Data set
  			$sl_Data['fid'] = $sl_row['id'];
  			$sl_Data['Name'] = $sl_row['Name'];
  			$sl_Data['ClientName'] = $sl_row['ClientName'];
  			$sl_client = $sl_row['ClientName'];
  			$sl_Data['TradeCurrency'] = $sl_row['TradeCurrency'];
  			$sl_Data['PriceBase'] = $sl_row['PriceBase'];
  			$sl_pricebase = $sl_row['PriceBase'];
  			$sl_LIX[$this->finplan->startYear-1]=1;
        $startYear = $this->finplan->startYear;
        if ($sl_row['Price'] == 'SC') {
          $sl_PRC[$startYear] = $sl_row['PriceBase'];
          $sl_Data['P_'.$sl_row['TradeCurrency'].'_'.$startYear] = $sl_row['PriceBase'];
          $startYear++;
        } else {
          $startYear--;
        }
  			for ($sl_i=$startYear; $sl_i <= $this->finplan->endYear; $sl_i++) {
  				$sl_amtval = 'Amt_'.$sl_i;
  				if ($sl_row['Amount'] == 'FD') { //if Amount type is fixed
  					$sl_AMT[$sl_i] = $sl_row['AmountFixed'];
  				} elseif ($sl_row['Amount'] == 'YR') {//if Amount type is yearly change
            if ($sl_row[$sl_amtval] =='') { //if Amount is empty for this year then take value from previous year
              $sl_AMT[$sl_i] = $sl_AMT[$sl_i-1];
  					} else {
  						$sl_AMT[$sl_i] = $sl_row[$sl_amtval];// Amount  for this year given by user
  					}
  				}
  				$sl_priceval = 'Pri_'.$sl_i;
  				$sl_CX = $sl_row['TradeCurrency'];
  				$sl_CY = $sl_CX.'_'.$sl_i;
  				if($sl_row['Price'] == 'SC') { //if Standard Change Relative to Inflation
  					$sl_PRF[$sl_i] = $sl_row['PriceFixed']/100;
  					$sl_inflt = $sl_adData['I_'.$sl_CY]/100; //get the local inflation, not the local index (new change)
  					//$sl_baseyear = $this->finplan->startYear -1;
  					//$sl_raise = $sl_i - $sl_baseyear;
  					$sl_standchange = 1 + $sl_PRF[$sl_i];
  					/*if ($sl_raise == 0) {
  						$sl_LIX[$sl_i] = $sl_standchange;
  					} else {
  						$sl_LIX[$sl_i] = pow($sl_standchange, $sl_raise);
  					}*/
  					//$sl_PRC[$sl_i] = $sl_pricebase * $sl_LIX[$sl_i] * $sl_inflt;
            //@todo remove dead code once confirmed
            $sl_PRC[$sl_i] = $sl_PRC[$sl_i-1] * ($sl_standchange + $sl_inflt);

  				} elseif ($sl_row['Price'] == 'YC') {//if Yearly Price Change Relative to Inflation
  					if ($sl_row[$sl_priceval] =='') { //if Inflation Deviation is empty for this year then take value from previous year
  						$sl_PRF[$sl_i] = $sl_PRF[$sl_i-1];
  					} else {
  						$sl_PRF[$sl_i] = $sl_row[$sl_priceval]/100;// Inflation Deviation  for this year given by user
  					}
  					$sl_inflt = $sl_adData[XmlKey::KEY_I.'_'.$sl_CY];
  					$sl_LIX[$sl_i] = 1 + $sl_PRF[$sl_i] + ($sl_inflt/100);
  					if ($sl_i == $this->finplan->startYear) {
  						$sl_PRC[$sl_i] = $sl_row['PriceBase'];
  					} else {
  						$sl_PRC[$sl_i] = $sl_PRC[$sl_i-1] * $sl_LIX[$sl_i]; //currentprice(j-1) *[1+annualchange+ inflation]
  					}
  				} elseif($sl_row['Price'] == 'CP') {//if  Yearly Current Price
  					if ($sl_row[$sl_priceval] =='') { //if empty for this year then take value from previous year
  						$sl_PRF[$sl_i] = $sl_PRF[$sl_i-1];
  					} else {
  						$sl_PRF[$sl_i] = $sl_row[$sl_priceval];// price  for this year given by user
  					}
            $sl_PRC[$sl_i] = $sl_PRF[$sl_i]; //
  				}
  				$sl_PRC[$sl_i] = $sl_PRC[$sl_i];
  				$sl_Data['Q_'.$sl_CY] = $sl_AMT[$sl_i];// Quanitity is passed to Data
  				$sl_Data[XmlKey::KEY_I.'_'.$sl_CY] = $sl_PRF[$sl_i];//Inflation Deviation by user passed to Data
  				$sl_Data['R_'.$sl_CY] = $sl_AMT[$sl_i] * $sl_PRC[$sl_i];// Revenue_Sale = quantity * price
  				$sl_Data['P_'.$sl_CY] = $sl_PRC[$sl_i];// Price is passed to Data
  				if ($sl_CX == $this->finplan->baseCurrency) {
  					$sl_share[$sl_i] = $sl_AMT[$sl_i] * $sl_PRC[$sl_i];
  				} else {
  					$sl_share[$sl_i] = ($sl_AMT[$sl_i] * $sl_PRC[$sl_i]) * $sl_excData[$sl_CY];
  				}
  				$sl_Data2['LC_'.$sl_i] = $sl_Data2['LC_'.$sl_i] + $sl_share[$sl_i];// Revenue_Sale_LC(j)
  				$sl_Data2['RS_'.$sl_CY] = $sl_Data2['RS_'.$sl_CY] + ($sl_AMT[$sl_i] * $sl_PRC[$sl_i]);// Revenue_Sale(C,j)
  				$sl_Data2['RSC_'.$sl_client.'_'.$sl_CY] = $sl_Data2['RSC_'.$sl_client.'_'.$sl_CY] + ($sl_AMT[$sl_i] * $sl_PRC[$sl_i]);// Revenue_Sale_Client(d,C,j)
  			}
  			$this->finplan->ayd->add($sl_Data);
  		}

  		$sl_Data2['sid'] = 1;
  		$this->finplan->bkd->add($sl_Data2);
  	}
    // echo "Sales data = <br>";
    // print_r($sl_Data2);
    return $sl_Data2;
  }

  /**
   * Calculate Purchase
   **/
  public function updatePurchase()
  {
  	$pr_arData = $this->finplan->ard->getall();
  	$pr_adData = $this->finplan->getInflationData();
  	$pr_excData = $this->finplan->getExchangeData();

    $pr_Data = Array();//initialise
    $pr_Data2 = Array();//initialise

  	if(is_array($pr_arData) && count($pr_arData) > 0){

  		foreach($pr_arData as $pr_row){
  			$pr_Data = Array();//intialise the Data set
  			$pr_Data['fid'] = $pr_row['id'];
  			$pr_Data['Name'] = $pr_row['Name'];
  			$pr_Data['ClientName'] = $pr_row['ClientName'];
  			$pr_Data['TradeCurrency'] = $pr_row['TradeCurrency'];
  			$pr_Data['PriceBase'] = $pr_row['PriceBase'];
  			$pr_pricebase = $pr_row['PriceBase'];
  			$pr_LIX[$this->finplan->startYear-1]=1;
        $startYear = $this->finplan->startYear;
        if ($pr_row['Price'] == 'SC') {
          $pr_PRC[$startYear] = $pr_row['PriceBase'];
          $pr_Data['P_'.$pr_row['TradeCurrency'].'_'.$startYear] = $pr_row['PriceBase'];
          $startYear++;
        } 
  			for($pr_i=$startYear; $pr_i <= $this->finplan->endYear; $pr_i++){
  				$pr_amtval = 'Amt_'.$pr_i;
  				if ($pr_row['Amount'] == 'FD') { //if Amount type is fixed
  					$pr_AMT[$pr_i] = $pr_row['AmountFixed'];
  				} elseif ($pr_row['Amount'] == 'YR') {//if Amount type is yearly change
  					if($pr_row[$pr_amtval] =='') { //if Amount is empty for this year then take value from previous year
  						$pr_AMT[$pr_i] = $pr_AMT[$pr_i-1];
  					} else {
  						$pr_AMT[$pr_i] = $pr_row[$pr_amtval];// Amount  for this year given by user
  					}
  				}
  				$pr_Data[$pr_amtval] = $pr_AMT[$pr_i];// AMOUNT is passed to Data
  				$pr_Data2[$pr_Data['Name'].'_'.$pr_i] = $pr_Data2[$pr_Data['Name'].'_'.$pr_i] + $pr_AMT[$pr_i];// AMOUNT is passed to Data
  				$pr_priceval = 'Pri_'.$pr_i;
  				$pr_CX = $pr_row['TradeCurrency'];
  				$pr_CY = $pr_CX.'_'.$pr_i;
  				if($pr_row['Price'] == 'SC') { //if Price type is fixed
  					$pr_PRF[$pr_i] = $pr_row['PriceFixed']/100;//get value for Inflation Deviation for Price of Standard Change
  					$pr_inflt = $pr_adData['I_'.$pr_CY]/100;// get local inflation, not the index
  					//$pr_baseyear = $this->finplan->startYear -1; // calculate base year
  					//$pr_raise = $pr_i - $pr_baseyear; // use baseyear
  					$pr_standchange = 1+$pr_PRF[$pr_i];
  					//$pr_LIX[$pr_i] = pow($pr_standchange,$pr_raise);
  					//$pr_PRC[$pr_i] = $pr_pricebase * $pr_LIX[$pr_i]* $pr_inflt; //base price * Inflation Deviation * inflation index
            $pr_PRC[$pr_i] = $pr_PRC[$pr_i-1] * ($pr_standchange + $pr_inflt);
  				} elseif($pr_row['Price'] == 'YC') {//if Inflation Deviation type is yearly change
  					if ($pr_row[$pr_priceval] =='') { //if Inflation Deviation is empty for this year then take value from previous year
  						$pr_PRF[$pr_i] = $pr_PRF[$pr_i-1];
  					} else {
  						$pr_PRF[$pr_i] = $pr_row[$pr_priceval]/100;// Inflation Deviation  for this year given by user
  					}
  					$pr_inflt = $pr_adData[XmlKey::KEY_I.'_'.$pr_CY];
  					$pr_LIX[$pr_i] = 1 + $pr_PRF[$pr_i] + ($pr_inflt/100);
  					if($pr_i == $this->finplan->startYear){
  						$pr_PRC[$pr_i] = $pr_row['PriceBase'];
  					}else{
  						$pr_PRC[$pr_i] = $pr_PRC[$pr_i-1] * $pr_LIX[$pr_i]; //currentprice(j-1) *[1+annualchange+ inflation]
  					}
  				}elseif($pr_row['Price'] == 'CP'){//if  yearly current price
  					if($pr_row[$pr_priceval] ==''){ //if empty for this year then take value from previous year
  						$pr_PRF[$pr_i] = $pr_PRF[$pr_i-1];
  					}else{
  						$pr_PRF[$pr_i] = $pr_row[$pr_priceval];// price  for this year given by user
  					}
  						$pr_PRC[$pr_i] = $pr_PRF[$pr_i]; //
  				}
  				$pr_PRC[$pr_i] = $pr_PRC[$pr_i];

  				$pr_Data['Q_'.$pr_CY] = $pr_AMT[$pr_i];// Quanitity is passed to Data
  				$pr_Data[XmlKey::KEY_I.'_'.$pr_CY] = $pr_PRF[$pr_i];//Inflation Deviation by user passed to Data
  				$pr_Data['E_'.$pr_CY] = $pr_AMT[$pr_i] * $pr_PRC[$pr_i];// expenditure = quantity * price
  				$pr_Data['P_'.$pr_CY] = $pr_PRC[$pr_i];// Price is passed to Data

  				if($pr_CX == $this->finplan->baseCurrency){
  					$pr_share[$pr_i] = $pr_AMT[$pr_i] * $pr_PRC[$pr_i];
  				}else{
  					$pr_share[$pr_i] = ($pr_AMT[$pr_i] * $pr_PRC[$pr_i]) * $pr_excData[$pr_CY];
  				}
  				$pr_Data2['LC_'.$pr_i] = $pr_Data2['LC_'.$pr_i] + $pr_share[$pr_i];//Tot_Expenses_Purchase_LC(j)
  				$pr_Data2['EP_'.$pr_CY] = $pr_Data2['EP_'.$pr_CY] + ($pr_AMT[$pr_i] * $pr_PRC[$pr_i]);// Expenses_Purchase(C,j)
  				//$pr_Data2['RSC_'.$pr_client.'_'.$pr_CY] = $pr_Data['RSC_'.$pr_client.'_'.$pr_CY] + ($pr_AMT[$pr_i] * $pr_PRC[$pr_i]);// Expenses_purchase_Client(d,C,j)
  			}
  			$this->finplan->bad->add($pr_Data);
  		}
  		$pr_Data2['sid'] = 1;
  		$this->finplan->cgd->add($pr_Data2);
  	}
    return $pr_Data2;
  }
  //Note:: find a case study where this is not null

  /**
   * Calculate Old Loans
   **/
  public function updateOldLoans()
  {
    $ol_Data = Array();//initialise
  	$ol_bvData = $this->finplan->bvd->getByField('1','sid');
  	$ol_boData = $this->finplan->bod->getByField('1','sid');
  	$ol_startyear = $this->finplan->startYear; // set value of start year to variable
  	$ol_Data['sid']=1; // set the sid to 1 for storing values
  	$ol_excData = $inv_excData;
  	$ol_prevyear = $ol_startyear-1;
  	for($ol_c = 0; $ol_c < count($this->finplan->allChunks); $ol_c++){// loop to get each currency id
  		$ol_CX = $this->finplan->allChunks[$ol_c];// get currency id
  		$ol_rtype = 'RateType_'.$ol_CX;
  		$ol_rattype = $ol_bvData[$ol_rtype];// get the inflation type given by user
  		$ol_oldval = 'OL_'.$ol_CX;
  		$ol_startyear1 = $ol_startyear-1;// setting year for old loans outstanding
  		$ol_oldoutval[$ol_CX.'_'.$ol_startyear1] = $ol_bvData[$ol_oldval];
  		$ol_initial['IL'] = $ol_initial['IL'] +($ol_bvData[$ol_oldval] * $ol_excData[$ol_CX.'_'.$ol_startyear]);// Init_Loan
  		$ol_initial['IBL'] = $ol_initial['IBL'] +($ol_bvData[$ol_oldval] * $ol_excData[$ol_CX.'_'.$ol_prevyear]);// Init_Loan
  		for($ol_i=$ol_startyear;$ol_i <= $this->finplan->endYear; $ol_i++){
  			$ol_CY = $ol_CX.'_'.$ol_i;
  			$ol_lon = 'L_'.$ol_CY;
  			$ol_rep = 'R_'.$ol_CY;
  			$ol_g = $ol_i-1;
  			$ol_CYY = $ol_CX.'_'.$ol_g;
  			$ol_lonval[$ol_CY] = $ol_bvData[$ol_lon];
  			$ol_repval[$ol_CY] = $ol_bvData[$ol_rep];
  			$ol_oldoutval[$ol_CY] = $ol_oldoutval[$ol_CYY] + $ol_lonval[$ol_CY] - $ol_repval[$ol_CY];
  			if($ol_rattype =="SR"){ //if average rate
  				$ol_int = 'Avg_'.$ol_CX;
  				$ol_intr = $ol_bvData[$ol_int];
  				$ol_intval[$ol_CY] = ($ol_intr/100) * $ol_oldoutval[$ol_CYY];
  			}elseif($ol_rattype =="YR"){ // if Yearly interest payment
  				$ol_intp = XmlKey::KEY_I.'_'.$ol_CY;
  				$ol_intval[$ol_CY] = $ol_bvData[$ol_intp];
  			}
  			$ol_exeouts[$ol_CY] = $ol_oldoutval[$ol_CY] * $ol_excData[$ol_CY];
  			$ol_exeint[$ol_CY] = $ol_intval[$ol_CY] * $ol_excData[$ol_CY];
  			$ol_exerep[$ol_CY] = $ol_repval[$ol_CY] * $ol_excData[$ol_CY];
  			$ol_exerepn[$ol_CY] = $ol_repval[$ol_CY] * $ol_excData[$ol_CYY];
  			$ol_exelon[$ol_CY] = $ol_lonval[$ol_CY] * $ol_excData[$ol_CY];
  			$ol_Data['LN_'.$ol_CY] = $ol_lonval[$ol_CY];//Old_Loans(c,j)
  			$ol_Data['LNL_'.$ol_i] = $ol_Data['LNL_'.$ol_i] + $ol_exelon[$ol_CY];//Old_Loans_LC

  			$ol_Data['L_'.$ol_CY] = $ol_oldoutval[$ol_CY];//Old_Loans_outstand(c,j)
  			$ol_Data['LL_'.$ol_i] = $ol_Data['LL_'.$ol_i] + $ol_exeouts[$ol_CY];//Old_Loans_Outstand_LC

  			$ol_Data[XmlKey::KEY_I.'_'.$ol_CY] = $ol_intval[$ol_CY];//Old_Loans_Int ( c,j)
  			$ol_Data['IL_'.$ol_i] = $ol_Data['IL_'.$ol_i] + $ol_exeint[$ol_CY];//Old_Loans_Int_LC

  			$ol_Data['R_'.$ol_CY] = $ol_repval[$ol_CY];//Old_Loans_Repay
  			$ol_Data['RL_'.$ol_i] = $ol_Data['RL_'.$ol_i] + $ol_exerep[$ol_CY];//Old_Loans_Repay_LC
  			$ol_Data['RLN_'.$ol_i] = $ol_Data['RLN_'.$ol_i] + $ol_exerepn[$ol_CY];//Old_Loans_Repay_LC for current maturity
  		}
  	}
  	$ol_Data['IL'] = $ol_initial['IL'] + $ol_boData['SLInitial'];//Init_Loans_LC
  	$ol_Data['ILO'] = $ol_Data['IL'] - $ol_Data['RL_'.$ol_startyear];//Init_Net_loans_outstand= Init_Loans_LC - Old_Loans_Repay_LC(First_year)
  	//$ol_Data['IBL'] = $ol_initial['IBL'];      //// this is changed to the line below to correct the loan outstaning in the initial balance sheet

  	$ol_Data['IBL'] = $ol_Data['ILO'];

  	$this->finplan->cad->add($ol_Data);
    return $ol_Data;
  }
  //Note:: find a case study where this is not null

  /**
   * Calculate New Loans
   **/
  public function updateNewCommercialLoans()
  {
    $data = array('sid' => 1);
    $loans = $this->finplan->lns->getByField('1','sid');
    $inflation = $this->finplan->add->getByField('1','sid');
    $exchange = $this->finplan->acd->getByField('1','sid');

    foreach($this->finplan->allChunks as $currency) {
      // start fresh for each curncy
      $balance = array();
      $interest = array();
      $repayment = array();
      $term = $loans['T_'.$currency];
      $interest_rate = $loans['S_'.$currency]/100;
      for($year = $this->finplan->startYear; $year <= $this->finplan->endYear; ++$year) {
        $drawdown = $loans['A_'.$currency.'_'.$year];
        $inflation_rate = $inflation[XmlKey::KEY_I.'_'.$currency.'_'.$year]/100;
        if ($drawdown) {
  	       for($y = $year + 1; $y <= $year + $term; ++$y) {
             $repayment[$y] += $drawdown / $term;
           }
        }
        $data['B_'.$currency.'_'.$year] = $balance[$year] = $balance[$year-1] - $repayment[$year] + $drawdown;
        $data[XmlKey::KEY_I.'_'.$currency.'_'.$year] = $interest[$year] = $balance[$year-1] * ($interest_rate + $inflation_rate);
        $data['R_'.$currency.'_'.$year] = $repayment[$year];
        $data['M_'.$currency.'_'.$year] = $maturity[$year] = $repayment[$year+1];
        $data['O_'.$currency.'_'.$year] = $outstanding[$year] = $balance[$year] - $maturity[$year];
        $exchange_rate = $exchange[$currency.'_'.$year];
        $data['TD_'.$year] += $drawdown * $exchange_rate;
        $data['TB_'.$year] += $balance[$year] * $exchange_rate;
        $data['TI_'.$year] += $interest[$year] * $exchange_rate;
        $data['TR_'.$year] += $repayment[$year] * $exchange_rate;
        $data['TM_'.$year] += $maturity[$year] * $exchange_rate;
        $data['TO_'.$year] += $outstanding[$year] * $exchange_rate;
      }
    }
    $this->finplan->cln->add($data);
  }
  //Note:: find a case study where this is not null

}

?>
