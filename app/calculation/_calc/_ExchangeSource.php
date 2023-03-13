<?php

class ExchangeSource extends FinplanService {

  public $finplan;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;
  }

  /**
   * Calculate Foreign Exchange
   **/
  public function updateForeignExchange($bo_CY)
  {
  	$fe_chData = $this->finplan->chd->getByField('1','sid');//cal_totalexport
  	$fe_ciData = $this->finplan->cid->getByField('1','sid');//cal_totalcommloan
  	$fe_caData = $this->finplan->cad->getByField('1','sid');//cal_oldloans
  	$fe_cbData = $this->finplan->cbd->getByField('1','sid');//cal_oldBonds
  	$fe_ccData = $this->finplan->ccd->getByField('1','sid');//cal_Bonds
  	$fe_cdData = $this->finplan->cdd->getByField('1','sid');//cal_Equity
    $fe_cjData = $this->finplan->cjd->getByField('1','sid');
  	$fe_bdData = $this->finplan->getTotalOmCost();
  	$fe_beData = $this->finplan->getTotalFuelCost();
  	$fe_bmData = $this->finplan->getBonds();
  	$fe_bvData = $this->finplan->getOldLoans();
  	$fe_bxData = $this->finplan->getOldBonds();
  	$fe_bzData = $this->finplan->getTotalGeneralExpense();
  	$fe_cgData = $this->finplan->getTotalPurchase();
  	$fe_bkData = $this->finplan->getTotalSale();
  	$fe_excData = $this->finplan->getExchangeData();//Exchange Data
    $cal_loans = $this->finplan->getLoanCalculations();

    $feData = Array();
    $feData['sid'] = 1;

  	for($fe_c = 0; $fe_c < count($this->finplan->curChunks); $fe_c++){// for each currency
  		$fe_CX = $this->finplan->curChunks[$fe_c];
  		for($fe_i=$this->finplan->startYear;$fe_i <= $this->finplan->endYear; $fe_i++){// for each study year
  			$fe_CY = $fe_CX.'_'.$fe_i;//currencyid_year
  			$fe_CY1 = $fe_CX.'_'.($fe_i-1);//currencyid_lastyear
  			$fe_bval = 'B_'.$fe_CY;//Tot_Export_Credits_Balance(f,j)//New_comm_loans_Balance(f,j)//Old_Bonds_outstand(c,j) //Bonds(f,j)//Old_Bonds(f,j)
  			$fe_lval = 'L_'.$fe_CY;//Old_Loans_outstand(c,j) //Tot_Export_Credits(f,j)//New_comm_loans(f,j)//Old_Loans(f,j)
  			$fe_oval = 'O_'.$fe_CY;//Bonds_Outstand(currency name,j)
  			$fe_eval = 'E_'.$fe_CY;//Equity_Outstand(currency name,j) Tot_O&M_Cost(f,j)  //Tot_Fuel_Cost(f,j) //Tot_Gen_Exp(f,j).
  			$fe_ival = XmlKey::KEY_I.'_'.$fe_CY;//Tot_Export_Credits_Interest(f,j)  //New_comm_loans_Interest(f,j)  //Old_Loans_Int ( c,j)//Old_Bonds_Rpay(c,j)
  			$fe_nval = 'N_'.$fe_CY;//New_bonds_Return(c,j)
  			$fe_rval = 'R_'.$fe_CY;//Old_Bonds_Return(c,j) //Tot_Export_Credits_Repay(f,j)//New_comm_loans_Repay(f,j)//Bonds_Repayment (currency name,k)//Old_Loans_Repay
  			$feData['PFL_'.$fe_CY] = ($fe_chData['B_'.$fe_CY1] + $fe_ciData['B_'.$fe_CY1] + $fe_caData['L_'.$fe_CY1] + $fe_cbData['B_'.$fe_CY1] + $fe_ccData['O_'.$fe_CY1] + $fe_cdData['E_'.$fe_CY1] + $cal_loans['B_'.$fe_CY1])*($fe_excData[$fe_CY]-$fe_excData[$fe_CY1]);
  			$feData['PFLY_'.$fe_i] = $feData['PFLY_'.$fe_i] + $feData['PFL_'.$fe_CY] ;//Prov_F_Loss_LC(j)
  			$feData['O_'.$fe_CY] = $fe_chData[$fe_bval] + $fe_ciData[$fe_bval] + $fe_caData[$fe_lval] + $fe_cbData[$fe_bval] + $fe_ccData[$fe_oval] + $fe_cdData[$fe_eval];
  			$feData['OC_'.$fe_CY] = $fe_bdData[$fe_eval] + $fe_beData[$fe_eval] + $fe_bzData[$fe_eval];
  			$feData['LBI_'.$fe_CY] = $fe_chData[$fe_ival] + $fe_ciData[$fe_ival] + $fe_ccData[$fe_nval]+ $fe_caData[$fe_ival] + $fe_cbData[$fe_rval];
  			$feData['LBR_'.$fe_CY] = $fe_chData[$fe_rval] + $fe_ciData[$fe_rval] + $fe_ccData[$fe_rval]+ $fe_caData[$fe_rval] + $fe_cbData[$fe_ival];
  			$fe_rsval = 'RS_'.$bo_CY;// Revenue_Sale(C,j) WHY USE THE BONDS COUNTER HERE?
  			$fe_epval = 'EP_'.$bo_CY;// Expenses_Purchase(C,j) WHY USE THE BONDS COUNTER HERE?
  			$feData['NR_'.$fe_CY] = $fe_bkData[$fe_rsval] - $fe_cjData[$fe_epval];
  			$feData['LB_'.$fe_CY] = $fe_chData[$fe_lval] + $fe_ciData[$fe_lval] + $fe_bmData[$fe_bval]+ $fe_bvData[$fe_lval] + $fe_bxData[$fe_bval];
  		}
  	}

    return $feData;
  }


  /**
   * 2.2.15.	Sources and Application of funds --Drawdown of loans from the Stand-by facility and interests on it
   * Short-term deposits computations
   * Computation of Dividend on Equity
   **/
  public function updateSourcesFunds($sourcesData)
  {
    $fi_aaData = $this->finplan->aad->getByField('1','sid');
    $fi_acData = $this->finplan->getExchangeData();
    $fi_adData = $this->finplan->getInflationData();
    $fi_agData = $this->finplan->getTotalInvestment();
    $fi_atData = $this->finplan->atd->getByField('1','sid');
    $fi_bbData = $this->finplan->bbd->getByField('1','sid');
    $fi_bdData = $this->finplan->bdd->getByField('1','sid');
    $fi_bgData = $this->finplan->bgd->getByField('1','sid');
    $fi_boData = $this->finplan->getOtherFinancialData();
    $fi_caData = $this->finplan->cad->getByField('1','sid');//cal_oldBonds
    $fi_cbData = $this->finplan->cbd->getByField('1','sid');//cal_oldBonds
    $fi_ccData = $this->finplan->ccd->getByField('1','sid');//cal_Bonds //null reference
    $fi_cdData = $this->finplan->cdd->getByField('1','sid');//cal_Equity
    $fi_ceData = $this->finplan->ced->getByField('1','sid');
    $fi_chData = $this->finplan->chd->getByField('1','sid');//cal_totalexport
    $fi_ciData = $this->finplan->cid->getByField('1','sid');//cal_totalcommloan
    $fi_cjData = $this->finplan->cjd->getByField('1','sid'); //get total revenue Data

    // echo "fi_bdData = ";
    // print_r($fi_bdData);
    // echo "<br>";
    // echo "fi_cjData = ";
    // print_r($fi_cjData);
    // echo "<br>";
    $fi_ckData = $this->finplan->ckd->getByField('1','sid');

    $fi_bsData = $this->finplan->getContributionData();
    $fi_cmData = $this->finplan->cmd->getByField('1','sid');
    $fi_brData = $this->finplan->brd->getByField('1','sid');

    $cal_loans = $this->finplan->getLoanCalculations();

    $fi_Data = Array();
    $fi_Tot_Interest_LC = Array();
    $fi_Tot_Repay_LC = Array();
    $fi_Short_Deposits_Int_LC = Array();
    $fi_Pre_Investment_Cash_LC = Array();
    $fi_Taxable_Income_LC = Array();
    $fi_Income_Tax_LC = Array();
    $fi_Retained_Earn_LC_BD = Array();
    $fi_Retained_Earn_LC = Array();
    $fi_Short_Loans_Outstand_LC = Array();
    $fi_Cum_Retained_Earn_LC = Array(); //lol to Cum Retained Earn
    $fi_Tot_Dividend_LC = Array();
    $lastDecomYears = $this->getDecommissioningLastYears();

    $fi_Short_Deposits_Balance_LC[$this->finplan->startYear-1] = $fi_aaData['ShortTermDeposits'];
    // echo "fi_aaData = ";
    // print_r($fi_aaData);
    // echo "<br>";
    for($fi_i=$this->finplan->startYear;$fi_i <= $this->finplan->endYear; $fi_i++) {// for each study year
      $fi_ii = $fi_i-1;
      if($fi_atData['TaxType']=='SR') {
        $fi_Income_Tax_rate[$fi_i] = $fi_atData['SteadyTaxRate'];
      } else {
        if ($fi_atData['Y_'.$fi_i] =='') {
          $fi_Income_Tax_rate[$fi_i] = $fi_Income_Tax_rate[$fi_ii];
        } else {
          $fi_Income_Tax_rate[$fi_i] = $fi_atData['Y_'.$fi_i];
        }
      }

      $fi_Tot_Comm_Loans_LC[$fi_i] = $fi_ciData['LLC_'.$fi_i] + $sourcesData['ol_Data']['LNL_'.$fi_i] + $cal_loans['TD_'.$fi_i];
      $fi_Tot_Bonds_LC[$fi_i] = $fi_ccData['BLC_'.$fi_i] + $sourcesData['ob_Data']['BDL_'.$fi_i];

      if ($fi_i==$this->finplan->startYear) {
        //Short_Deposits_Int_LC(First_Year) = Init_Short_Deposit �[Inf_Rate(j)+ Spread_Short_Int_Deposit]
        $fi_Short_Deposits_Int_LC[$fi_i] = $fi_aaData['ShortTermDeposits'] * ($fi_adData[XmlKey::KEY_I.'_'.$this->finplan->baseCurrency.'_'.$fi_i] + $fi_boData['STDeposit'])/100;
        //Short_Loans_LC_Interest(First_year)= Short_Loans_Outstand_Initial �[Inf_Rate(j)+ Spread_Short_Int_Loan].
        $fi_Short_Loans_LC_Interest[$fi_i] = $fi_boData['SLInitial'] *($fi_adData[XmlKey::KEY_I.'_'.$this->finplan->baseCurrency.'_'.$fi_i] + $fi_boData['SBFacility'])/100;
        //Tot_Interest_LC(j)= Old_Loans_Int_LC(j) + Old_Bonds_Return_LC(j)+Tot_Export_Credits_Interest_LC(j) + New_Loans_Interest_LC(j)+ New_Bonds_Return_LC + Short_Loans_LC_Interest(j)
        $fi_Tot_Interest_LC[$fi_i] = $fi_caData['IL_'.$fi_i] + $fi_cbData['IL_'.$fi_i] + $fi_chData['ILC_'.$fi_i] + $fi_ciData['ILC_'.$fi_i] + $fi_ccData['NLC_'.$fi_i] + $fi_Short_Loans_LC_Interest[$fi_i];
        //Taxable_Income_LC(j) = Tot_Revenues_LC(j) + Short_Deposits_Int_LC(j)  - Tot_Optcost_LC(j) - Tot_Interest_LC(j) - Exc_Loss_LC(j) -Ann_Decom_Funds_LC(j) - Tot_Dep_LC(j) - Royalty_LC(j) - Tot_Export_Credit_Fee_LC(j)
        $fi_Taxable_Income_LC[$fi_i] = $fi_cjData['R_'.$fi_i] + $fi_Short_Deposits_Int_LC[$fi_i] - $fi_cjData['O_'.$fi_i] - $fi_Tot_Interest_LC[$fi_i] - $sourcesData['feData']['PFLY_'.$fi_i]-$fi_ceData['ADFL_'.$fi_i]-$sourcesData['dp_Data2']['T_'.$fi_i]- $fi_ckData['RLC_'.$fi_i] - $fi_chData['FLC_'.$fi_i];
        if ($fi_atData['TaxLossForward']=='Y') {
          $fi_Cum_Taxable_Income_LC[$fi_i] = $fi_aaData['RetainedEarning'] + $fi_Taxable_Income_LC[$fi_i];
          $fi_Net_Taxable_Income_LC[$fi_i] = max(0, $fi_Taxable_Income_LC[$fi_i] - $fi_atData['LossBaseYear']);
          $fi_TLCF_LC[$fi_i] = $fi_atData['LossBaseYear'] - $fi_Taxable_Income_LC[$fi_i] + $fi_Net_Taxable_Income_LC[$fi_i];
          $fi_Income_Tax_LC[$fi_i] = ($fi_Income_Tax_rate[$fi_i]/100) * max(0, $fi_Net_Taxable_Income_LC[$fi_i]);
        } else {
          $fi_Income_Tax_LC[$fi_i] = ($fi_Income_Tax_rate[$fi_i]/100) * max(0, $fi_Taxable_Income_LC[$fi_i]);
          $fi_Cum_Taxable_Income_LC[$fi_i] = $fi_Taxable_Income_LC[$fi_i];
        }
        //Retained_Earn_LC(j)= Taxable_Income_LC(j) - Income_Tax_LC(j) - Dividend_LC(j).
        $fi_Retained_Earn_LC_BD[$fi_i] = $fi_Taxable_Income_LC[$fi_i] - $fi_Income_Tax_LC[$fi_i];

        if($fi_Cum_Taxable_Income_LC[$fi_i] >0){
          //Dividend_Possible(currency name, j)= {[Taxable_Income_LC(j)-Income_Tax_LC(j)+Min(0, Cum_Retained_Earn(j))] � Equity_Share(currency,j)} / Exc_Rate(currency name,j).
          $fi_minretainearn = min(0, $fi_aaData['RetainedEarning']);
          $fi_Dividend_Possible[$fi_i]=$fi_Taxable_Income_LC[$fi_i]-$fi_Income_Tax_LC[$fi_i] + $fi_minretainearn;
          if ($fi_Dividend_Possible[$fi_i] < 0) {
            $fi_Dividend_Possible[$fi_i]=0;
          }
          //Dividend (currency name, j) = Minimum [Dividend_Due(currency name, j), Dividend_possible(currency name, j)].
          $fi_Dividend[$fi_i] =   min ($sourcesData['eq_totout'][$fi_i]  *  $sourcesData['eq_drateval']/100, $fi_Dividend_Possible[$fi_i]);
          $fi_Data['Div_'.$fi_i]=  $fi_Dividend[$fi_i];
        } else {
          $fi_Dividend[$fi_i] = 0;
          $fi_Data['Div_'.$fi_i]= 0;
        }

        $fi_Tot_Dividend_LC[$fi_i] = $fi_Dividend[$fi_i];
        $fi_Retained_Earn_LC[$fi_i] = $fi_Retained_Earn_LC_BD[$fi_i] - $fi_Tot_Dividend_LC[$fi_i];

        // Cum_Retained_Earn_LC(j) =  Init_Retained_Earnings_LC + Retained_Earn_LC(j).
        $fi_Cum_Retained_Earn_LC[$fi_i] =  $fi_aaData['RetainedEarning'] + $fi_Retained_Earn_LC[$fi_i];

        //Pre_Investment_Cash_LC (j) = Tot_Revenues_LC(j) - Tot_Optcost_LC (j) - Royalty_LC(j) - Income_Tax_LC(j)
        $fi_Pre_Investment_Cash_LC[$fi_i] = $fi_cjData['R_'.$fi_i] -$fi_cjData['O_'.$fi_i]-$fi_ckData['RLC_'.$fi_i]-$fi_Income_Tax_LC[$fi_i];

        //Funds_Arranged(j) = Pre_Investment_Cash_LC (j) + Con_Contribution_Inc_LC(j) + Con_Deposits_Inc_LC(j) + Short_Deposits_Int_LC(j) + Init_Short_Deposit + New_Equity_LC(j) + New_Bonds_LC(j) +
        //Tot_Export_Credits_LC(j) + New_Comm_Loans_LC(j).
        $fi_Funds_Arranged[$fi_i] = $fi_Pre_Investment_Cash_LC[$fi_i] + $fi_bsData['C_'.$fi_i] + $fi_bsData['D_'.$fi_i] + $fi_Short_Deposits_Int_LC[$fi_i] + $fi_aaData['ShortTermDeposits'] + $fi_cdData['N_'.$fi_i] + $fi_Tot_Bonds_LC[$fi_i] + $fi_Tot_Comm_Loans_LC[$fi_i] + $fi_chData['LLC_'.$fi_i]; /*+ $fi_chData['LLC_'.$fi_i]*/   //added   $fi_chData['LLC_'.$fi_i]   term to this eq.

        //Tot_Repay_LC(j) = Old_Bonds_Repay_LC(j)+Old_Loans_Reapy_LC(j) + Tot_Export_Credits_Repay_LC(j) + New_Comm_Loans_Repay_LC(j)+New_Bonds_Repay_LC(j)+
        $fi_Tot_Repay_LC[$fi_i] = $fi_cbData['RL_'.$fi_i] + $fi_caData['RL_'.$fi_i] + $fi_chData['RLC_'.$fi_i] + $fi_ciData['RLC_'.$fi_i]+$fi_ccData['RLC_'.$fi_i];

        //Funds_Applied(j)= Global_Invest_LC(j) + Tot_Interest_LC(j) + Tot_Repay_LC(j) + Tot_Equity_Repay_LC(j) + Tot_Dividend_LC(j) + Receivables(j-1) + VAT_TBR(j) + Tot_Decom_Costs_LC(j)?? + Tot_Export_Credit_Fee_LC(j)
        $fi_Funds_Applied[$fi_i] = $fi_agData['GIL_'.$fi_i] + $fi_Tot_Interest_LC[$fi_i] + $fi_Tot_Repay_LC[$fi_i] + $fi_cdData['E_'.$fi_i] + $fi_Tot_Dividend_LC[$fi_i] + $fi_aaData['Receivables'] + $fi_cmData['TT_'.$fi_i] + $fi_chData['FLC_'.$fi_i] + $fi_ceData['TDCL_'.$fi_i];

        //Cash_Available_LC(j) = Funds_Arranged(j) - Funds_Applied(j)
        $fi_Cash_Available_LC[$fi_i] = $fi_Funds_Arranged[$fi_i] - $fi_Funds_Applied[$fi_i];

        //Short_Loans_LC(First_year) = Max(-Cash_Available_LC(First_year), - Short_Loans_Outstanding_Initial).
        $fi_Short_Loans_LC[$fi_i] = max(-$fi_Cash_Available_LC[$fi_i], -$fi_boData['SLInitial']);
        $fi_Short_Deposits_LC[$fi_i] = $fi_Cash_Available_LC[$fi_i] + $fi_Short_Loans_LC[$fi_i] - $fi_Short_Deposits_Balance_LC[$fi_ii];
        $fi_Short_Deposits_Balance_LC[$fi_i] = $fi_Short_Deposits_LC[$fi_i] + $fi_Short_Deposits_Balance_LC[$fi_ii];

        //Short_Loans _Outstand_LC(First_year) = Short_Loans_Outstand_Initial + Short_Loans_LC(j)
        $fi_Short_Loans_Outstand_LC[$fi_i] = $fi_boData['SLInitial'] + $fi_Short_Loans_LC[$fi_i];
        $fi_Short_Loans_Repay_LC[$fi_i] =  max(0,$fi_Short_Loans_LC[$fi_i]);

        //Tot_Sources_LC(j) = Pre_Investment_Cash_LC (j)+ Con_Contribution_Inc_LC(j)+ Con_Deposits_Inc_LC(j)+ Short_Deposits_Int_LC(j)+ Short_Deposits_Balance_Initial+New_Equity_LC(j)+ New_Bonds_LC(j)
        //+ Tot_Export_Credits_LC(j)+New_Comm_Loans_LC(j) + Short_Loans_LC(j)
        $fi_Tot_Sources_LC[$fi_i] = $fi_Pre_Investment_Cash_LC[$fi_i]+ $fi_bsData['C_'.$fi_i]+ $fi_bsData['D_'.$fi_i]+ $fi_Short_Deposits_Int_LC[$fi_i] + $fi_aaData['ShortTermDeposits'] + $fi_cdData['N_'.$fi_i] + $fi_Tot_Bonds_LC[$fi_i] + $fi_Tot_Comm_Loans_LC[$fi_i] + $fi_chData['LLC_'.$fi_i] + $fi_Short_Loans_LC[$fi_i];

        // echo "Short deposits balance: ";
        // print_r($fi_Short_Deposits_Balance_LC);
        // echo "<br>, Short Deposits: ";
        // print_r($fi_Short_Deposits_LC);
        // echo "<br>";
      } else {
        //Short_Deposits_Int_LC(j) = Short_Deposits_Balance_LC(j-1)�[Inf_Rate(j)+ Spread_Short_Int_Deposit].
        $fi_Short_Deposits_Int_LC[$fi_i] = $fi_Short_Deposits_Balance_LC[$fi_ii] *($fi_adData[XmlKey::KEY_I.'_'.$this->finplan->baseCurrency.'_'.$fi_i] + $fi_boData['STDeposit'])/100;
        //Short_Loans_LC_Interest(j)= Short_Loans_Outstand_LC(j-1)*[Inf_Rate(j)+ Spread_Short_Int_Loan]
        $fi_Short_Loans_LC_Interest[$fi_i] = $fi_Short_Loans_Outstand_LC[$fi_ii] *($fi_adData[XmlKey::KEY_I.'_'.$this->finplan->baseCurrency.'_'.$fi_i] + $fi_boData['SBFacility'])/100;
        //Tot_Interest_LC(j)= Old_Loans_Int_LC(j) + Old_Bonds_Return_LC(j)+Tot_Export_Credits_Interest_LC(j) + New_Loans_Interest_LC(j)+ New_Bonds_Return_LC + Short_Loans_LC_Interest(j)
        $fi_Tot_Interest_LC[$fi_i] = $fi_caData['IL_'.$fi_i] + $fi_cbData['IL_'.$fi_i] + $fi_chData['ILC_'.$fi_i] + $fi_ciData['ILC_'.$fi_i] + $fi_ccData['ILC_'.$fi_i] + $fi_Short_Loans_LC_Interest[$fi_i] + $cal_loans['TI_'.$fi_i];
        //Taxable_Income_LC(j) = Tot_Revenues_LC(j) - Tot_Optcost_LC(j)- Tot_Dep_LC(j) - Tot_Interest_LC(j) + Short_Deposits_Int_LC(j)  - Royalty_LC(j) - Exc_Loss_LC(j) - Ann_Decom_Funds_LC(j) - Tot_Export_Credit_Fee_LC(j)
        $fi_Taxable_Income_LC[$fi_i] = $fi_cjData['R_'.$fi_i] - $fi_cjData['O_'.$fi_i] -$sourcesData['dp_Data2']['T_'.$fi_i] - $fi_Tot_Interest_LC[$fi_i] + $fi_Short_Deposits_Int_LC[$fi_i] - $fi_ckData['RLC_'.$fi_i]
        - $sourcesData['feData']['PFLY_'.$fi_i] - $fi_ceData['ADFL_'.$fi_i] - $fi_chData['FLC_'.$fi_i];
        if ($fi_atData['TaxLossForward']=='Y') {
          $fi_Cum_Taxable_Income_LC[$fi_i] = $fi_Cum_Taxable_Income_LC[$fi_ii] + $fi_Taxable_Income_LC[$fi_i];
          $fi_Net_Taxable_Income_LC[$fi_i] = max(0, $fi_Taxable_Income_LC[$fi_i] - $fi_TLCF_LC[$fi_i-1]);
          $fi_TLCF_LC[$fi_i] = $fi_TLCF_LC[$fi_i-1] - $fi_Taxable_Income_LC[$fi_i] + $fi_Net_Taxable_Income_LC[$fi_i];
          $fi_Income_Tax_LC[$fi_i] = ($fi_Income_Tax_rate[$fi_i]/100) * max(0, $fi_Net_Taxable_Income_LC[$fi_i]);
        } else {
          $fi_Income_Tax_LC[$fi_i] = ($fi_Income_Tax_rate[$fi_i]/100) * max(0, $fi_Taxable_Income_LC[$fi_i]);
          $fi_Cum_Taxable_Income_LC[$fi_i] = $fi_Taxable_Income_LC[$fi_i];
        }
        //Pre_Investment_Cash_LC (j) = Tot_Revenues_LC(j) - Tot_Optcost_LC (j) - Royalty_LC(j) - Income_Tax_LC(j).
        //$fi_Pre_Investment_Cash_LC[$fi_i] = $fi_cjData['R_'.$fi_i] - $fi_cjData['O_'.$fi_i] - $fi_ckData['RLC_'.$fi_i] - $fi_Income_Tax_LC[$fi_i];
        $fi_Pre_Investment_Cash_LC[$fi_i] = $fi_cjData['R_'.$fi_i] - $fi_cjData['O_'.$fi_i] - $fi_ckData['RLC_'.$fi_i] - $fi_Income_Tax_LC[$fi_i];
        if (array_key_exists($fi_i, $lastDecomYears)) $fi_Pre_Investment_Cash_LC[$fi_i] -= $lastDecomYears[$fi_i];

        //$fi_Pre_Investment_Cash_LC[$fi_i] = $fi_cjData['R_'.$fi_i] - $sourcesData['feData']['O_'.$fi_i] - $fi_ckData['RLC_'.$fi_i] - $fi_Income_Tax_LC[$fi_i];
        //Funds_Arranged(j) = Pre_Investment_Cash_LC (j) + Con_Contribution_Inc_LC(j) + Con_Deposits_Inc_LC(j) + Short_Deposits_Int_LC(j) + Short_Deposits_Balance_LC(j-1) + New_Equity_LC(j) +
        //New_Bonds_LC(j)  +  Tot_Export_Credits_LC(j)  +  New_Comm_Loans_LC(j),
        $fi_Funds_Arranged[$fi_i] = $fi_Pre_Investment_Cash_LC[$fi_i] + $fi_bsData['C_'.$fi_i] + $fi_bsData['D_'.$fi_i] + $fi_Short_Deposits_Int_LC[$fi_i] +  $fi_Short_Deposits_Balance_LC[$fi_ii] + $fi_cdData['N_'.$fi_i] + $fi_Tot_Bonds_LC[$fi_i] + $fi_Tot_Comm_Loans_LC[$fi_i] + $fi_chData['LLC_'.$fi_i];
        //$fi_Tot_Repay_LC = Old_Bonds_Repay_LC(j)+Old_Loans_Reapy_LC(j) + Tot_Export_Credits_Repay_LC(j) + New_Comm_Loans_Repay_LC(j)+New_Bonds_Repay_LC(j) + New_Commercial_Loans_Repay
        $fi_Tot_Repay_LC[$fi_i] = $fi_cbData['RL_'.$fi_i] + $fi_caData['RL_'.$fi_i] + $fi_chData['RLC_'.$fi_i] + $fi_ciData['RLC_'.$fi_i] + $fi_ccData['RLC_'.$fi_i] + $cal_loans['TR_'.$fi_i];
        //Retained_Earn_LC(j)= Taxable_Income_LC(j) - Income_Tax_LC(j) - Dividend_LC(j).
        $fi_Retained_Earn_LC_BD[$fi_i] = $fi_Taxable_Income_LC[$fi_i] - $fi_Income_Tax_LC[$fi_i];

        //testing dividend cal
        if ($fi_Cum_Taxable_Income_LC[$fi_i] >0) {
            //Dividend_Possible(currency name, j)= {[Taxable_Income_LC(j)-Income_Tax_LC(j)+Min(0, Cum_Retained_Earn(j))] � Equity_Share(currency,j)} / Exc_Rate(currency name,j).
            $fi_minretainearn = min(0, $fi_Cum_Retained_Earn_LC[$fi_i-1]);
            $fi_Dividend_Possible[$fi_i]=$fi_Taxable_Income_LC[$fi_i]-$fi_Income_Tax_LC[$fi_i] + $fi_minretainearn ;
            if ($fi_Dividend_Possible[$fi_i] < 0) {
              $fi_Dividend_Possible[$fi_i]=0;
            }
            //Dividend (currency name, j) = Minimum [Dividend_Due(currency name, j), Dividend_possible(currency name, j)].
            $fi_Dividend[$fi_i] =   min ($sourcesData['eq_totout'][$fi_i]  *  $sourcesData['eq_drateval']/100, $fi_Dividend_Possible[$fi_i]);
            $fi_Data['Div_'.$fi_i]=  $fi_Dividend[$fi_i];
          } else {
            $fi_Dividend[$fi_i] = 0;
            $fi_Data['Div_'.$fi_i]= 0;
          }
          $fi_Tot_Dividend_LC[$fi_i] = $fi_Dividend[$fi_i];
          $fi_Retained_Earn_LC[$fi_i] = $fi_Retained_Earn_LC_BD[$fi_i] - $fi_Tot_Dividend_LC[$fi_i];
          // Cum_Retained_Earn_LC(j)= Cum_Retained_Earn_LC(j-1) +Retained_Earn_LC(j).
          $fi_Cum_Retained_Earn_LC[$fi_i] =  $fi_Cum_Retained_Earn_LC[$fi_ii] + $fi_Retained_Earn_LC[$fi_i];
          //Funds_Applied(j)= Global_Invest_LC(j) + Tot_Interest_LC(j) + Tot_Repay_LC(j) + Tot_Equity_Repay_LC(j) + Tot_Dividend_LC(j) + VAT_TBR(j) + Tot_Export_Credit_Fee_LC(j)
          $fi_Funds_Applied[$fi_i] = $fi_agData['GIL_'.$fi_i] + $fi_Tot_Interest_LC[$fi_i] + $fi_Tot_Repay_LC[$fi_i] + $fi_cdData['E_'.$fi_i] + $fi_Tot_Dividend_LC[$fi_i] + $fi_cmData['TT_'.$fi_i] + $fi_chData['FLC_'.$fi_i];
          //Cash_Available_LC(j) = Funds_Arranged(j) - Funds_Applied(j)
          $fi_Cash_Available_LC[$fi_i] = $fi_Funds_Arranged[$fi_i] - $fi_Funds_Applied[$fi_i];
          //Short_Loans_LC(j) = Max(-Cash_Available_LC(j), - Short_Loans_Outstand(j-1)).
          $fi_Short_Loans_LC[$fi_i] = max(-$fi_Cash_Available_LC[$fi_i], -$fi_Short_Loans_Outstand_LC[$fi_ii]);
          //Short_Deposits_LC(j) = Cash_Available_LC(j) + Short_Loans_LC(j) - Short_Deposits_Balance_LC(j-1).
          $fi_Short_Deposits_LC[$fi_i] = $fi_Cash_Available_LC[$fi_i] + $fi_Short_Loans_LC[$fi_i] - $fi_Short_Deposits_Balance_LC[$fi_ii];
          //Short_Deposits_Balance_LC(j) = Short_Deposits_LC(j) + Short_Deposits_Balance_LC(j-1)
          $fi_Short_Deposits_Balance_LC[$fi_i] =  $fi_Short_Deposits_LC[$fi_i] + $fi_Short_Deposits_Balance_LC[$fi_ii];
          //Short_Loans _Outstand_LC(j)= Short_Loans_Outstand_LC(j-1)+Short_Loans_LC(j),
          $fi_Short_Loans_Outstand_LC[$fi_i] = $fi_Short_Loans_Outstand_LC[$fi_ii] + $fi_Short_Loans_LC[$fi_i];
          //	Short_Loans_Repay_LC(j) = max(0,- Short_Loans_LC(j))
          $fi_Short_Loans_Repay_LC[$fi_i] = max(0,-$fi_Short_Loans_LC[$fi_i]);
          //Tot_Sources_LC(j) = Pre_Investment_Cash_LC (j)+ Con_Contribution_Inc_LC(j)+ Con_Deposits_Inc_LC(j)+ Short_Deposits_Int_LC(j)+ Short_Deposits_Balance_LC(j-1) New_Equity_LC(j)+ New_Bonds_LC(j) + Tot_Export_Credits_LC(j) + New_Comm_Loans_LC(j) + Short_Loans_Repay_LC[$fi_i]
          $fi_Tot_Sources_LC[$fi_i] = $fi_Pre_Investment_Cash_LC[$fi_i] + $fi_bsData['C_'.$fi_i] + $fi_bsData['D_'.$fi_i]+ $fi_Short_Deposits_Int_LC[$fi_i] + $fi_Short_Deposits_Balance_LC[$fi_ii] + $fi_cdData['N_'.$fi_i] + $fi_Tot_Bonds_LC[$fi_i] + $fi_chData['LLC_'.$fi_i] + $fi_Tot_Comm_Loans_LC[$fi_i]/* + $fi_ciData['LLC_'.$fi_i]*/ + max(0,$fi_Short_Loans_LC[$fi_i]);
          //if($fi_Tot_Sources_LC[$fi_i] < 0.0){ // to make sure negative value is not passed
          //	$fi_Tot_Sources_LC[$fi_i]= 0;
          //}
        }
        $fi_Data['AREL_'.$fi_i] = $fi_Cum_Retained_Earn_LC[$fi_i];
        $fi_Data['TERL_'.$fi_i]	= $fi_cdData['E_'.$fi_i];//Tot_Equity_Repay_LC(j)
        $fi_Data['DivL_'.$fi_i]	= $fi_Tot_Dividend_LC[$fi_i];
        $fi_Data['TaxR_'.$fi_i] = $fi_Income_Tax_rate[$fi_i];
        $fi_Data['SDIL_'.$fi_i] = $fi_Short_Deposits_Int_LC[$fi_i];
        $fi_Data['SLLI_'.$fi_i] = $fi_Short_Loans_LC_Interest[$fi_i];
        $fi_Data['TIL_'.$fi_i] = $fi_Tot_Interest_LC[$fi_i];
        $fi_Data['CTIL_'.$fi_i] = $fi_Cum_Taxable_Income_LC[$fi_i];
        $fi_Data['TaxIL_'.$fi_i] = $fi_Taxable_Income_LC[$fi_i];
        $fi_Data['ITL_'.$fi_i] = $fi_Income_Tax_LC[$fi_i];
        $fi_Data['PICL_'.$fi_i] = $fi_Pre_Investment_Cash_LC[$fi_i];
        $fi_Data['FAr_'.$fi_i] = $fi_Funds_Arranged[$fi_i];
        $fi_Data['TRL_'.$fi_i] = $fi_Tot_Repay_LC[$fi_i];
        $fi_Data['FAp_'.$fi_i] = $fi_Funds_Applied[$fi_i];
        $fi_Data['CAL_'.$fi_i] = $fi_Cash_Available_LC[$fi_i];
        $fi_Data['SLL_'.$fi_i] = $fi_Short_Loans_LC[$fi_i];
        $fi_Data['SDL_'.$fi_i] = $fi_Short_Deposits_LC[$fi_i];

        $fi_Data['SDBL_'.$fi_i] = $fi_Short_Deposits_Balance_LC[$fi_i];
        $fi_Data['SLOLC_'.$fi_i] = $fi_Short_Loans_Outstand_LC[$fi_i];
        $fi_Data['SLRL_'.$fi_i] = $fi_Short_Loans_Repay_LC[$fi_i];
        $fi_Data['TSL_'.$fi_i] = $fi_Tot_Sources_LC[$fi_i];

        $fi_Data['REL_'.$fi_i] = $fi_Retained_Earn_LC[$fi_i];
        //Tot_Comm_Loans_LC(j)= New_Comm_Loans_LC(j)+Old_Loans_LC(j)+New_Commercial_Loans_LC(j) (Comm means Project)

        $fi_Data['TCLL_'.$fi_i] = $fi_Tot_Comm_Loans_LC[$fi_i];
        //Tot_Bonds_LC(j)=New_Bonds_LC(j)+Old_Bonds_LC(j)

        $fi_Data['TBL_'.$fi_i] = $fi_Tot_Bonds_LC[$fi_i];
        //Tot_App_LC(j) = Global_Invest_LC(j) + Tot_Interest_LC(j) + Tot_Repay_LC(j) + Short_Loans_Repay_LC(j) + Short_Deposits_Balance_LC(j)+Tot_Equity_Repay_LC(j)+Tot_Dividend_LC(j) + VAT_TBR(j) + Tot_Decom_Costs_LC(j)?? + Tot_Export_Credit1_Fee_LC(j) + Tot_Export_Credit2_Fee_LC(j)
        $fi_Tot_App_LC[$fi_i] = $fi_agData['GIL_'.$fi_i] + $fi_Tot_Interest_LC[$fi_i] + $fi_Tot_Repay_LC[$fi_i] + $fi_Short_Loans_Repay_LC[$fi_i] + $fi_Short_Deposits_Balance_LC[$fi_i] + $fi_cdData['E_'.$fi_i] + $fi_Tot_Dividend_LC[$fi_i] + $fi_cmData['TT_'.$fi_i] + $fi_chData['FLC_'.$fi_i];
        //edit removed + $fi_ceData['TDCL_'.$fi_i]
        $fi_Data['TAL_'.$fi_i] = $fi_Tot_App_LC[$fi_i];

        $fi_Data['TLCF_'.$fi_i] = $fi_TLCF_LC[$fi_i];
        $fi_Data['NTI_'.$fi_i] = $fi_Net_Taxable_Income_LC[$fi_i];

        // 2.2.14.1.	Profit/Loss computation
        //Tot_Expenses_LC(j)= Tot_Optcost_LC (j) + Tot_Interest_LC(j) + Prov_F_Loss_LC(j)+Tot_Dep_LC(j)+ Royalty_LC(j) + Income_Tax_LC(j) + VAT_LC(j)
        $fi_Tot_Expenses_LC[$fi_i] = $fi_cjData['O_'.fi_i] + $fi_Tot_Interest_LC[$fi_i] + $sourcesData['feData']['PFLY_'.$fi_i] + $sourcesData['dp_Data2']['T_'.$fi_i] + $fi_ckData['RLC_'.$fi_i] + $fi_Income_Tax_LC[$fi_i];

        //Profit_Loss_LC(j)=Tot_Revenues_LC(j) + Short_Deposits_Int_LC(j) - Tot_Expenses_LC(j).
        $fi_Profit_Loss_LC[$fi_i] = $fi_cjData['R_'.$fi_i] + $fi_Short_Deposits_Int_LC[$fi_i] - $fi_Tot_Expenses_LC[$fi_i];

        $fi_Data['TEL_'.$fi_i] = $fi_Tot_Expenses_LC[$fi_i];
        $fi_Data['PLL_'.$fi_i] = $fi_Profit_Loss_LC[$fi_i];

      }

      //$fi_Data = $this->updatePreInvestmentFix($fi_Data);

      $fi_Data['SDBL_'.($this->finplan->startYear-1)] = $fi_Short_Deposits_Balance_LC[$this->finplan->startYear-1];


      // echo "Fi data = ";
      // print_r($fi_Data);
      // echo "<br>";


      $fi_Data['sid']=1;
      $this->finplan->cnd->add($fi_Data);

      $fundsData = Array(
        'tot_int'     =>  $fi_Tot_Interest_LC,
        'tot_repay'   =>  $fi_Tot_Repay_LC,
        'short_dep'   =>  $fi_Short_Deposits_Balance_LC,
        'pre_inv'     =>  $fi_Pre_Investment_Cash_LC,
        'tax_inc'     =>  $fi_Taxable_Income_LC,
        'inc_tax'     =>  $fi_Income_Tax_LC,
        'ret_earn'    =>  $fi_Retained_Earn_LC,
        'short_loans' =>  $fi_Short_Loans_Outstand_LC,
        'short_int'   =>  $fi_Short_Deposits_Int_LC,
        'cml_earn'    =>  $fi_Cum_Retained_Earn_LC,
        'tot_div'     =>  $fi_Tot_Dividend_LC,
        'fi_Data'     =>  $fi_Data
      );

      return $fundsData;

  }

  public function getDecommissioningLastYears()
  {
    $de_cpData = $this->finplan->getPlantData();// get Data for all plants in this study
    $pi_ceData = $this->finplan->ced->getByField('1','sid');
    $pi_aaData = $this->finplan->aad->getByField('1','sid');
    $pi_bsData = $this->finplan->bsd->getByField('1','sid');
    $pi_cmData = $this->finplan->cmd->getByField('1','sid');
    $decomData = Array();
    $lastYears = Array();


  	if (is_array($de_cpData) && count($de_cpData) > 0) {// check if plant exist
      foreach ($de_cpData as $de_row) {
        $de_idData = $this->finplan->akd->getByField($de_row['id'],'pid');// get generalexpense by plant id
        $de_syear = $de_idData['startyear'];
  			$de_fyear = $de_idData['deccyear'];
        for ($i=$de_syear; $i<=$de_fyear; $i++) {
          if ($i == $this->finplan->startYear) {
            $decomData['CDDFL_'.$i] = $decomData['CDDFL_'.$i] + $pi_aaData['ConsumerDeposits'] + $pi_bsData['D_'.$i] + $pi_ceData['ADFL_'.$i] + $pi_cmData['TT_'.$i];//+VAT(j) fix
          } else {
            $decomData['CDDFL_'.$i] = $decomData['CDDFL_'.($i-1)] + $pi_bsData['D_'.$i] + $pi_ceData['ADFL_'.$i];
          }
        }
        $lastYears[$de_fyear+1] = $decomData['CDDFL_'.$de_fyear];
      }
    }

    return $lastYears;
  }

  /**
   * Calculate Balance sheet, add Foreign Exchange Data
   **/
  public function updateBalanceSheet($balanceData)
  {
    // Get Current_Invest_Costs(C,J)
    $fei_cpData = $this->finplan->getPlantData();
    $bs_Data = Array();

    foreach ($fei_cpData as $fei_row) {// for each plant
      $gi_aeData = $this->finplan->aed->getByField($fei_row['id'],'pid');//get investment Data by plant id
      for ($fei_j = 0; $fei_j < count($this->finplan->curChunks); $fei_j++){// for each currency
        $fei_CX = $this->finplan->curChunks[$fei_j];
        for($fei_i=$this->finplan->startYear;$fei_i <= $this->finplan->endYear; $fei_i++){// for each study year
          $fei_CY = $fei_CX.'_'.$fei_i;//currencyid_year
          $fei_Data[$fei_CY] = $fei_Data[$fei_CY] + $gi_aeData['EI_'.$fei_CY];
        }
      }
    }

    $fei_brData = $this->finplan->brd->getByField('1','sid');

    for ($fe_c = 0; $fe_c < count($this->finplan->curChunks); $fe_c++) {// for each currency
      $fe_CX = $this->finplan->curChunks[$fe_c];
      for ($fe_i=$this->finplan->startYear;$fe_i <= $this->finplan->endYear; $fe_i++) {// for each study year
        $fe_CY = $fe_CX.'_'.$fe_i;//currencyid_year
        // F_Invest(f,j) =    Current_Invest_Costs(f,p,j) + Committed_Invest(f,j)  for all plants
        $fe_invest[$fe_CY] = $fei_Data[$fe_CY]+ $fe_brData['C_'.$fe_CY];
        $balanceData['feData']['FI_'.$fe_CY] = $fe_invest[$fe_CY];//F_Invest(f,j)
        //F_Cash_Balance(f,j)= F_Loans_Bonds(f,j) + Equity(f,j)+F_Net_Revenues(f,j)-F_Invest(f,j) - F_Loans_Bonds_Repay(f,j) - Equity_Returned(f,j)-F_Loans_Bonds_Interest(f,j) - F_OptCost(f,j) - Dividened(f,j) - Tot_Export_Credit_Fee(f,j)
        $balanceData['feData']['CB_'.$fe_CY] = $balanceData['feData']['LB_'.$fe_CY] - $balanceData['feData']['FI_'.$fe_CY] - $balanceData['feData']['LBR_'.$fe_CY] - $balanceData['feData']['LBI_'.$fe_CY]- $balanceData['feData']['OC_'.$fe_CY];
      }
    }

    $this->finplan->cld->add($balanceData['feData']);
    // 2.2.17.	Balance sheet module
    $bs_aaData = $this->finplan->getBalanceData();//balace Data
    $bs_Data['sid']=1;
    // Init_Tot_Assets=Init_Total_Init_Gross_Fixed_Asset - Init_Cum_depreciation + Init_Con_contribution + Init_Work_inprog +Init_Receivable+Init_Short_Deposit
    $bs_Init_Tot_Assets = $bs_aaData['GrossFixedAssets'] - $bs_aaData['LessDepreciation'] - $bs_aaData['ConsumerContribution'];
    $bs_Init_Tot_Assets += $bs_aaData['WorkProgress'] + $bs_aaData['Receivables'] + $bs_aaData['ShortTermDeposits'];
    $bs_Data['Init_Tot_Assets'] = $bs_Init_Tot_Assets;
    // Init_Tot_Liabilities=Init_Equity + Init_Retained_Earnings + Init_Net_Bonds_outstand + Init_Net_Loans_outstand + Init_Con_deposits_Decomm_reserve + Init_Current_Maturity
    $bs_Init_Tot_Liabilities = $balanceData['eq_Data']['IE'] + $bs_aaData['RetainedEarning'] + $balanceData['ob_Data']['IOLC'] +  $balanceData['ol_Data']['ILO'] + $bs_aaData['ConsumerDeposits'] + $balanceData['ob_Data']['ICM'];

    $bs_Data['Init_Tot_Liabilities'] = $bs_Init_Tot_Liabilities;
    // Check_Initial_Balance = Init_Tot_Assets - Init_Tot_Liabilities
    $bs_Check_Initial_Balance = $bs_Init_Tot_Assets - $bs_Init_Tot_Liabilities;
    $bs_Data['Check_Initial_Balance'] = $bs_Check_Initial_Balance;

    $this->finplan->cpd->add($bs_Data);
  }




}

?>
