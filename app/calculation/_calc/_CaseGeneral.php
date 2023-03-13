<?php

class CaseGeneral extends FinplanService {

  public $finplanData;

  public function __construct($finplanData)
  {
    $this->finplan = $finplanData;
  }

  /**
   * Update Inflation Information
   **/
  public function updateInflationInformation()
  {
  	$in_cajData = $this->finplan->ajd->getByField('1','sid'); //get Data for the id
  	$in_startyear = $this->finplan->startYear; // set value of start year to variable
  	$in_LIX[$in_startyear-1] = 1; // set start year inflation index to 1
    $in_Data = Array();
  	$in_Data['sid'] = 1; // set the sid to 1 for storing infl index
  	for($in_c = 0; $in_c < count($this->finplan->allChunks); $in_c++){// loop to get each currency id
  		$in_CX = $this->finplan->allChunks[$in_c];// get currency id
  		$in_rtype = XmlKey::getKey(Array(XmlKey::KEY_RATE_TYPE, $in_CX), false);
      if (array_key_exists($in_rtype, $in_cajData)) {
        $in_inftype = $in_cajData[$in_rtype];// get the inflation type given by user
      } else {
        $in_inftype = 0;
      }
  		for($in_i=$in_startyear;$in_i <= $this->finplan->endYear; $in_i++) {
        $in_CY = XmlKey::getKey(Array($in_CX, $in_i), true);
        if($in_inftype ==XmlKey::KEY_SR) { //if steady change
  				$in_VX = XmlKey::getKey(Array(XmlKey::KEY_STEADY_INF, $in_CX), true);
          if (array_key_exists($in_VX, $in_cajData)) {
            $in_LIF[$in_i] = $in_cajData[$in_VX];
          } else {
            $in_LIF[$in_i] = 0;
          }
  			} elseif ($in_inftype == XmlKey::KEY_YR) { // if Year rate change
  				if ($in_cajData[$in_CY] =='') {
  					$in_LIF[$in_i] = $in_LIF[$in_i-1] ;
  				} else {
  					$in_LIF[$in_i] = $in_cajData[$in_CY];
  				}
  			}
  			$in_LIX[$in_i] = $in_LIX[$in_i-1] * (1 + $in_LIF[$in_i] / 100);
  			$in_INF = XmlKey::getKey(Array(XmlKey::KEY_I, $in_CY), true);
  			$in_Data[$in_INF] = $in_LIF[$in_i];
  			$in_Data[$in_CY] = $in_LIX[$in_i];
  		}
  	}

    $this->finplan->add->add($in_Data);
  }

  /**
   * Update Currency Exchange Rates
   **/
  public function updateCurrencyExchangeRates()
  {
  	$ex_cbData = $this->finplan->aid->getByField('1','sid');//get exchange Data for this id
    $inf_cbData = $this->finplan->getInflationData();

    $ex_Data = Array();
    $ex_Data['sid'] = 1;
    $ex_LIX = Array();

  	for($c = 0; $c < count($this->finplan->allChunks); $c++) {
  		$ex_CX = $this->finplan->allChunks[$c];
  		$ex_rtype = XmlKey::getKey(Array(XmlKey::KEY_RATE_TYPE, $ex_CX), false);
  		$ex_inftype = $ex_cbData[$ex_rtype];// get inflation type for this currency
  		$ex_strtval = XmlKey::getKey(Array($ex_CX, $this->finplan->startYear), true);
  		$ex_startcurr = $ex_cbData[$ex_strtval]; // get inflation value for this currency for start year
  		$ex_prevyear = $this->finplan->startYear-1;
      $ex_key = XmlKey::getKey(Array($ex_CX, $ex_prevyear), true);
  		if($ex_CX == $this->finplan->baseCurrency) {
  			$ex_Data[$ex_key] = 1;
  		} else {
  			$ex_Data[$ex_key] = $ex_cbData[$ex_key];
  		}
  		for($i=$this->finplan->startYear;$i <= $this->finplan->endYear; $i++) {
  			$ex_CY = XmlKey::getKey(Array($ex_CX, $i), true);
  			if($ex_CX == $this->finplan->baseCurrency) {
  				$ex_LIX[$i] = 1;
  			} else {
  				if($ex_inftype == XmlKey::KEY_SR) { // If inflation type = steady
  					$ex_FCV = XmlKey::getKey(Array('YearlyRate', $ex_CX), false);
  					$ex_LIF[$i] = $ex_cbData[$ex_FCV];// get steady change rate
  					$ex_LIX[$i] = $ex_LIX[$i-1]+($ex_LIX[$i-1]*$ex_cbData[$ex_FCV]/100);//assign steady change rate
  				} elseif($ex_inftype == XmlKey::KEY_YR) { // If inflation type = yearly

  					if($ex_cbData[$ex_CY] =='') { //if not given for this year get value from previous year
  						$ex_LIF[$i] = $ex_LIF[$i-1] ;
  					}else {
  						$ex_LIF[$i] = $ex_cbData[$ex_CY];//for this year get value from stored values in xml
  					}
  					$ex_LIX[$i] = $ex_LIF[$i];
  				}elseif($ex_inftype == XmlKey::KEY_II) { // If inflation type = inflation index
  					$ex_BC = $this->finplan->baseCurrency.'_'.$i;
            $ex_loc_key = XmlKey::getKey(Array(XmlKey::KEY_I, $ex_BC), true);
            $ex_for_key = XmlKey::getKey(Array(XmlKey::KEY_I, $ex_CY), true);
  					$ex_locindx = $inf_cbData[XmlKey::KEY_I.'_'.$ex_BC];//get inflation index for base currency for this year
  					$ex_forindx = $inf_cbData[XmlKey::KEY_I.'_'.$ex_CY];//get inflation index for this currency for this year
  					$ex_LIX[$this->finplan->startYear-1] = $ex_startcurr;// set start year -1 = first year value
  					$ex_LIX[$i] = $ex_LIX[$i-1] * (1+(($ex_locindx/100) - ($ex_forindx/100)));// exchange index (i-1) * (1+(loc Infl indx - for infl indx)
  				}
  				$ex_LIX[$this->finplan->startYear]=$ex_startcurr;	// start year is assigned the value of 1st year given by user input
  			}
  		$ex_Data[$ex_CY]=$ex_LIX[$i];
  		}
  	}
    // echo "ex_Data = ";
    // print_r($ex_Data);
    $this->finplan->acd->add($ex_Data);

  }

}


?>
