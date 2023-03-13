<?php

class AssetRisk extends FinplanService {

  public $finplan;

  public function __construct($finplan)
  {
    $this->finplan = $finplan;

    $this->cal_Loans = $this->finplan->getLoanCalculations();
    $this->bs_aaData = $this->finplan->getBalanceData();
    $this->fi_agData = $this->finplan->getTotalInvestment();
    $this->fi_cmData = $this->finplan->cmd->getByField('1','sid');
    $this->fi_bsData = $this->finplan->getContributionData();
    $this->fi_cjData = $this->finplan->cjd->getByField('1','sid');
    $this->sl_bkData = $this->finplan->getTotalSale();
    $this->sl_cgData = $this->finplan->getTotalPurchase();
    $this->sl_beData = $this->finplan->getTotalFuelCost();
    $this->fi_ckData = $this->finplan->ckd->getByField('1','sid');
    $this->fi_cdData = $this->finplan->cdd->getByField('1','sid');
    $this->fe_chData = $this->finplan->chd->getByField('1','sid');
    $this->fe_ccData = $this->finplan->ccd->getByField('1','sid');
    $this->fi_acData = $this->finplan->acd->getByField('1','sid');

  }


  /**
   * Calculate Asset, Equity, Liability
   **/
  public function updateAssetLiability($assetData)
  {
    $el_Net_Equity_Outstand_LC = Array();
    $el_Cum_Retained_Earn_LC = Array();
    $el_Net_Bonds_Outstand_LC = Array();
    $el_Net_Loan_Outstand_LC = Array();
    $el_Current_Maturity_LC = Array();
    $el_Cons_Dep_Decom_Fund_LC = Array();
    $as_Net_Fixed_Assets = Array();
    $as_Gross_Fixed_Assets = Array();
    $as_Con_contribution = Array();
    $as_Tot_Assets_LC = Array();
    $asel_Data = Array();

    // Assets Module
    for ($as_i=$this->finplan->startYear;$as_i <= $this->finplan->endYear; $as_i++) {//  for each study year
      $as_ii = $as_i-1;
      if ($as_i==$this->finplan->startYear) {
        // Gross_Fixed_Assets(First_year) = Init_Gross_Fixed_Asset + Global_Invset(First_year) - Work_inprog_LC(First_year)+ Init_Work_inprog.
        $as_Gross_Fixed_Assets[$as_i] = $this->bs_aaData['GrossFixedAssets'] + $this->fi_agData['GIL_'.$as_i] - $assetData['tin_Data']['WPL_'.$as_i] + $this->bs_aaData['WorkProgress'];
        // Cum_Depreciation(j) =Tot_Dep_LC(j)+Init_Cum_depreciation
        $as_Cum_Depreciation[$as_i] = $assetData['dp_Data2']['T_'.$as_i] + $this->bs_aaData['LessDepreciation'];
        // Con_contribution(j)= Init_Con_contribution + Con_Contribution_Inc_LC(j),
        $as_Con_contribution[$as_i] = $this->bs_aaData['ConsumerContribution'] + $this->fi_bsData['C_'.$as_i];
        // Receivable (j) = Init_Receivable+ VAT_TBR(j),
        $as_Receivable[$as_i] = $this->bs_aaData['Receivables'] + $this->fi_cmData['TT_'.$as_i];
      } else {
        // Gross_Fixed_Assets(j) = Gross_Fixed_Asset(j-1) + Global_Invset_LC(j) - Work_inprog_LC(j)+ Work_inprog_LC(j-1).
        $as_Gross_Fixed_Assets[$as_i] = $as_Gross_Fixed_Assets[$as_ii] + $this->fi_agData['GIL_'.$as_i] - $assetData['tin_Data']['WPL_'.$as_i] + $assetData['tin_Data']['WPL_'.$as_ii];
        // Cum_Depreciation(j) =Tot_Dep_LC(j)+ Cum_Depreciation(j-1),
        $as_Cum_Depreciation[$as_i] = $assetData['dp_Data2']['T_'.$as_i] + $as_Cum_Depreciation[$as_ii];
        // Con_contribution(j)= Con_contribution(j-1) + Con_Contribution_Inc_LC(j),
        $as_Con_contribution[$as_i]= $as_Con_contribution[$as_ii] + $this->fi_bsData['C_'.$as_i];
        // Receivable (j) = Receivable (j-1) + VAT_TBR(j),
        $as_Receivable[$as_i] = $as_Receivable[$as_ii] + $this->fi_cmData['TT_'.$as_i];
      }
      $asel_Data['GFA_'.$as_i] = $as_Gross_Fixed_Assets[$as_i];
      $asel_Data['CD_'.$as_i] = $as_Cum_Depreciation[$as_i];
      $asel_Data['CC_'.$as_i] = $as_Con_contribution[$as_i];
      $asel_Data['R_'.$as_i] = $as_Receivable[$as_i];
      // Net_Fixed_Assets(j)=Gross_Fixed_Assets(j) - Cum_Depreciation(j) - Con_Contribution_LC(j)
      $as_Net_Fixed_Assets[$as_i] = $as_Gross_Fixed_Assets[$as_i] - $as_Cum_Depreciation[$as_i] - $as_Con_contribution[$as_i];
      // Tot_Assets_LC(j)= Net_Fixed_Assets(j)+ Work_inprog(j) + Receivable(j) + Short_Deposits_Balance_LC (j)
      $as_Tot_Assets_LC[$as_i] = $as_Net_Fixed_Assets[$as_i] + $assetData['tin_Data']['WPL_'.$as_i] + $as_Receivable[$as_i] + $assetData['short_dep'][$as_i];
      $asel_Data['NFA_'.$as_i] = $as_Net_Fixed_Assets[$as_i];
      $asel_Data['TAL_'.$as_i] = $as_Tot_Assets_LC[$as_i];
    }

    // Equity and Liabilities Module
    $el_Net_Equity_Outstand_LC[$this->finplan->startYear-1] = $assetData['eq_Data']['IE'];
    for ($el_i=$this->finplan->startYear;$el_i <= $this->finplan->endYear; $el_i++) { //  for each study year
      $el_ii = $el_i+1;//  adding 1 to current year
      if ($el_i==$this->finplan->startYear) {
        // Cum_Retained_Earn_LC(j) =  Init_Retained_Earnings_LC +Retained_Earn_LC(j).
        $el_Cum_Retained_Earn_LC[$el_i] =  $this->bs_aaData['RetainedEarning'] + $assetData['ret_earn'][$el_i];
        // Cons_Dep_Decom_Fund_LC(j)  =  Init_Cons_Dep_Decom_Funds_LC + Con_Deposit_Inc_LC(j)+Ann_Decom_Funds_LC (j)
        $el_Cons_Dep_Decom_Fund_LC[$el_i] = $this->bs_aaData['ConsumerDeposits'] + $this->fi_bsData['D_'.$el_i] + $assetData['de_Data']['ADFL_'.$el_i] + $this->fi_cmData['TT_'.$el_i];//+VAT(j) fix
      }else{
        // Cum_Retained_Earn_LC(j)= Cum_Retained_Earn_LC(j-1) +Retained_Earn_LC(j).
        $el_Cum_Retained_Earn_LC[$el_i] =  $el_Cum_Retained_Earn_LC[$el_i-1] + $assetData['ret_earn'][$el_i];
        // Cons_Dep_Decom_Fund_LC(j) = Cons_Dep_Decom_Fund_LC(j-1) + Con_Deposit_Inc_LC(j)+Ann_Decom_Funds_LC (j)
        $el_Cons_Dep_Decom_Fund_LC[$el_i] = $el_Cons_Dep_Decom_Fund_LC[$el_i-1] + $this->fi_bsData['D_'.$el_i] + $assetData['de_Data']['ADFL_'.$el_i];
      }

      //$asel_Data['CDDFL_'.$el_i] = $el_Cons_Dep_Decom_Fund_LC[$el_i];

      //  Net_Equity_Outstand_LC(j) = Tot_Equity_Outstand_LC(j)
      $el_Net_Equity_Outstand_LC[$el_i] = $assetData['eq_totout'][$el_i];
      $asel_Data['NEOL_'.$el_i] = $el_Net_Equity_Outstand_LC[$el_i];
      //   Net_Bonds_Outstand_LC(j)= New_Bonds_Outstand_LC(j) - New_Bonds_Repayment_LC(j+1)+ Old_Bonds_Outstand_LC(j) - Old_Bonds_Repay(j+1)
      $el_Net_Bonds_Outstand_LC[$el_i] = $assetData['bo_Data']['OLC_'.$el_i] - $assetData['bo_Data']['RLCN_'.$el_ii]+ $assetData['ob_Data']['BL_'.$el_i] - $assetData['ob_Data']['ILN_'.$el_ii];
      $asel_Data['NBOL_'.$el_i] = $el_Net_Bonds_Outstand_LC[$el_i];
      //   Net_Loan_Outstand_LC(j) = Tot_Export_Credits_Balance_LC(j) - Tot_Export_Credits_Repay_LC(j+1) + Tot_Comm_Loans_Balance_LC(j) - Tot_Comm_Loans_Repay_LC(j+1)  + Old_Loans_Outstand_LC(j) - Old_Loans_Repay_LC(j+1)
      $el_Net_Loan_Outstand_LC[$el_i] = $assetData['tex_Data']['BLC_'.$el_i] - $assetData['tex_Data']['RLCN_'.$el_ii] + $assetData['tcl_Data']['BLC_'.$el_i] - $assetData['tcl_Data']['RLCN_'.$el_ii]  + $assetData['ol_Data']['LL_'.$el_i] - $assetData['ol_Data']['RLN_'.$el_ii] + $this->cal_Loans['TO_'.$el_i];
      $asel_Data['NLOL_'.$el_i] = $el_Net_Loan_Outstand_LC[$el_i];
      //  Current_Maturity_LC(j)=.Short_Loans_Outstand_LC(j) +Tot_Export_Credits_Repay_LC(j+1)+ Tot_Comm_Loans_Repay_LC(j+1)+ Old_Loans_Repay_LC(j+1)+ New_Bonds_Repayment_LC(j+1)+ Old_Bonds_Repay(j+1).
      $el_Current_Maturity_LC[$el_i] = $assetData['short_loans'][$el_i] + $assetData['tex_Data']['RLCN_'.$el_ii] + $assetData['tcl_Data']['RLCN_'.$el_ii] + $assetData['ol_Data']['RLN_'.$el_ii]+ $assetData['bo_Data']['RLCN_'.$el_ii] + $assetData['ob_Data']['ILN_'.$el_ii] + $this->cal_Loans['TM_'.$el_i];
      $asel_Data['CML_'.$el_i] = $el_Current_Maturity_LC[$el_i];
      //   Tot_Liabilities_LC(j)= Net_Equity_Outstand_LC(j) + Cum_Retained_Earn_LC(j) + Net_Bonds_Outstand_LC(j)+Net_Loan_Outstand_LC(j)+ Cons_Dep_Decom_Fund_LC(j)+ Current_Maturity_LC(j).
      $el_Tot_Liabilities_LC[$el_i] = $el_Net_Equity_Outstand_LC[$el_i] + $assetData['cml_earn'][$el_i] + $el_Net_Bonds_Outstand_LC[$el_i] + $el_Net_Loan_Outstand_LC[$el_i] + $el_Cons_Dep_Decom_Fund_LC[$el_i] + $el_Current_Maturity_LC[$el_i];
      $asel_Data['TLL_'.$el_i] = $el_Tot_Liabilities_LC[$el_i];
      //  Tot_Assets_Liabilities_LC(j)= Tot_Assets_LC(j) -Tot_Liabilities_LC(j)
      $el_Tot_Assets_Liabilities_LC[$el_i] = $as_Tot_Assets_LC[$el_i]- $el_Tot_Liabilities_LC[$el_i];
      $asel_Data['TALL_'.$el_i] = $el_Tot_Assets_Liabilities_LC[$el_i];
    }
    //Current maturity for Intial Balance sheet
    //repayment on bonds and loans
    $asel_Data['CMBL'] = $assetData['ob_Data']['RL_'.$this->finplan->startYear] + $assetData['ol_Data']['RLN_'.$this->finplan->startYear];
    $asel_Data = array_merge($asel_Data, $this->updateDecommissioningFix($assetData['de_Data']));
    $asel_Data['sid']=1;

    $this->finplan->cod->add($asel_Data);	//  add both asset ,equity&liability module


    $liabilityData = Array(
      'net_equ'     =>  $el_Net_Equity_Outstand_LC,//
      'cml_ret'     =>  $el_Cum_Retained_Earn_LC,
      'net_bnd'     =>  $el_Net_Bonds_Outstand_LC,//
      'net_loan'    =>  $el_Net_Loan_Outstand_LC, //
      'cur_mat'     =>  $el_Current_Maturity_LC, //
      'dep_dec'     =>  $el_Cons_Dep_Decom_Fund_LC,//
      'net_fxd'     =>  $as_Net_Fixed_Assets, //
      'grs_fxd'     =>  $as_Gross_Fixed_Assets,
      'con_ctb'     =>  $as_Con_contribution,
      'tot_ast'     =>  $as_Tot_Assets_LC //
    );

    return $liabilityData;

  }

  /**
   * Calculate Decommissioning
   **/
  public function updateDecommissioningFix($de_Data)
  {
    $decomData = Array();
  	$de_cpData = $this->finplan->getPlantData();// get Data for all plants in this study
    $decomData['sid'] = 1;

    for ($el_i=$this->finplan->startYear;$el_i <= $this->finplan->endYear; $el_i++) {
      $decomData['CDDFL_'.$el_i] = 0;
    }

  	if (is_array($de_cpData) && count($de_cpData) > 0) {// check if plant exist
      foreach ($de_cpData as $de_row) {
        $de_idData = $this->finplan->akd->getByField($de_row['id'],'pid');// get generalexpense by plant id
        $de_syear = $de_idData['startyear'];
  			$de_fyear = $de_idData['deccyear'];
        for ($i=$de_syear; $i<=$de_fyear; $i++) {
          if ($i == $this->finplan->startYear) {
            $decomData['CDDFL_'.$i] = $decomData['CDDFL_'.$i] + $this->bs_aaData['ConsumerDeposits'] + $this->fi_bsData['D_'.$i] + $de_Data['ADFL_'.$i] + $this->fi_cmData['TT_'.$i];//+VAT(j) fix
          } else {
            $decomData['CDDFL_'.$i] = $decomData['CDDFL_'.($i-1)] + $this->fi_bsData['D_'.$i] + $de_Data['ADFL_'.$i];
          }
        }
      }
    }

    //$this->finplan->cod->add($decomData);
    // echo "new Decom = <br>";
    // print_r($decomData);
    // echo "<br>";
    return $decomData;
  }

  /**
   * 2.2.18.	Project risk analysis module
   **/
  public function updateRiskAnalysis($fi_Data)
  {
  	$rk_bnData = $this->finplan->bnd->getByField('1','sid');// balace Data
  	$rk_L1Year = $rk_bnData['FY_Cash_DebtService'] + $rk_bnData['Loan_Term'];// L1Year = FY_Cash_DebtService+ Loan_Term.
  	$rk_L2Year =  $rk_bnData['FY_Cash_DebtService'] + $rk_bnData['Life_Term'];// L2Year = FY_Cash_DebtService + Life_Term

  	$rk_D_Rate = $rk_bnData['DRate']/100;
  	$rk_Security_ratio1 = $rk_bnData['Security_ratio1'];
  	$rk_Security_ratio2 = $rk_bnData['Security_ratio2'];

  	for ($rk_i=$this->finplan->startYear;$rk_i <= $this->finplan->endYear; $rk_i++) { //   for each study year

  		if($rk_i >$rk_bnData['FY_Cash_DebtService'] and $rk_i <= $rk_L1Year){// Loan_during_Term(k) = Pre_Investment_Cash_LC (k)
  			$rk_Loan_during_Term[$rk_i] = $fi_Data['PICL_'.$rk_i];
  		}else{
  			$rk_Loan_during_Term[$rk_i] = 0;
  		}
  		if($rk_i >$rk_bnData['FY_Cash_DebtService'] and $rk_i <= $rk_L2Year){//  Loan_during_Life(k) = Pre_Investment_Cash_LC (k),  .
  			$rk_Loan_during_Life[$rk_i] = $fi_Data['PICL_'.$rk_i];
  		}else{
  			$rk_Loan_during_Life[$rk_i] = 0;
  		}

  		$rk_Data['LL_'.$rk_i] = $rk_Loan_during_Life[$rk_i];// Loan_during_Life
  		$rk_Data['LT_'.$rk_i] = $rk_Loan_during_Term[$rk_i];// Loan_during_Term
  	}

  	for ($rk_j=$this->finplan->startYear;$rk_j <= $this->finplan->endYear; $rk_j++) { //   for each study year
  		$npvtarray=array();
  		for($rk_m=$rk_j;$rk_m <= $rk_L1Year; $rk_m++) {
  			$npvtarray[] = $rk_Loan_during_Term[$rk_m];
  		}
  		$npvlarray=array();
  		for($rk_n=$rk_j;$rk_n <= $rk_L2Year; $rk_n++) {
  			$npvlarray[] = $rk_Loan_during_Life[$rk_n];
  		}
  		// Loan_NPV_Term(j) =  NPV (D_Rate/100, Loan_during_Life(j) to � Loan_during_Term(L1Year)),  .
  		$rk_Loan_NPV_Term[$rk_j] = $this->finplan->fpmt->NPV($rk_D_Rate, $npvtarray);
  		//array_splice($npvtarray,0,1);
  		// MX_Fin_Loan(j)= Loan_NPV_Term(j)/Security_ratio1
  		$rk_MX_Fin_Loan[$rk_j] = $rk_Loan_NPV_Term[$rk_j]/$rk_Security_ratio1;

  		// Loan_NPV_Life(j) =  NPV (D_Rate/100, Loan_during_Life(j) to ���.. Loan_during_Life(L2Year)),  .
  		$rk_Loan_NPV_Life[$rk_j] = $this->finplan->fpmt->NPV($rk_D_Rate, $npvlarray);
  		//array_splice($npvlarray,0,1);
  		// MX_Fin_Life(j)= Loan_NPV_Life(j)/Security_ratio1
  		$rk_MX_Fin_Life[$rk_j] = $rk_Loan_NPV_Life[$rk_j]/$rk_Security_ratio2;

  		$rk_Data['NT_'.$rk_j] = $rk_Loan_NPV_Term[$rk_j];// Loan_NPV_Term
  		$rk_Data['MFLo_'.$rk_j] = $rk_MX_Fin_Loan[$rk_j];// MX_Fin_Loan

  		$rk_Data['NL_'.$rk_j] = $rk_Loan_NPV_Life[$rk_j];// Loan_NPV_Life
  		$rk_Data['MFLi_'.$rk_j] = $rk_MX_Fin_Life[$rk_j];// MX_Fin_Life
  	}
  	$rk_Data['sid']=1;
  	$this->finplan->crd->add($rk_Data);
  }


  /**
  * 2.2.19.	Financial Ratios module
  **/
  public function updateFinancialRatios($ratioData)
  {
    $br_Data = Array();
    $br_WCapital_Ratio = Array();
    for ($br_i=$this->finplan->startYear;$br_i <= $this->finplan->endYear; $br_i++) { //   for each study year
      //   Working Capital ratio (R1)
      //  WCapital_Ratio(j)= Net_Equity_Outstand_LC[j]+Cum_Retained_Earn_LC[j] +Net_Bonds_Outstand_LC[j] +Net_Loan_Outstand_LC[j]/max(0.00001,Net_Fixed_Assets(j)+Work_inprog(j) )
      $br_WCapital_Ratio[$br_i]= ($ratioData['net_fxd'][$br_i] + $ratioData['tin_Data']['WPL_'.$br_i])/max(0.00001,($ratioData['net_equ'][$br_i] + $ratioData['cml_ret'][$br_i] + $ratioData['net_bnd'][$br_i] + $ratioData['net_loan'][$br_i]) );
      //M.W. Request - 2016-08-23
      //reverse the division in Working Capital Ratio
      //old:
      //$br_WCapital_Ratio[$br_i]= ($ratioData['net_equ'][$br_i] + $ratioData['cml_ret'][$br_i] + $ratioData['net_bnd'][$br_i] +
      // $ratioData['net_loan'][$br_i])/max(0.00001,($ratioData['net_fxd'][$br_i] + $ratioData['tin_Data']['WPL_'.$br_i]) );

      $br_R1[$br_i] = max(0, min(4, $br_WCapital_Ratio[$br_i])) ;
      if($br_WCapital_Ratio[$br_i] < 1) {
        $br_R1_Status[$br_i] = 'Pb';
      } elseif ($br_WCapital_Ratio[$br_i] >= 1) {
        $br_R1_Status[$br_i] = 'OK';
      }
      $br_Data['WCR_'.$br_i] = $br_WCapital_Ratio[$br_i];
      $br_Data['R1_'.$br_i] = $br_R1[$br_i];
      $br_Data['R1S_'.$br_i] = $br_R1_Status[$br_i];
      //  Leverage ration (R2)
      //  Leverage(j) = Net_Bonds_Outstand_LC(j) + Net_Loan_Outstand_LC(j)/Net_Equity_Outstand_LC(j) + Cum_Retained_Earn_LC(j)
      $br_Leverage[$br_i] = ($ratioData['net_bnd'][$br_i] + $ratioData['net_loan'][$br_i])/($ratioData['net_equ'][$br_i] + $ratioData['cml_ret'][$br_i]);
      $br_R2[$br_i] = max(0, min(15, $br_Leverage[$br_i]));
      //  R2_Status(j) = PB if Leverage(j) >1.3.
      //  R2_Status(j) = OK if Leverage(j) <1.3
      if($br_Leverage[$br_i] > 1.3){
        $br_R2_Status[$br_i] = 'Pb';
      }elseif($br_Leverage[$br_i] < 1.3) {
        $br_R2_Status[$br_i] = 'Ok';
      }
      $br_Data['L_'.$br_i] = $br_Leverage[$br_i];
      $br_Data['R2_'.$br_i] = $br_R2[$br_i];
      $br_Data['R2S_'.$br_i] = $br_R2_Status[$br_i];
      //  Equipment renewal ratio (R3)
      //  Net_Investment(j) = Net_Fixed_Assets(j) + Work_inprog(j)
      $br_Net_Investment[$br_i] = $ratioData['net_fxd'][$br_i] + $ratioData['tin_Data']['WPL_'.$br_i];
      $br_Aggr_Global_Investment[$br_i] = max(0.0001, ($ratioData['grs_fxd'][$br_i] + $ratioData['tin_Data']['WPL_'.$br_i]));
      //  Equip_Ren(j) = Net_Investment(j) / Aggr_Global_Investment(j)
      $br_Equip_Ren[$br_i] = $br_Net_Investment[$br_i] / $br_Aggr_Global_Investment[$br_i];
      $br_R3[$br_i] = max(0, min(1, $br_Equip_Ren[$br_i]));
      if($br_R3[$br_i] < 0.5){
        $br_R3_Status[$br_i] = 'Pb';
      }else{
        $br_R3_Status[$br_i] = 'Ok';
      }

      $br_Data['ER_'.$br_i] = $br_Equip_Ren[$br_i];
      $br_Data['R3_'.$br_i] = $br_R3[$br_i];
      $br_Data['R3S_'.$br_i] = $br_R3_Status[$br_i];

      //  Gross Profit Ratio (R4)
      //  Gross_Profit (j) = Revenues_Sale_LC(j) - Tot_Optcost_LC (j)
      $br_Gross_Profit[$br_i] = $this->sl_bkData['LC_'.$br_i] - $this->fi_cjData['O_'.$br_i];
      //  Value_Added (j) = Revenues_Sale_LC(j) - Tot_Expenses_Purchase_LC(j) - Tot_Fuel_Cost_LC(j)
      $br_Value_Added[$br_i] = $this->sl_bkData['LC_'.$br_i] - $this->sl_cgData['LC_'.$br_i] - $this->sl_beData['LC_'.$br_i];
      //  Gross_Profit_Ratio(j) = Gross_Profit (j)/max(0.00001, Value_Added (j))
      $br_Gross_Profit_Ratio[$br_i] = $br_Gross_Profit[$br_i]/(max(0.00001, $br_Value_Added[$br_i]));
      //  R4(j) = max(0, min{1, Gross_Profit_Ratio(j)})
      $br_R4[$br_i] = max(0, min(1, $br_Gross_Profit_Ratio[$br_i]));
      //  R4_Status(j) = PB if R4(j) <0.2
      if ($br_R4[$br_i] < 0.2) {
        $br_R4_Status[$br_i] = 'Pb';
      } else {
        $br_R4_Status[$br_i] = 'Ok';
      }

      $br_Data['GPR_'.$br_i] = $br_Gross_Profit_Ratio[$br_i];
      $br_Data['R4_'.$br_i] = $br_R4[$br_i];
      $br_Data['R4S_'.$br_i] = $br_R4_Status[$br_i];

      //  Debt Repayment time Ratio (R5)
      //  Yealy_Cash_Flow(j) = Tot_Revenues_LC(j) - Tot_Optcost_LC (j) - Income_Tax_LC(j) - Royalty_LC(j)
      $br_Yealy_Cash_Flow[$br_i] = $this->fi_cjData['R_'.$br_i] - $this->fi_cjData['O_'.$br_i] - $ratioData['inc_tax'][$fi_i] - $this->fi_ckData['RLC_'.$fi_i];
      //  Debt_Repayment_Time(j) = Net_Bonds_Outstand_LC(j) + Net_Loan_Outstand_LC(j)/max(0.0001, Yealy_Cash_Flow(j))
      $br_Debt_Repayment_Time[$br_i] = ($ratioData['net_bnd'][$br_i] + $ratioData['net_loan'][$br_i])/max(0.0001, $br_Yealy_Cash_Flow[$br_i]);
      $br_R5[$br_i] = max(0.2, min(40, $br_Debt_Repayment_Time[$br_i]));
      if ($br_R5[$br_i] > 4) {
        $br_R5_Status[$br_i] = 'Pb';
      } else {
        $br_R5_Status[$br_i] = 'Ok';
      }

      $br_Data['DRT_'.$br_i] = $br_Debt_Repayment_Time[$br_i];
      $br_Data['R5_'.$br_i] = $br_R5[$br_i];
      $br_Data['R5S_'.$br_i] = $br_R5_Status[$br_i];

      //  Sensitivity to Exchange Rate Ratio (R6)
      for($br_c = 0; $br_c < count($this->finplan->base1Chunks); $br_c++){//   for each currency
        $br_CX = $this->finplan->base1Chunks[$br_c];
        $br_CY = $br_CX.'_'.$br_i;
        if($br_CX == $this->finplan->baseCurrency){
          //  Local_Cash_LC(j) = Revenue_Sale(c,j) + Short_Deposits_Int_LC(j) - Expenses_Purchase(c,j) -Tot_O&M_Cost(c,j) - Tot_Fuel_Cost(c,j) - Tot_Gen_Exp(c,j) - Income_Tax_LC(j) - Royalty_LC(j) Old_Loans_Int ( c,j)  - Old_Loans_Repay(c,j) - Old_Bonds_Return (c,j)
          //  -  Old_Bonds_Repay(c,j) - New_Loans_Interest(c,j) - New_Comm_Loans_Repay(c,j) - New_bonds_Return(c,j) - Bonds_Repayment(c,j) - Short_Loans_LC_Interest(j) - Short_Loans_Repay_LC(j)
          //  Local_Cash_LC(j) = Revenue_Sale(c,j) - Tot_Loc_invest(c,j)-Tot_Fuel_Cost(c,j)-Tot_O&M_Cost(c,j)- Tot_Gen_Exp(c,j) -Expenses_Purchase(c,j)-Short_Loans_LC_Interest(j)-New_Loans_Interest(c,j)-New_Comm_Loans_Repay(c,j)+ Bonds (currency name,k)+ New_comm_loans(c,j) + Short_Loans_LC[j] - Bonds_Interest(c,j) - Bonds_Repayment (c,k) - Income_Tax_LC(j) - Royalty_LC(j)
          $br_Local_Cash_LC[$br_i] = $this->sl_bkData['RS_'.$br_CY] - $this->fi_agData['EI_'.$br_CY] - $ratioData['tfc_Data']['E_'.$br_CY] - $ratioData['tom_Data']['E_'.$br_CY] - $ratioData['tge_Data']['E_'.$br_CY]- $ratioData['pr_Data2']['EP_'.$br_CY]- $ratioData['fi_Data']['SLLI_'.$br_i] - $ratioData['tcl_Data'][XmlKey::KEY_I.'_'.$br_CY] - $ratioData['tcl_Data']['R_'.$br_CY] + $ratioData['bo_Data']['B_'.$br_CY] + $ratioData['tcl_Data']['L_'.$br_CY] + $ratioData['fi_Data']['SLL_'.$br_i] - $ratioData['bo_Data'][XmlKey::KEY_I.'_'.$br_CY] - $ratioData['bo_Data']['R_'.$br_CY] - $ratioData['fi_Data']['ITL_'.$br_i] - $this->fi_ckData['RLC_'.$br_i] ;
          $br_Data['Loc_'.$br_i] = $br_Local_Cash_LC[$br_i];
        } else {
          //  Req_Foreign_Currencies (f,j) = Expenses_Purchase(f,j) + Tot_O&M_Cost(f,j) + Tot_Fuel_Cost(f,j) + Tot_Gen_Exp(f,j) + Old_Loans_Int(f,j) + Old_Loans_Repay(f,j) + Old_Bonds_Return (f,j) + Old_Bonds_Repay(f,j) + New_Loans_Interest(f,j) +
          //  New_Comm_Loans_Repay(f,j) - New_bonds_Return(f,j) + Bonds_Reapyment(f,j) +Tot_Export_Credits_Interest(f,j) + Tot_Export_Credits_Repay(f,j) + Tot_Export_Credit1_Fee(f,j) + Tot_Export_Credit2_Fee(f,j) - Revenue_Sale(f,j),
          $br_Req_Foreign_Currencies[$br_CY] = $ratioData['pr_Data2']['EP_'.$br_CY] + $ratioData['tom_Data']['E_'.$br_CY] + $ratioData['tfc_Data']['E_'.$br_CY] + $ratioData['tge_Data']['E_'.$br_CY] + $ratioData['ol_Data'][XmlKey::KEY_I.'_'.$br_CY] + $ratioData['ol_Data']['R_'.$br_CY] +
          $ratioData['ob_Data']['R_'.$br_CY] + $ratioData['ob_Data'][XmlKey::KEY_I.'_'.$br_CY] + $ratioData['tcl_Data'][XmlKey::KEY_I.'_'.$br_CY] + $ratioData['tcl_Data']['R_'.$br_CY] - $this->fe_ccData[$fe_nval] + $ratioData['bo_Data']['R_'.$br_CY] +$this->fe_chData[XmlKey::KEY_I.'_'.$br_CY] + $this->fe_chData['R_'.$br_CY]
          + $ratioData['tex_Data']['TFEE_'.$br_CY] - $this->sl_bkData['RS_'.$br_CY];
          $br_Req_Foreign_Currencies_LC[$br_i] =  $br_Req_Foreign_Currencies_LC[$br_i] + ($br_Req_Foreign_Currencies[$br_CY] * $this->fi_acData[$br_CY]);
          $br_Data['F_'.$br_CY] = $br_Req_Foreign_Currencies[$br_CY];
        }
      }
      $br_Data['For_'.$br_i] = $br_Req_Foreign_Currencies_LC[$br_i];
      $br_Foreign_Currency_ratio[$br_i] =  $br_Local_Cash_LC[$br_i]/max(0.00001,$br_Req_Foreign_Currencies_LC[$br_i]);
      $br_R6[$br_i] = max(0.1, min(15, $br_Foreign_Currency_ratio[$br_i]));
      //  R6_Status(j) = PB if R6(j) < 1.2
      if ($br_R6[$br_i] < 1.2) {
        $br_R6_Status[$br_i] = 'Pb';
      } else {
        $br_R6_Status[$br_i] = 'Ok';
      }
      $br_Data['FCR_'.$br_i] = $br_Foreign_Currency_ratio[$br_i];
      $br_Data['R6_'.$br_i] = $br_R6[$br_i];
      $br_Data['R6S_'.$br_i] = $br_R6_Status[$br_i];
      //  Break-even Point for Amount of Electricity Sold (R7)

      //  Revenues_Netof_Fuels_LC(j)= Revenue_Sale_LC(j) - Tot_Fuel_Cost_LC(j) - Tot_Expenses_Purchase__LC(j),
      $br_Revenues_Netof_Fuels_LC[$br_i] = $ratioData['sl_Data2']['LC_'.$br_i] - $this->sl_beData['LC_'.$br_i] - $this->sl_cgData['LC_'.$br_i];
      //  Debt_Service_LC(j) = Tot_Interest_LC(j)+ Tot_Repay_LC(j)  - Short_Deposits_Int_LC(j) REMOVED + Short_Loans_Repay_LC(j)
      $br_Debt_Service_LC[$br_i] = $ratioData['tot_int'][$br_i] + $ratioData['tot_repay'][$br_i] - $ratioData['short_int'][$br_i];
      //  Fixed_Optcost_LC(j) = Tot_Optcost_LC (j) -Tot_Fuel_Cost_LC(j) changed FROM + -
      $br_Fixed_Optcost_LC[$br_i] = $this->fi_cjData['O_'.$br_i] - $this->sl_beData['LC_'.$br_i];
      //  Min_Expenditure_LC(j)= Debt_Service(j) + Fixed_Optcost_LC(j),
      $br_Min_Expenditure_LC[$br_i] = $br_Debt_Service_LC[$br_i] + $br_Fixed_Optcost_LC[$br_i];
      $br_Break_Even_Point[$br_i] =  $br_Min_Expenditure_LC[$br_i]/max(0.00001,$br_Revenues_Netof_Fuels_LC[$br_i]);
      //  R7(j) = max(0.1, min{10, Break_Even_Point(j)})
      $br_R7[$br_i] = max(0.1, min(10, $br_Break_Even_Point[$br_i]));
      //  R7_Status(j) = PB if R7(j) >0.8
      if($br_R7[$br_i] > 0.8){
        $br_R7_Status[$br_i] = 'Pb';
      }else{
        $br_R7_Status[$br_i] = 'Ok';
      }
      $br_Data['BEP_'.$br_i] = $br_Break_Even_Point[$br_i];
      $br_Data['R7_'.$br_i] = $br_R7[$br_i];
      $br_Data['R7S_'.$br_i] = $br_R7_Status[$br_i];
      //  Relative Interest Charge Weight (R8)
      $br_Interest_Charge_Ratio[$br_i] = ($ratioData['tot_int'][$br_i] - $ratioData['short_int'][$br_i])/max(0.0001,$br_Value_Added[$br_i]);
      //  R8(j) = max(0.01, min{2, Interest_Charge_Ratio (j)})
      $br_R8[$br_i] = max(0.01, min(2, $br_Interest_Charge_Ratio[$br_i]));
      //  R8_Status(j) = PB if R8(j) >0.2
      if($br_R8[$br_i] > 0.2){
        $br_R8_Status[$br_i] = 'Pb';
      }else{
        $br_R8_Status[$br_i] = 'Ok';
      }
      $br_Data['ICR_'.$br_i] = $br_Interest_Charge_Ratio[$br_i];
      $br_Data['R8_'.$br_i] = $br_R8[$br_i];
      $br_Data['R8S_'.$br_i] = $br_R8_Status[$br_i];

      //  Global Ratio
      $br_Global_Index[$br_i] = (0.05*$br_R1[$br_i]/1)+(0.20*$br_R2[$br_i]/1.3)+(0.10*$br_R31[$br_i]/0.5)+(0.15*$br_R4[$br_i]/0.2)+(0.20*$br_R5[$br_i]/4)+(0.15*$br_R6[$br_i]/1.2)+(0.10*$br_R7[$br_i]/0.8)+(0.05*$br_R8[$br_i]/0.2);
      $br_Self_Finance[$br_i] = $ratioData['pre_inv'][$br_i] + $ratioData['con_ctb'][$br_i] + $this->fi_bsData['D_'.$br_i] - $ratioData['tot_int'][$br_i] - $ratioData['tot_repay'][$br_i];

      //  Self-financing ratio (R9)
      $br_ii = $br_i+1;

      $fi_gilsum = ($this->fi_agData['GIL_'.$br_i] + $this->fi_agData['GIL_'.$br_ii])/2;

      //division by zero
      $br_Self_Financing_Ratio[$br_i] = $br_Self_Finance[$br_i]/max(0.0001, $fi_gilsum);

      $br_R9[$br_i] = $br_Self_Financing_Ratio[$br_i];

      //  R9_Status(j) = PB if R9(j) >0.3
      // 2016-08-15 changed to PB if R9(j) < 0.3
      if($br_R9[$br_i] < 0.3){
        $br_R9_Status[$br_i] = 'Pb';
      }else{
        $br_R9_Status[$br_i] = 'Ok';
      }

      $br_Data['GI_'.$br_i] = $br_Global_Index[$br_i];
      $br_Data['SFR_'.$br_i] = $br_Self_Financing_Ratio[$br_i];
      $br_Data['R9_'.$br_i] = $br_R9[$br_i];
      $br_Data['R9S_'.$br_i] = $br_R9_Status[$br_i];

      //  Debt-to-Equity ratio (R10)
      $br_Tot_Loans[$br_i] = $ratioData['net_equ'][$br_i] + $ratioData['cml_ret'][$br_i] + $ratioData['net_bnd'][$br_i] + $ratioData['net_loan'][$br_i];
      $br_Debt_to_Equity[$br_i] = ($ratioData['net_bnd'][$br_i] + $ratioData['net_loan'][$br_i])/$br_Tot_Loans[$br_i];

      $br_R10[$br_i] = $br_Debt_to_Equity[$br_i];
      //  R10_Status(j) = PB if R10(j) <0.6
      // 2016-08-15 changed to Pb if R10(j) > 0.6
      if($br_R10[$br_i] > 0.6){
        $br_R10_Status[$br_i] = 'Pb';
      }else{
        $br_R10_Status[$br_i] = 'Ok';
      }

      $br_Data['DTE_'.$br_i] = $br_Debt_to_Equity[$br_i];
      $br_Data['R10_'.$br_i] = $br_R10[$br_i];
      $br_Data['R10S_'.$br_i] = $br_R10_Status[$br_i];
      //  Debt Service coverage (R11)
      $br_Debt_Service_Coverage[$br_i] = ($ratioData['pre_inv'][$br_i] + $this->fi_cdData['N_'.$br_i])/($ratioData['tot_int'][$br_i] + $ratioData['tot_repay'][$br_i]);

      $br_R11[$br_i] = $br_Debt_Service_Coverage[$br_i];

      //  R11_Status(j) = PB if R11(j) <1.3
      if($br_R11[$br_i] < 1.3){
        $br_R11_Status[$br_i] = 'Pb';
      }else{
        $br_R11_Status[$br_i] = 'Ok';
      }

      $br_Data['DSC_'.$br_i] = $br_Debt_Service_Coverage[$br_i];
      $br_Data['R11_'.$br_i] = $br_R11[$br_i];
      $br_Data['R11S_'.$br_i] = $br_R11_Status[$br_i];
      $br_ii = $br_i-1;
      //  ROR on Assets (R12)
      $br_ROR[$br_i] =  ($ratioData['tax_inc'][$br_i] - $ratioData['inc_tax'][$br_i] + $ratioData['tot_int'][$br_i]-$ratioData['short_int'][$br_i])/(($ratioData['net_fxd'][$br_i] + $ratioData['tin_Data']['WPL_'.$br_i] + $ratioData['net_fxd'][$br_ii] + $ratioData['tin_Data']['WPL_'.$br_ii])/2)*100;
      $br_R12[$br_i] = $br_ROR[$br_i];
      //  R12_Status(j) = PB if R12(j) <8.
      if($br_R12[$br_i] < 8){
        $br_R12_Status[$br_i] = 'Pb';
      }else{
        $br_R12_Status[$br_i] = 'Ok';
      }
      $br_Data['ROR_'.$br_i] = $br_ROR[$br_i];
      $br_Data['R12_'.$br_i] = $br_R12[$br_i];
      $br_Data['R12S_'.$br_i] = $br_R12_Status[$br_i];
    }
    $br_Data['sid']=1;

    $this->finplan->ctd->add($br_Data);
  }


  /**
   * 2.2.20.	Share Holders' Return module
   **/
  public function updateShareholdersReturn($returnData)
  {
    $di_cqData = $this->finplan->cqd->getByField('1','sid');//  get share holder return Data
    $di_DispYear = $di_cqData['Disposal_Year'];
    $di_DRate = $di_cqData['D_Rate'];
    $di_t = 1;// For using in power function

    $di_Total_Flow[$this->finplan->startYear-1] = -$returnData['eq_Data']['IE'];
    $irrarray[] = $di_Total_Flow[$this->finplan->startYear-1];

    for($di_i=$this->finplan->startYear;$di_i <= $di_DispYear; $di_i++){
      if($di_i==$di_cqData['Disposal_Year']){
        $di_Final_Disposal[$di_DispYear] = $returnData['tot_ast'][$di_DispYear] - $returnData['net_bnd'][$di_DispYear] - $returnData['net_loan'][$di_DispYear] - $returnData['dep_dec'][$di_DispYear] - $returnData['cur_mat'][$di_DispYear];
      }else{
        $di_Final_Disposal[$di_i] = 0;
      }
      //  Total_Flow(j) = - New_Equity_LC(j) + Tot_Dividend_LC(j) + Tot_Equity_Repay_LC (j) + Final_Disposal(j),  .
      $di_Total_Flow[$di_i] = - $this->fi_cdData['N_'.$di_i] + $returnData['tot_div'][$di_i] + $returnData['eq_Data']['E_'.$di_i] + $di_Final_Disposal[$di_i];
      $di_pow = pow((1+$di_DRate/100),$di_t);
      $di_Total_Flowsum = $di_Total_Flowsum + ($di_Total_Flow[$di_i]/$di_pow);
      $di_t++;

      $irrarray[] = $di_Total_Flow[$di_i];
      if ($returnData['net_equ'][$di_i-1] == 0) {
        $di_Return_OnEquity[$di_i] = 'n.a.';
      } else {
        $di_Return_OnEquity[$di_i] = ($returnData['tot_div'][$di_i]/$returnData['net_equ'][$di_i - 1])*100;
      }

      //division by zero
      $di_Return_OnAssets[$di_i] = ($returnData['net_equ'][$di_i]/max(0.0001, $returnData['tot_div'][$di_i]));

      $di_Data['FD_'.$di_i] =  $di_Final_Disposal[$di_i];
      $di_Data['TF_'.$di_i] = $di_Total_Flow[$di_i];
      $di_Data['RE_'.$di_i] = $di_Return_OnEquity[$di_i];
      $di_Data['RA_'.$di_i] = $di_Return_OnAssets[$di_i];

    }
    $di_Data['TF_'.($this->finplan->startYear-1)] = $di_Total_Flow[$this->finplan->startYear-1];
    $di_Data['TFS'] = $di_Total_Flowsum;
    $di_NPV_Project = - $returnData['eq_Data']['IE'] + $di_Total_Flowsum;
    $di_Data['NPV'] = $di_NPV_Project;
    //  Internal_ROR= IRR( Tot_Flow(j)),A_ROR, where j varies from First_Year to the D_Year.
    $di_AROR = $di_cqData['A_ROR'];
    $IRR = $this->finplan->fpmt->IRR($irrarray,$di_AROR);
    $di_Data['IRR'] = $IRR * 100;
    $di_Data['sid']=1;
    $this->finplan->csd->add($di_Data);
  }




}

?>
