<?php
/**
 * This file contains functions realted to config vars for site
 * Date : Jan 18 2009
 * Description: This file parse generic XML file used for read only purpose. The root node must be same as that of data file name
 */

class Config {
	private function __contruct(){}//so that we do not create instance of this class

	public static function getData($configName)
	{
		require_once(CLASS_PATH.'XmlParser.php');
		$xml = new XmlParser();
		$xml->LoadFile(COMMON_DATA_FILE_PATH.$configName.".".DATA_FILE_EXT);
		$dataArr = $xml->ToArray();
		if (is_array($dataArr)){
		  $key = each($dataArr);

		  $key = $key['key'];
		  return $dataArr[$key];
		}
		return $dataArr;
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
