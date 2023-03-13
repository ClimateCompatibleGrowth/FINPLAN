<?php

require_once(CLASS_PATH."Base.php");

/*
 * @todo
 * This class should be used for migrations (if any)
 */


class XmlKey extends Base {


  const KEY_START_YEAR = 'startYear';
  const KEY_END_YEAR = 'endYear';

  const KEY_RATE_TYPE = 'RateType';
  const KEY_STEADY_INF = 'SteadyInf'; //Steady Inflation
  //Inflation Types
  const KEY_I = 'I'; //Inflation Data
  const KEY_SR = 'SR'; //Steady Inflation
  const KEY_II = 'II'; //Inflation Index
  const KEY_YR = 'YR'; //Yearly Change


  const KEY_DELIMITER = "_";

  public static function getKey($parameters, $useDelimiter)
  {

    //return implode($useDelimiter? self::KEY_DELIMITER : '', $parameters);
    if ($useDelimiter) {
      return implode(self::KEY_DELIMITER, $parameters);
    } else {
      return implode('', $parameters);
    }
  }

}

?>
