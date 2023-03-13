<?php

class BalanceLoan extends FinplanService {

  public $finplan;
  public $tex_data;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
    $tex_Data= array();
    $tex_Data['sid'] = 1;//set SID for storing Data
  }

  /**
   * UPDATE V.K. 20151105 IDC add and exclude financing sorces which are not included
   **/
  public function updateBalanceExport()
  {
    $bal_Data = Array();
    $bal_Data2 = Array();
  	$bal_cpData = $this->finplan->getPlantData();//get Data for all plants
  	$bal_infData = $this->finplan->getInflationData();  //get inflation data

  	if (is_array($bal_cpData) && count($bal_cpData) > 0) {//Check if plant Data exists
      foreach($bal_cpData as $bal_row) {//froeach plant
        
        $bal_Data = Array(); //intialise the Data set
  			$bal_pid = $bal_row['id'];// pass plant id value    [1]
  			$bal_Data['pid'] = $bal_pid;//assign pass plant id value to pid

        for($bal_c = 0; $bal_c < count($this->finplan->curChunks); $bal_c++) {// for each currency
          $bal_fidval = $this->finplan->curChunks[$bal_c].'_'.$bal_pid;//assign currency_pid to variable  [USD_1]
  				$bal_cdData = $this->finplan->abd->getByField($bal_fidval,'fid');// get all values from drawdown using fid field
          $bal_CX = $this->finplan->curChunks[$bal_c];//USD

          foreach($this->finplan->financeSources as $financesource){// for each finance source
            if($financesource['type'] == 'E') {
              $bal_FS = $financesource['id'];   //E1
  						$bal_cperiod = $bal_row['FOyear']-$bal_row['CPeriod'];//calculating first year of construction
  						$bal_repval = $bal_fidval.'_'.$bal_FS;// currency_pid_financeid   [USD_1_E1]
  						$bal_trmData = $this->finplan->aud->getByField($bal_repval,'fid');
  						$bal_graceval = 0;// extracting grace period for this finance source
  						$bal_maturval = $bal_trmData['MaturityTime'];// extracting maturity time for this finance source
  						$bal_totalfinance = $bal_cdData['Tot_'.$bal_FS];//extract total  investment for this plant and finance source //210
              $bal_repstart = $bal_row['FOyear'];// first year of construction + 1 + Grace period
              $bal_lastpaymentyear = $bal_repstart + $bal_maturval - 1;// Last year of repayment

              if ($bal_totalfinance != 0) {//if Tot_E2 tj izvor finansiranja iz $bal_cdData onda ne radi nista jer EC2 i PL nisu ukljuceni
                for($bal_i=$this->finplan->startYear; $bal_i <= $this->finplan->endYear; $bal_i++) {//start loop from first year of study
                  if($bal_trmData['RepaymentOption'] == 'UI') {//repayment rate option fo export credit
                    $bal_reprate = $bal_totalfinance/$bal_trmData['MaturityTime'];//Repay_Rate = TotalExport/MaturityTime       for uniform installment of principle amount
                  } elseif($bal_trmData['RepaymentOption'] == 'ST') {

                    $pmtInterest = $bal_trmData['InterestRate']/100;
                    if ($bal_trmData['InterestOption'] == 'F') {
                      $pmtInterest = ($bal_trmData['InterestSpreadRate'] + $bal_infData['I_'.$bal_CX.'_'.$bal_i])/100;
                    }
                    $bal_reprat = $this->finplan->fpmt->PMT($pmtInterest, $bal_trmData['MaturityTime'], -1,0);
                    //$bal_reprat = $this->finplan->fpmt->PMT($bal_trmData[$trm_key]/100, $bal_trmData['MaturityTime'], -1,0);//PMT(interest rate , MaturityTime, -1,0)  for steady repayment of P+I

                    $bal_reprate = $bal_reprat * $bal_totalfinance; //UFI
                  }

              
                  if($bal_i >= $bal_cperiod and  $bal_i <= $bal_lastpaymentyear ) {
                    if($bal_trmData['InterestOption'] == 'C') {
                      $bal_intrate[$bal_i] = $bal_trmData['InterestRate'];//constant
                    } elseif($bal_trmData['InterestOption'] == 'F') {
                      $bal_intrate[$bal_i] = $bal_infData['I_'.$bal_CX.'_'.$bal_i] + $bal_trmData['InterestSpreadRate'];// inf_rate+ Int_spread
                    }
                    //intrest caluculation on export credit
                    //expi ECI export credit interest for given year

                    
                    //$bal_expi[$bal_i] = $bal_expb[$bal_i-1] * ($bal_intrate[$bal_i]/100);
                    //da bi racunao blance tacno za vise elektrana v.k. 30.11.2017
                    $bal_expi[$bal_i] = $bal_expb[$bal_pid."_".($bal_i-1)] * ($bal_intrate[$bal_i]/100);
                    
                    //repayment on export credit ECR
                    $bal_yr = $bal_i -$bal_cperiod + 1;// current year-first year + 1
                    $bal_fsvalue = $bal_FS.'_'.$bal_yr;//financeId_yearNo         [E1_1]
                    $bal_ddvalue = $bal_cdData[$bal_fsvalue];//get value for drawdown for this study year  [56]
                    $bal_repval = 'Repy_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i.'_'.$bal_pid;     //[Repy_USD_E1_2012_1]
                    //echo '<br>';


                    //Repayment options
                    if($bal_i >= $bal_repstart and  $bal_i <= $bal_lastpaymentyear ) {
                      if($bal_trmData['RepaymentOption'] == 'UI') {//repayment  if Uniform installment
                        $bal_exprepi[$bal_repval] = $bal_reprate;
                      } elseif($bal_trmData['RepaymentOption'] == 'ST') {//repayment  if Steady installment
                        $bal_exprepi[$bal_repval] = $bal_reprate - $bal_expi[$bal_i-$bal_graceval];//repayment = UFI-Intrate
                       // echo 'PMT = '.$bal_reprate.', ';
                        //echo 'IntRate = '.$bal_expi[$bal_i-$bal_graceval].'<br>';
                      }
                      $bal_Data[$bal_repval] = $bal_exprepi[$bal_repval];
                    }




                    //Export_Credit1_Balance(f, p,j)= Export_Credit1_Balance(f, p,j-1) + Export_Credit1(f, p,j) - Export_Credit1_Repay(f, p,j).
                   // $bal_expb[$bal_i] = $bal_expb[$bal_i-1]	+ $bal_ddvalue - $bal_exprepi[$bal_repval];//

                    //da bi racuna tacno balance za vise elektrana potrebno dodati ID planta pored godine
                    $bal_expb[$bal_pid."_".$bal_i] = $bal_expb[$bal_pid."_".($bal_i-1)]	+ $bal_ddvalue - $bal_exprepi[$bal_repval];
                 
                    //echo "plant " . $bal_pid. "&nbsp;&nbsp;Year " .$bal_i."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; balance " .$bal_expb[$bal_pid."_".($bal_i-1)] .  "dd value " .$bal_ddvalue.  "                   exprepi " .$bal_exprepi[$bal_repval].  "<br>";

                    // Create Field Names for Balance,Repayment and Interest
                    $bal_balval = 'Bal_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_intrval = 'Int_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;


                    //Parsing  Balance ,Interest,Repayment in Data
                    //$bal_Data[$bal_balval] = $bal_expb[$bal_i];
                    //da bi racuna tacno balance za vise elektrana potrebno dodati ID planta pored godine
                    $bal_Data[$bal_balval] = $bal_expb[$bal_pid."_".$bal_i];

                    $bal_Data[$bal_intrval] = $bal_expi[$bal_i];
                    if($bal_trmData['PDrawdown'] == 'YES') {
                      $bal_DD[$bal_i] = $bal_ddvalue * ($bal_trmData['PDRate']/100);
                    }


                    //ovaj dio ne radi
                    if($bal_trmData['IDCOption'] == 'Y') {
                      //13.10 vedran & Irej IDC calc
                      if($bal_i<$bal_row['FOyear']) {
                        $idc_loan_dd[$bal_CX.'_'.$bal_FS.'_'.$bal_i] =  $bal_expi[$bal_i] * $bal_trmData['IDCLoan']/100;
                        $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.($bal_i-1)]+ $idc_loan_dd[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                        $idc_loan_int[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.($bal_i-1)] * $bal_trmData['IDCRate']/100;
                        $idc_loan_rep[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = 0;
                        $total_idc_loan = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                      } else {
                        if($bal_trmData['RepaymentOption'] == 'UI') {
                          $idc_loan_rep[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $total_idc_loan/ $bal_trmData['IDCTerm'];
                        } elseif($bal_trmData['RepaymentOption'] == 'ST') {
                          $bal_reprat_idc = $this->finplan->fpmt->PMT($bal_trmData['IDCRate']/100, $bal_trmData['IDCTerm'], -1,0);//PMT(interest rate , MaturityTime, -1,0)  for steady repayment of P+I
                          $idc_loan_rep[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $bal_reprat_idc * $total_idc_loan-$idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.($bal_i-1)]*$bal_trmData['IDCRate']/100; //UFI
                        }
                        $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.($bal_i-1)]-$idc_loan_rep[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                        $idc_loan_int[$bal_CX.'_'.$bal_FS.'_'.$bal_i] = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.($bal_i-1)] * $bal_trmData['IDCRate']/100;
                      }
                      $bal_idcdd = 'IDC_DD_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_idcb = 'IDC_B_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_idci = 'IDC_I_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_idcr = 'IDC_R_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_Data[$bal_idcdd] = $idc_loan_dd[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                      $bal_Data[$bal_idcb] = $idc_loan_bal[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                      $bal_Data[$bal_idci] = $idc_loan_int[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                      $bal_Data[$bal_idcr] = $idc_loan_rep[$bal_CX.'_'.$bal_FS.'_'.$bal_i];
                    }




                    if($bal_trmData['PDrawdown'] == 'YES') { // Export_Credit_DD
                      $bal_dfval = 'DF_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_Data[$bal_dfval] = $bal_DD[$bal_i];
                      $bal_totdfval = 'TotDF_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                      $bal_Data[$bal_totdfval] = $bal_Data[$bal_totdfval]+$bal_DD[$bal_i];
                    }
                    // EC DD value in final array
                    $bal_ddval = 'DD_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_Data[$bal_ddval] = $bal_ddvalue;
                    // kalkulacija totalnog interest i repayment zbirno po godinama
                    // calculate for debit to show export credits by currency
                    $bal_repval2 = 'Repy_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_Data2[$bal_ddval] = $bal_Data2[$bal_ddval] + $bal_ddvalue;

                    //$bal_Data2[$bal_balval] = $bal_Data2[$bal_balval] + $bal_expb[$bal_i];
                    //da bi racuna tacno balance za vise elektrana potrebno dodati ID planta pored godine
                    $bal_Data2[$bal_balval] = $bal_Data2[$bal_balval] + $bal_expb[$bal_pid."_".$bal_i];
                    
                    $bal_Data2[$bal_intrval] = $bal_Data2[$bal_intrval] + $bal_expi[$bal_i];
                    $bal_Data2[$bal_repval2] = $bal_Data2[$bal_repval2] + $bal_exprepi[$bal_repval];

                    // Create Field Names for Balance,Repayment and Interest
                    $bal_idcddval = 'IDD_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_idcbalval = 'IB_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_idcrepval = 'IR_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    $bal_idcintrval = 'II_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                    // Parsing  Balance ,Interest,Repayment in Data
                    $bal_Data[$bal_idcddval] = $bal_Data[$bal_ddval] + $bal_Data[$bal_idcdd];
                    $bal_Data[$bal_idcbalval] = $bal_Data[$bal_idcb] + $bal_Data[$bal_balval];
                    $bal_Data[$bal_idcrepval] = $bal_Data[$bal_idcr] +$bal_Data[$bal_repval];
                    $bal_Data[$bal_idcintrval] = $bal_Data[$bal_idci] + $bal_Data[$bal_intrval];
                  }
                }//kraj za godine


                //various fees related to export credit drowndown, commitment fee etc..
                if($bal_trmData['DUpfront'] == 'YES') {
                  $bal_totufval = 'TotUF_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                  $bal_ufval = 'UF_'.$bal_CX.'_'.$bal_FS.'_'.$bal_cperiod;
                  $bal_Data[$bal_ufval] = $bal_totalfinance * ($bal_trmData['DURate']/100); //Export_Credit1_UF
                  $bal_Data[$bal_totufval] = $bal_Data[$bal_totufval]+ $bal_Data[$bal_ufval];
                }
                if($bal_trmData['OTInitial'] == 'YES') {
                  $bal_otval = 'OT_'.$bal_CX.'_'.$bal_FS.'_'.$bal_cperiod;
                  $bal_tototval = 'TotOT_'.$bal_CX.'_'.$bal_FS.'_'.$bal_i;
                  $bal_Data[$bal_otval] = $bal_totalfinance * ($bal_trmData['DURate']/100); //Export_Credit1_Fee_OTI
                  $bal_Data[$bal_tototval] = $bal_Data[$bal_otval]+($bal_totalfinance * ($bal_trmData['DURate']/100)); //Total_Export_Credit1_fee_OTI
                }
              }
            }
          }
        }
         $this->finplan->avd->add($bal_Data);  //promijenio jer su se u intermediate resultu javljali vise
      }
  		$bal_Data2['sid'] = 1;
  		$this->finplan->bqd->add($bal_Data2);
    }
    return $bal_Data;
  }

  /**
   * Calculate Total Export Credit
   **/
  public function updateExportCredit()
  {
    $tex_data = Array();//initialise
    $tex_Data['sid'] = 1;//set SID for storing Data
    $tex_cpData = $this->finplan->getPlantData();// get all Data for plants
    $tex_excData = $this->finplan->getExchangeData();// get exch Data

    foreach($tex_cpData as $tex_row) {
      $tex_CP = $tex_CX.'_'.$tex_row['id'];
      $tex_bdData = $this->finplan->avd->getByField($tex_row['id'],'pid');// get values from cal_balance
      $tex_pid = $tex_row['id'];
      foreach($this->finplan->financeSources as $financesource) {
        if($financesource['type'] == 'E') {
          $tex_FS = $financesource['id'];  //E1
          for($tex_c = 0; $tex_c < count($this->finplan->allChunks); $tex_c++) {
            $tex_CX = $this->finplan->allChunks[$tex_c];  //currency
            for($tex_i=$this->finplan->startYear;$tex_i <= $this->finplan->endYear; $tex_i++) {// for each study year
              $tex_CY = $tex_CX.'_'.$tex_i;
              $tex_ii= $tex_i-1;
              $tex_CYY = $tex_CX.'_'.$tex_ii;
              $tex_idcintrval = 'II_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;//for IDC interest
              $tex_idcrepval = 'IR_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;//for IDC repay
              $tex_idcbalval = 'IB_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;//for IDC balance
              $tex_ddval = 'IDD_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;//for export credit
              $tex_repval = 'IR_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;//.'_'.$tex_pid;// for export credit repay
              $tex_balval = 'IB_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;// for export credit Balance
              $tex_intrval = 'II_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;
              $tex_tec = 'L_'.$tex_CY;//Tot_Export_Credits(f,j)
              $tex_tecr = 'R_'.$tex_CY;//Tot_Export_Credits_Repay(f,j)
              $tex_tecb = 'B_'.$tex_CY;//Tot_Export_Credits_Balance(f,j)
              $tex_teci = XmlKey::KEY_I.'_'.$tex_CY;//Tot_Export_Credits_Interest(f,j)
              $tex_totufval = 'TotUF_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;
              $tex_tototval = 'TotOT_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;
              $tex_totdfval = 'TotDF_'.$tex_CX.'_'.$tex_FS.'_'.$tex_i;
              //calculate Tot_Export_Credits(f,j),Tot_Export_Credits_Repay(f,j),Tot_Export_Credits_Balance(f,j),Tot_Export_Credits_Interest(f,j)
              $tex_Data[$tex_tec]  = $tex_Data[$tex_tec] + $tex_bdData[$tex_ddval];//exportcredit
              $tex_Data[$tex_tecr] = $tex_Data[$tex_tecr] + $tex_bdData[$tex_repval];//exportcredit_repay
              $tex_Data[$tex_tecb] = $tex_Data[$tex_tecb] + $tex_bdData[$tex_balval];//exportcredit_bal
              $tex_Data[$tex_teci] = $tex_Data[$tex_teci] + $tex_bdData[$tex_intrval];//exportcredit_int
              $tex_teclc[$tex_i] = $tex_bdData[$tex_ddval] * $tex_excData[$tex_CY];
              $tex_tecrlc[$tex_i] = $tex_bdData[$tex_repval]  * $tex_excData[$tex_CY];
              $tex_tecrlcn[$tex_i] = $tex_bdData[$tex_repval]  * $tex_excData[$tex_CYY];
              $tex_tecblc[$tex_i] = $tex_bdData[$tex_balval] * $tex_excData[$tex_CY];
              $tex_tecilc[$tex_i] = $tex_bdData[$tex_intrval]  * $tex_excData[$tex_CY];
              $tex_tecuflc[$tex_i] = $tex_bdData[$tex_totufval] * $tex_excData[$tex_CY];
              $tex_tecotlc[$tex_i] = $tex_bdData[$tex_tototval] * $tex_excData[$tex_CY];
              $tex_tecdflc[$tex_i] = $tex_bdData[$tex_totdfval] * $tex_excData[$tex_CY];
  						$tex_Data['LLC_'.$tex_i] = $tex_Data['LLC_'.$tex_i] + $tex_teclc[$tex_i];// Tot_Export_Credit_LC
              $tex_Data['RLC_'.$tex_i] = $tex_Data['RLC_'.$tex_i] + $tex_tecrlc[$tex_i];// Tot_Export_Credit_Repay_LC
              $tex_Data['RLCN_'.$tex_i] = $tex_Data['RLCN_'.$tex_i] + $tex_tecrlcn[$tex_i];// Tot_Export_Credit_Repay_LC for current maturity
              $tex_Data['BLC_'.$tex_i] = $tex_Data['BLC_'.$tex_i] + $tex_tecblc[$tex_i];// Tot_Export_Credits_Balance_LC
              $tex_Data['ILC_'.$tex_i] = $tex_Data['ILC_'.$tex_i] + $tex_tecilc[$tex_i];// Tot_Export_Credit_Int_LC
              //Export credit1 and export credit2  LC
              $tex_Data['LLC_'.$tex_FS.'_'.$tex_i] = $tex_Data['LLC_'.$tex_FS.'_'.$tex_i] + $tex_teclc[$tex_i];// Tot_Export_Credit_LC
              $tex_Data['RLC_'.$tex_FS.'_'.$tex_i] = $tex_Data['RLC_'.$tex_FS.'_'.$tex_i] + $tex_tecrlc[$tex_i];// Tot_Export_Credit_Repay_LC
              $tex_Data['BLC_'.$tex_FS.'_'.$tex_i] = $tex_Data['BLC_'.$tex_FS.'_'.$tex_i] + $tex_tecblc[$tex_i];// Tot_Export_Credits_Balance_LC
              $tex_Data['ILC_'.$tex_FS.'_'.$tex_i] = $tex_Data['ILC_'.$tex_FS.'_'.$tex_i] + $tex_tecilc[$tex_i];// Tot_Export_Credit_Int_LC
              $tex_Data['OTLC_'.$tex_i] = $tex_Data['OTLC_'.$tex_i] + $tex_tecotlc[$tex_i];// Tot_Export_Credit_FEE_OTI_LC
              $tex_Data['DFLC_'.$tex_i] = $tex_Data['DFLC_'.$tex_i] + $tex_tecdflc[$tex_i];// Tot_Export_Credit_FEE_DD_LC
              $tex_Data['UFLC_'.$tex_i] = $tex_Data['UFLC_'.$tex_i] + $tex_tecuflc[$tex_i];// Tot_Export_Credit_FEE_UF_LC
              //Tot_export_Credit_fee(f,j)
              $tex_Data['TFEE_'.$tex_CY] = $tex_Data['TFEE_'.$tex_CY] + $tex_bdData[$tex_totufval] + $tex_bdData[$tex_tototval] +$tex_bdData[$tex_totdfval];
              $tex_Data['FLC_'.$tex_i] = $tex_Data['FLC_'.$tex_i] + $tex_tecotlc[$tex_i] + $tex_tecdflc[$tex_i] + $tex_tecuflc[$tex_i];//Tot_export_Credit_fee_lc(j)
            }
          }
        }
      }
    }
    $this->finplan->chd->add($tex_Data);
    return $tex_Data;
  }

  /**
   * Calculate Balance for Commercial Loan
   **/
  public function updateBalanceLoan()
  {
    $col_Data = Array();//initialise
    $col_cpData = $this->finplan->getPlantData();//get Data for all plants
    $col_infData = $this->finplan->getInflationData();//get inflation

    if (is_array($col_cpData) && count($col_cpData) > 0) { //Check if plant Data exists
      foreach($col_cpData as $col_row) {
        $col_Data = Array(); //intialise the Data set
        $col_pid = $col_row['id'];// pass plant id value
        $col_Data['pid'] = $col_pid;//assign pass plant id value to pid
        for($col_c = 0; $col_c < count($this->finplan->allChunks); $col_c++) { // for each currency
          $col_fidval = $this->finplan->allChunks[$col_c].'_'.$col_pid;//assign currency_pid to variable
          $col_cdData = $this->finplan->abd->getByField($col_fidval,'fid');// get all values from drawndown using fid field
          $col_CX = $this->finplan->allChunks[$col_c];

          foreach($this->finplan->financeSources as $financesource) {// for each finance source
            if($financesource['type'] == 'C') {
              $col_FS = $financesource['id'];
              $col_cperiod = $col_row['FOyear']-$col_row['CPeriod'];//calculating first year of construction
              $col_repval = $col_fidval.'_'.$col_FS;// currency_pid_financeid
              $col_reprate = $col_carData[$col_repval];// get repay rate for currency_pid_financeid
              $col_trmData = $this->finplan->aud->getByField($col_repval,'fid');
              $col_intval = $col_trmData['InterestRate'];// extracting interest rate for this finance source
              $col_maturval = $col_trmData['MaturityTime'];// extracting maturity time for this finance source
              $col_totalfinance = $col_cdData['Tot_'.$col_FS];//extract total  investment for this plant and finance source
              $col_totbalval = 'TotBal_'.$col_CX.'_'.$col_FS;//assigning field names  for storing values
              $col_totrepval = 'TotREP_'.$col_CX.'_'.$col_FS;
              $col_totintrval = 'TotInt_'.$col_CX.'_'.$col_FS;
              $col_Data[$col_totrepval] = 0;	//making sure that field values are set to zero
              $col_Data[$col_totintrval] = 0;
              $col_repstart = $col_cperiod + 1 ;// first year of construction + 1 + Grace period
              for($col_i=$this->finplan->startYear;$col_i <= $this->finplan->endYear; $col_i++) { //start loop from first year of study
                // if finance source is other than Bond & Equity
                $col_k = $col_i+1;
                $col_lstyear = $col_cperiod +$col_maturval;
                if ($col_i<$col_cperiod) {// if study year is less than first year of construction
                  $col_expb[$col_i] = 0;
                } elseif ($col_i >= $col_cperiod  and  $col_i <= $col_lstyear) {//if study year is greater than 1st yr of construction and less than 'first year of construction + 1 + Grace period'
                  $col_yr = $col_i-$col_cperiod+1;// current year-first year + 1
                  $col_fsvalue = $col_FS.'_'.$col_yr;//financeId_yearNo
                  $col_ddvalue = $col_cdData[$col_fsvalue];//get value for drawdown for this study year
                  if ($col_ddvalue > 0) { // if DDown exist for this year ,then calculate repayment for this DD over maturity period
                    for($col_j=0;$col_j < $col_maturval; $col_j++) {// set repayment for each maturity year
                      $col_m= $col_k + $col_j;// 'Studyyear+1'+ year of return
                      $col_repyval = 'Repy_'.$col_CX.'_'.$col_FS.'_'.$col_m.'_'.$col_pid;
                      $col_exprepi[$col_repyval] = $col_exprepi[$col_repyval] + $col_ddvalue/$col_trmData['MaturityTime'];
                    }
                  } else {	// if DDown does not exist for this year ,this will set repayment
                    //$col_repyval = 'Repy_'.$col_CX.'_'.$col_FS.'_'.$col_i.'_'.$col_pid;
                    //$col_exprepi[$col_repyval] = $col_exprepi[$col_repyval];
                  }
                  $col_repyval = 'Repy_'.$col_CX.'_'.$col_FS.'_'.$col_i.'_'.$col_pid;
                }
              }
              for ($col_i=$this->finplan->startYear;$col_i <= $this->finplan->endYear; $col_i++) { //start loop from first year of study
                // if finance source is other than Bond & Equity
                $col_k = $col_i+1;
                if ($col_trmData['InterestOption'] == 'C') {
                  $col_intrate[$col_i] = $col_trmData['InterestRate'];//constant
                } elseif ($col_trmData['InterestOption'] == 'F') {
                  $col_intrate[$col_i] = $col_infData[XmlKey::KEY_I.'_'.$col_CX.'_'.$col_i] + $col_trmData['InterestSpreadRate'];// inf_rate+ Int_spread
                }
                $col_expi[$col_i] = $col_expb[$col_i-1] * ($col_intrate[$col_i]/100);// calculating Interest
                $col_lstyear = $col_cperiod +$col_maturval;
                $col_yr = $col_i-$col_cperiod+1;// current year-first year + 1
                $col_fsvalue = $col_FS.'_'.$col_yr;//financeId_yearNo
                $col_ddvalue = $col_cdData[$col_fsvalue];//get value for drawdown for this study year
                $col_repyval = 'Repy_'.$col_CX.'_'.$col_FS.'_'.$col_i.'_'.$col_pid;
                if($col_trmData['PDrawdown'] == 'YES') {
                  $col_DD[$col_i] = $col_ddvalue * ($col_trmData['PDRate']/100);
                }
                $col_expb[$col_i] = $col_expb[$col_i-1] + $col_ddvalue - $col_exprepi[$col_repyval];//
                // Create Field Names for Balance,Repayment and Interest,DD
                $col_im = $col_i-1;
                $col_balval = 'Bal_'.$col_CX.'_'.$col_FS.'_'.$col_i;
                $col_balval2 = 'Bal_'.$col_CX.'_'.$col_FS.'_'.$col_im;
                $col_repval = 'Repy_'.$col_CX.'_'.$col_FS.'_'.$col_i.'_'.$col_pid;
                $col_intrval = 'Int_'.$col_CX.'_'.$col_FS.'_'.$col_i;
                $col_ddval = 'DD_'.$col_CX.'_'.$col_FS.'_'.$col_i;
                if ($col_expb[$col_i] < 0.001) {
                  $col_expb[$col_i] = 0;}
                  //Parsing  Balance ,Interest,Repayment in Data
                  $col_Data[$col_balval] = $col_expb[$col_i];
                  $col_Data[$col_intrval] = $col_expi[$col_i];
                  $col_Data[$col_repval] = $col_exprepi[$col_repval];
                  $col_Data[$col_ddval] = $col_ddvalue;
                  //Calculate Total  Repayment,  Balance and Interest
                  $col_Data[$col_totrepval] = $col_Data[$col_totrepval]+$col_exprepi[$col_repval];
                  $col_Data[$col_totintrval] = $col_Data[$col_totintrval]+$col_expi[$col_i];
                }
              }
            }
          }
          $this->finplan->afd->add($col_Data);
        }
      }
    }
    //Note:: find a case study where this is not null
  }

?>
