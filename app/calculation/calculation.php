<?php
	include("../../config.php");
	include("../../general.php");
	require_once(CLASS_PATH."XmlData.php");
	require_once(CLASS_PATH."/CaseStudy.php");
	require_once("label.php");
	require_once(CLASS_PATH."/XmlKey.php");
	require_once(CLASS_PATH."/XmlCollection.php");
	require_once("Financial.php");
	//ini_set('display_startup_errors', 1);
	//ini_set('display_errors', 1);
	//error_reporting(-1);
	$finplanService = new FinplanService($caseStudyId);
	$finplanService->runCalculations();


	class FinplanService {

		private $caseStudyId;
		public $finplan;
	  
		public function __construct($caseStudyId)
		{
	  
		  $this->caseStudyId = $caseStudyId;
		  $files = glob(USER_CASE_PATH.$this->caseStudyId.'/result/cal_*.xml'); // get all file names
		  foreach($files as $file){ // iterate files
				if(is_file($file)) {
				  unlink($file); // delete file
				}
		  }
		  
		  $this->finplan = new Finplan($this->caseStudyId);
	  
		  foreach (glob(BASE_PATH."/app/calculation/_calc/*") as $filename) {
			require_once($filename);
		  }

		  $files = glob(USER_CASE_PATH.$this->caseStudyId.'/result/cal_*.xml'); // get all file names
			foreach($files as $file){ // iterate files
  				if(is_file($file)) {
  				  unlink($file); // delete file
  				}
			}
	  
		}
	  
		public function runCalculations()
		{
	  
		  $caseGeneral = new CaseGeneral($this->finplan);
			$caseGeneral->updateInflationInformation();
			$caseGeneral->updateCurrencyExchangeRates();
	  
			$plantInvestment = new PlantInvestment($this->finplan);
			$plantInvestment->updateIvestmentInformation();
			$plantInvestment->updateVat();
			$totalInvestmentData = $plantInvestment->updateTotals();
			$fixedAssets = $plantInvestment->updateDepreciation($totalInvestmentData);
			$plantInvestment->updateDrawDown();
	  
			$balanceLoan = new BalanceLoan($this->finplan);
			$balanceLoan->updateBalanceExport();
			$totalExportCreditData = $balanceLoan->updateExportCredit();
			$balanceLoan->updateBalanceLoan();
	  
			$purchaseLoan = new PurchaseLoan($this->finplan);
			$totalProjectLoanData = $purchaseLoan->updateTotalProjectLoan();
			$salesData = $purchaseLoan->updateSales();
			$purchaseData = $purchaseLoan->updatePurchase();
			$oldLoansData = $purchaseLoan->updateOldLoans();
			$purchaseLoan->updateNewCommercialLoans();
	  
			$bondEquity = new BondEquity($this->finplan);
			$oldBondsData = $bondEquity->updateOldBonds($oldLoansData);
			$newBondsData = $bondEquity->updateNewBonds();
			$equityData = $bondEquity->updateEquity();
	  
			$costRevenue = new CostRevenue($this->finplan);
			$costRevenue->updateOperationMaintenanceCosts();
			$costRevenue->updateFuelCosts();
		  $totalFuelCost = $costRevenue->updateTotalFuelCosts();
			$costRevenue->updateGeneralExpenseCosts();
			$totalGeneralExpenseCost = $costRevenue->updateTotalGeneralExpenseCosts();
			$costRevenue->updateFixedRevenues();
			$costRevenue->updateGlobalInvestments();
			$totalDepreciation = $costRevenue->updateDepreciation($fixedAssets);
			$decommissioningData = $costRevenue->updateDecommissioning();
			$totalOperationMaintenanceCost = $costRevenue->updateTotalOperationMaintenanceCosts();
			$costRevenue->updateTotalRevenue();
			$costRevenue->updateRoyalties();
			$costRevenue->updateProduction();
	  
			$exchangeSource = new ExchangeSource($this->finplan);
			$exchangeData = $exchangeSource->updateForeignExchange($newBondsData['bo_CY']);
		  $sourcesData = Array(
			'dp_Data2'      =>  $totalDepreciation,
			'ol_Data'       =>  $oldLoansData,
			'ob_Data'       =>  $oldBondsData,
			'feData'        =>  $exchangeData,
			'eq_drateval'   =>  $equityData['eq_drateval'],
			'eq_totout'     =>  $equityData['eq_totout']
		  );
			$fundsData = $exchangeSource->updateSourcesFunds($sourcesData);
		  $balanceData = Array(
			'eq_Data'       =>  $equityData['eq_Data'],
			'feData'        =>  $exchangeData,
			'ob_Data'       =>  $oldBondsData,
			'ol_Data'       =>  $oldLoansData
		  );
			$exchangeSource->updateBalanceSheet($balanceData);
	  
			$assetRisk = new AssetRisk($this->finplan);
		  $assetData = Array(
			'tex_Data'      =>  $totalExportCreditData,
			'tin_Data'      =>  $totalInvestmentData,
			'de_Data'       =>  $decommissioningData,
			'dp_Data2'      =>  $totalDepreciation,
			'bo_Data'       =>  $newBondsData['bo_Data'],
			'ob_Data'       =>  $oldBondsData,
			'ol_Data'       =>  $oldLoansData,
			'eq_Data'       =>  $equityData['eq_Data'],
			'eq_totout'     =>  $equityData['eq_totout'],
			'ret_earn'      =>  $fundsData['ret_earn'],
			'short_loans'   =>  $fundsData['short_loans'],
			'short_dep'     =>  $fundsData['short_dep'],
			'cml_earn'      =>  $fundsData['cml_earn'],
			'tcl_Data'      =>  $totalProjectLoanData
		  );
			$liabilityData = $assetRisk->updateAssetLiability($assetData);
			$assetRisk->updateRiskAnalysis($fundsData['fi_Data']);
		  $ratioData = Array(
			'tex_Data'      =>  $totalExportCreditData,
			'tin_Data'      =>  $totalInvestmentData,
			'ob_Data'       =>  $oldBondsData,
			'ol_Data'       =>  $oldLoansData,
			'bo_Data'       =>  $newBondsData['bo_Data'],
			'tot_int'       =>  $fundsData['tot_int'],
			'tot_repay'     =>  $fundsData['tot_repay'],
			'short_int'     =>  $fundsData['short_int'],
			'pre_inv'       =>  $fundsData['pre_inv'],
			'tax_inc'       =>  $fundsData['tax_inc'],
			'inc_tax'       =>  $fundsData['inc_tax'],
			'tfc_Data'      =>  $totalFuelCost,
			'tom_Data'      =>  $totalOperationMaintenanceCost,
			'tge_Data'      =>  $totalGeneralExpenseCost,
			'pr_Data2'      =>  $purchaseData,
			'tcl_Data'      =>  $totalProjectLoanData,
			'net_equ'       =>  $liabilityData['net_equ'],
			'net_loan'      =>  $liabilityData['net_loan'],
			'cml_ret'       =>  $liabilityData['cml_ret'],
			'net_bnd'       =>  $liabilityData['net_bnd'],
			'grs_fxd'       =>  $liabilityData['grs_fxd'],
			'net_fxd'       =>  $liabilityData['net_fxd'],
			'con_ctb'       =>  $liabilityData['con_ctb'],
			'tot_ast'       =>  $liabilityData['tot_ast'],
			'fi_Data'       =>  $fundsData['fi_Data'],
			'sl_Data2'      =>  $salesData
		  );
	  
			$assetRisk->updateFinancialRatios($ratioData);
		  $returnData = Array(
			'eq_Data'       =>  $equityData['eq_Data'],
			'tot_div'       =>  $fundsData['tot_div'],
			'net_equ'       =>  $liabilityData['net_equ'],
			'net_bnd'       =>  $liabilityData['net_bnd'],
			'net_loan'      =>  $liabilityData['net_loan'],
			'cur_mat'       =>  $liabilityData['cur_mat'],
			'dep_dec'       =>  $liabilityData['dep_dec'],
			'tot_ast'       =>  $liabilityData['tot_ast']
		  );
	  
			$assetRisk->updateShareholdersReturn($returnData);
		}
	  
	  }

	  class Finplan {

		//XML file names
		const XML_LOANS = 'loans_data';
		const XML_AAXML = 'assetliability_data';
		const XML_AHXML = 'geninf_data';
		const XML_AIXML = 'exchange_data';
		const XML_AJXML = 'inflation_data';
		const XML_AKXML = 'decomm_data';
		const XML_ALXML = 'depreciation_data';
		const XML_AMXML = 'fuelcost_data';
		const XML_ANXML = 'investment_data';
		const XML_AOXML = 'OM_data';
		const XML_APXML = 'plant_data';
		const XML_AQXML = 'product_data';
		const XML_ARXML = 'purchase_data';
		const XML_ASXML = 'source_finance2';
		const XML_ATXML = 'taxinfo_data';
		const XML_AUXML = 'termfinance_data';
		const XML_BLXML = 'equity_data';
		const XML_BMXML = 'bonds_data';
		const XML_BNXML = 'project_data';
		const XML_BOXML = 'otherfin_data';
		const XML_BRXML = 'comminvest_data';
		const XML_BSXML = 'conscontrib_data';
		const XML_BTXML = 'royalty_data';
		const XML_BUXML = 'genexpense_data';
		const XML_CQXML = 'shareholder_data';
		const XML_CUXML = 'fixrevotrinc';
		const XML_BVXML = 'oldloans_data';
		const XML_BXXML = 'oldbonds_data';
		const XML_AVXML = 'cal_balance';
		const XML_AYXML = 'cal_sale';
		const XML_AZXML = 'sale_data';
		const XML_BAXML = 'cal_purchase';
		const XML_BBXML = 'cal_om';
		const XML_BCXML = 'cal_fuelcost';
		const XML_BDXML = 'cal_totalomcost';
		const XML_BEXML = 'cal_totalfuelcost';
		const XML_BFXML = 'cal_globalinvst';
		const XML_BGXML = 'cal_depreciation';
		const XML_BHXML = 'cal_purchaseprod';
		const XML_BIXML = 'cal_production';
		const XML_BJXML = 'cal_totalpurchase';// need to remove this
		const XML_BKXML = 'cal_totalsale';
		const XML_BPXML = 'cal_debitdrawdown';
		const XML_BQXML = 'cal_debitother';
		const XML_BYXML = 'cal_genexpense';
		const XML_BZXML = 'cal_totalgenexpense';
		const XML_CAXML = 'cal_oldloans';
		const XML_CBXML = 'cal_oldbonds';
		const XML_CCXML = 'cal_bonds';
		const XML_CDXML = 'cal_equity';
		const XML_CEXML = 'cal_deccom';
		const XML_CFXML = 'cal_production';
		const XML_CGXML = 'cal_totalpurchase';
		const XML_CHXML = 'cal_totalexport';
		const XML_CIXML = 'cal_totalcommloan';
		const XML_CJXML = 'cal_totalrevenue';
		const XML_CKXML = 'cal_royalty';
		const XML_CLXML = 'cal_forexchange';
		const XML_CMXML = 'cal_vat';
		const XML_CNXML = 'cal_SAoffunds';
		const XML_COXML = 'cal_AELmodule';
		const XML_CPXML = 'cal_balancesheet';
		const XML_CRXML = 'cal_risk';
		const XML_CSXML = 'cal_shareholder';
		const XML_CTXML = 'cal_financialratios';
		const XML_CVXML = 'cal_revenues';
		const XML_ABXML = 'cal_Drawdown';
		const XML_ACXML = 'cal_Exchange';
		const XML_ADXML = 'cal_Inflation';
		const XML_AEXML = 'cal_Investment';
		const XML_AFXML = 'cal_commloan';
		const XML_AGXML = 'cal_TotalInvestment';
		const XML_CAL_LOANS = 'cal_loans';
		const XML_CG = 'geninf_data';
	
		public $currencies;
		public $studyTypes;
		public $financeSources;
		public $xmlData;
	
		public $startYear;
		public $endYear;
		public $caseStudyName;
		public $baseCurrency;
		public $baseCurrencyName;
		public $allChunks;
		public $baseChunks;
		public $base1Chunks;
		public $curChunks;
	
		private $caseStudyId;
	
		public $fpmt;
	
	
		public function __construct($caseStudyId)
		{
			$this->currencies = Config::getData('currencies');
			$this->studyTypes = Config::getData('studytypes');
			$this->financeSources = Config::getData('financesource');
			$this->caseStudyId = $caseStudyId; //$_SESSION['cs']['id'];
	
			//get general information
			$this->xmlData = new XmlCollection($caseStudyId, self::XML_CG);
			$this->xmlCollection = $this->xmlData->getoneById();
	
			$this->startYear = array_key_exists(XmlKey::KEY_START_YEAR, $this->xmlCollection) ? $this->xmlCollection['startYear'] : null;
			$this->endYear = array_key_exists(XmlKey::KEY_END_YEAR, $this->xmlCollection) ? $this->xmlCollection['endYear'] : null;
			$this->caseStudyName = array_key_exists('studyName', $this->xmlCollection) ? $this->xmlCollection['studyName'] : null;
			$this->baseCurrency = array_key_exists('baseCurrency', $this->xmlCollection) ? $this->xmlCollection['baseCurrency'] : null;
	
			$this->baseCurrencyName = Config2::getData('currencies',$this->baseCurrency);
	
			$forCurr = $this->xmlCollection['CurTypeSel'];
			if($forCurr){
				$bothCurr = $forCurr;
				$bothCurr .= ',';
				$bothCurr .= $this->baseCurrency;
				$base1Curr = $this->baseCurrency.','.$forCurr;
				$this->curChunks = explode(",", $forCurr);
			}else{
				$bothCurr = $baseCurr;
				$base1Curr = $baseCurr;
			}
			$this->allChunks = explode(",", $bothCurr);
			$this->base1Chunks = explode(",", $base1Curr);
			$this->baseChunks = explode(",", $this->baseCurrency);
			//Defining width for some tables
			$totcurr = count($this->curChunks);
			$wide = $totcurr * 200;
			$wide = $wide + 500;
			$RoundPlace = 2;// Variable to define the round value for reports
			$newwindow =  ""; // Set '?viewid=1' for report without css
			$targetwindow = ""; // For report to open in new page value for target
			$NoYears = $this->xmlCollection['endYear'] - $this->xmlCollection['startYear'];
	
			//$ahd = new XmlData($caseStudyId,$ahxml);
	
			$xmlDataMap = Array(
				'apd'			=>	self::XML_APXML,
				'add'			=>	self::XML_ADXML,
				'ajd'			=>	self::XML_AJXML,
				'aid'			=>	self::XML_AIXML,
				'acd'			=>	self::XML_ACXML,
				'atd'			=>	self::XML_ATXML,
				'aad'			=>	self::XML_AAXML,
				'aed'			=>	self::XML_AEXML,
				'and'			=>	self::XML_ANXML,
				'cmd'			=>	self::XML_CMXML,
				'agd'			=>	self::XML_AGXML,
				'brd'			=>	self::XML_BRXML,
				'abd'			=>	self::XML_ABXML,
				'asd'			=>	self::XML_ASXML,
				'bpd'			=>	self::XML_BPXML,
				'aud'			=>	self::XML_AUXML,
				'avd'			=>	self::XML_AVXML,
				'bqd'			=>	self::XML_BQXML,
				'chd'			=>	self::XML_CHXML,
				'afd'			=>	self::XML_AFXML,
				'cid'			=>	self::XML_CIXML,
				'ayd'			=>	self::XML_AYXML,
				'azd'			=>	self::XML_AZXML,
				'bkd'			=>	self::XML_BKXML,
				'ard'			=>	self::XML_ARXML,
				'bad'			=>	self::XML_BAXML,
				'cgd'			=>	self::XML_CGXML,
				'bvd'			=>	self::XML_BVXML,
				'cad'			=>	self::XML_CAXML,
				'bod'			=>	self::XML_BOXML,
				'lns'			=>	self::XML_LOANS,
				'cln'			=>	self::XML_CAL_LOANS,
				'bxd'			=>	self::XML_BXXML,
				'cbd'			=>	self::XML_CBXML,
				'bmd'			=>	self::XML_BMXML,
				'ccd'			=>	self::XML_CCXML,
				'aad'			=>	self::XML_AAXML,
				'bld'			=>	self::XML_BLXML,
				'cdd'			=>	self::XML_CDXML,
				'aod'			=>	self::XML_AOXML,
				'bbd'			=>	self::XML_BBXML,
				'amd'			=>	self::XML_AMXML,
				'bcd'			=>	self::XML_BCXML,
				'bed'			=>	self::XML_BEXML,
				'bud'			=>	self::XML_BUXML,
				'byd'			=>	self::XML_BYXML,
				'bzd'			=>	self::XML_BZXML,
				'cud'			=>	self::XML_CUXML,
				'cvd'			=>	self::XML_CVXML,
				'bfd'			=>	self::XML_BFXML,
				'ald'			=>	self::XML_ALXML,
				'bgd'			=>	self::XML_BGXML,
				'akd'			=>	self::XML_AKXML,
				'ced'			=>	self::XML_CEXML,
				'bdd'			=>	self::XML_BDXML,
				'cjd'			=>	self::XML_CJXML,
				'ckd'			=>	self::XML_CKXML,
				'btd'			=>	self::XML_BTXML,
				'aqd'			=>	self::XML_AQXML,
				'cfd'			=>	self::XML_CFXML,
				'cld'			=>	self::XML_CLXML,
				'bsd'			=>	self::XML_BSXML,
				'cnd'			=>	self::XML_CNXML,
				'cpd'			=>	self::XML_CPXML,
				'cod'			=>	self::XML_COXML,
				'crd'			=>	self::XML_CRXML,
				'bnd'			=>	self::XML_BNXML,
				'ctd'			=>	self::XML_CTXML,
				'cqd'			=>	self::XML_CQXML,
				'csd'			=>	self::XML_CSXML
			);
	
			foreach ($xmlDataMap as $key => $value) {
				$this->$key = new XmlData($this->caseStudyId, $value);
			}
	
			$this->fpmt = new Financial;
		}
	
		/**
	   * @return Plant Data (xml fields)
	   **/
		public function getCaseStudyId()
	  {
		return $this->finplan->caseStudyId;
	  }
	
		/**
	   * @return Plant Data (xml fields)
	   **/
		public function getPlantData()
	  {
		return $this->apd->getAll();
	  }
	
		/**
	   * @return Inflation Data (xml fields)
	   **/
	  public function getInflationData()
	  {
		return $this->add->getByField('1','sid');
	  }
	
		/**
	   * @return Exchange Data (xml fields)
	   **/
		public function getExchangeData()
		{
			return $this->acd->getByField('1','sid');
		}
	
		/**
	   * @return Total Operation and Maintenance Costs (xml fields)
	   **/
		public function getTotalOmCost()
		{
			return $this->bdd->getByField('1','sid');
		}
	
		/**
	   * @return Total Fuel Costs (xml fields)
	   **/
		public function getTotalFuelCost()
		{
			return $this->bed->getByField('1','sid');
		}
	
		/**
	   * @return Total General Expense (xml fields)
	   **/
		public function getTotalGeneralExpense()
		{
			return $this->bzd->getByField('1','sid');
		}
	
		/**
	   * @return Total Sale (xml fields)
	   **/
		public function getTotalPurchase()
		{
			return $this->cgd->getByField('1','sid');
		}
	
		/**
	   * @return Total Sale (xml fields)
	   **/
		public function getTotalSale()
		{
			return $this->bkd->getByField('1','sid');
		}
	
		/**
	   * @return Old Loans (xml fields)
	   **/
		public function getOldLoans()
		{
			return $this->bvd->getByField('1','sid');
		}
	
		/**
	   * @return Bonds (xml fields)
	   **/
		public function getBonds()
		{
			return $this->bmd->getByField('1','sid');
		}
	
		/**
	   * @return Bonds (xml fields)
	   **/
		public function getOldBonds()
		{
			return $this->bxd->getByField('1','sid');
		}
	
		/**
	   * @return Other Financial Data (xml fields)
	   **/
		public function getOtherFinancialData()
		{
			return $this->bod->getByField('1','sid');
		}
	
		/**
	   * @return Loan Calculations (xml fields)
	   **/
		public function getLoanCalculations()
		{
			return $this->cln->getByField('1','sid');
		}
	
		/**
	   * @return Balance Data (xml fields)
	   **/
		public function getBalanceData()
		{
			return $this->aad->getByField('1','sid');
		}
	
		/**
	   * @return Total Investment (xml fields)
	   **/
		public function getTotalInvestment()
		{
			return $this->agd->getByField('1','sid');
		}
	
		/**
	   * @return Contribution Data (xml fields)
	   **/
		public function getContributionData()
		{
			return $this->bsd->getByField('1','sid');
		}
	
	}

?>
