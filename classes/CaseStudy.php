<?php

class CaseStudy{
	private $caseStudyId;
	private $directory_path;
	private $caseStudyData;

	public function __construct($id)
	{
	  $this->caseStudyId = $id;
	  $filename = sanitize_filename($id);
	  $this->directory_path = PROJECT_DATA_FILE_PATH.$filename;
	  if (!file_exists($this->directory_path))
	    mkdir($this->directory_path);
	    mkdir($this->directory_path.'/result/');
	}

	public function getData(){
		return $this->caseStudyData;
	}

	//add or update
	public function save($data){
	  $xmlData = XML_FILE_HEAD;
	  $xmlData .= '<row>';
	  $xmlData .= '<id>'.$this->caseStudyId.'</id>';
	  foreach($data as $k => $v){
	    $xmlData .= "<$k>$v</$k>";
	  }
	  $xmlData .= '</row>';

	  $fp = fopen($this->directory_path.'/geninf_data.'.DATA_FILE_EXT,'w');
	  fwrite($fp,$xmlData);
	  fclose($fp);
	  //	  return $ret;
	}

	//return all case studies in array form
	public static function getAllCaseStudies(){
		if ($handle = opendir(PROJECT_DATA_FILE_PATH)) {
			$caseStudies = array();
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file != '..' && is_dir(PROJECT_DATA_FILE_PATH.$file)){
					require_once(MODELS_PATH.'/XmlParser.php');
					$xml = new XmlParser();
					$gfile ='geninf_data';
					$xml->LoadFile(PROJECT_DATA_FILE_PATH.$file."/".$gfile.".".DATA_FILE_EXT);
					$dataArr = $xml->ToArray();
					if(is_array($dataArr) && $dataArr['id']){
					  $caseStudies[$dataArr['studyName']] = $dataArr;
					}
				}
			}
			closedir($handle);
			uksort($caseStudies,"strnatcasecmp");
			return $caseStudies;
		}
		return false;
	}

	//return all case studies in array form
	public static function getMaxId(){
		if ($handle = opendir(PROJECT_DATA_FILE_PATH)) {
			$maxId = 0;
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file != '..' && is_dir(PROJECT_DATA_FILE_PATH.$file)){
					if(is_numeric($file) && $file > $maxId){
						$maxId = $file;
					}
				}
			}
			return $maxId;
			closedir($handle);
		}
		return false;
	}
}
