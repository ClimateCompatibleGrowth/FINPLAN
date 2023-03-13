<?php
require_once CLASS_PATH . "Data.class.php";
$caseStudyId = $_COOKIE['titlecs'];
$cg = 'geninf_data';
$dc = new Data($caseStudyId, $cg);
$aaData = $dc->getRow();

$str = file_get_contents(ROOT_FOLDER . "/app/files.json");
$fileNames = json_decode($str, true);

foreach ($fileNames as $key => $value)
{
    ${$key} = $value;
}

$DefaultStudy = $aaData['studyName'];
$startYear = $aaData['startYear'];
$endYear = $aaData['endYear'];
$baseCurrency=$aaData['baseCurrency'];
$curTypeSel=$aaData['CurTypeSel'];
$AllYear=array();
for($n = $startYear;$n <= $endYear; $n++){
    $AllYear[]=$n;
}

class Config {
	private function __contruct(){}//so that we do not create instance of this class

	public static function getData($configName)
	{
		require_once(CLASS_PATH.'XmlParser.php');
		$xml = new XmlParser();
		$xml->LoadFile(COMMON_DATA_FILE_PATH.$configName.".".DATA_FILE_EXT);
		$dataArr = $xml->ToArray();

		// if (is_array($dataArr)){
		//   $key = each($dataArr);
		//   $key = $key['key'];
		//   return $dataArr[$key];
		// }
		return $dataArr[$configName];
	}
}

class Config1
{
    private function __construct()
    {
    }
    public static function getData($configName, $path, $common = false)
    {
        if ($common)
        {
            $a1=@file_get_contents(COMMON_DATA_FILE_PATH . $configName . "." . DATA_FILE_EXT);
        }
        else
        {
            $a1=@file_get_contents(USER_CASE_PATH . $path . "/" . $configName . "." . DATA_FILE_EXT);
        }

        $ob= simplexml_load_string($a1);
        $json = json_encode($ob);
        $array = json_decode($json,TRUE);
        $a=$array[$configName];
		if (count($a) === count($a, COUNT_RECURSIVE)) 
		{
			$a[0] = $a;
			$a = array_intersect_key($a, array_flip(array_filter(array_keys($a) , 'is_numeric')));
		}
        return $a;
    }
}
class Config2 {
	private function __contruct(){}//so that we do not create instance of this class

	public static function getData($configName, $id)
	{
		require_once(CLASS_PATH.'XmlParser.php');
		$xmldoc = new DOMDocument();
		$xmldoc->load(COMMON_DATA_FILE_PATH.$configName.".".DATA_FILE_EXT);
		$rows = $xmldoc->getElementsByTagName($configName);
		foreach($rows as $row){
			$rowIds = $row->getElementsByTagName('id');
			$rowId = $rowIds->item(0)->nodeValue;
			if($id == $rowId){
			  $rowVals = $row->getElementsByTagName('value');
			  $rowVal = $rowVals->item(0)->nodeValue;
			  return $rowVal;
			}
		}
		return false;
	}
}
?>
