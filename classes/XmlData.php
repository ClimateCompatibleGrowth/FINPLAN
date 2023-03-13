<?php

require_once(CLASS_PATH."Base.php");

class XmlData extends Base {
	private $xmlFile; //the file containing the data
	private $objectName; //name of the object equivalent to table name
	private $dom; //the php5 dom object
	private $caseStudyId;
	private $autoIncrement;

	public function __construct($caseStudyId, $objectName){
		$this->caseStudyId = $caseStudyId;
		$this->objectName = $objectName;
		$filechunk = explode("_", $objectName);//added for result folder
		$dir = USER_CASE_PATH.$this->caseStudyId;
		if (!is_dir($dir)) {
		mkdir($dir,0777);
		}
		if($filechunk[0] == 'cal'){//added for result folder
			$this->xmlFile =  USER_CASE_PATH.$this->caseStudyId.'/result/'.$this->objectName.'.'.DATA_FILE_EXT;
		}
		else{
			$this->xmlFile =  USER_CASE_PATH.$this->caseStudyId.'/'.$this->objectName.'.'.DATA_FILE_EXT;
		}
		if(!is_file($this->xmlFile)){//if does not exists create one
			$this->_create();
		}
		$this->dom = new DOMDocument();
		$this->dom->validateOnParse = true;
		$this->dom->load( $this->xmlFile);

		$this->autoIncrement = $this->dom->getElementsByTagName('meta')->item(0)->getElementsByTagName('autoIncrement')->item(0)->nodeValue;
	}

	//get row node (xml )for give id
	private function _getById($id){
		$rows = $this->_getAll();
		foreach($rows as $row){
			$rowIds = $row->getElementsByTagName("id");
			$rowId = $rowIds->item(0)->nodeValue;
			if($id == $rowId){
				return $row;
			}
		}
		return false;
	}

	private function _getByField($id,$fld){
		$rows = $this->_getAll();
		foreach($rows as $row){
			$rowIds = $row->getElementsByTagName($fld);
			$rowId = $rowIds->item(0)->nodeValue;
			if($id == $rowId){
				return $row;
			}
		}
		return false;
	}

	//return row nodes (xml)
	private function _getAll(){
		return $this->dom->getElementsByTagName('row');
	}

	private function _getAutoIncrementId(){
		return $this->autoIncrement;
	}

	private function _setAutoIncrementId($id){
		$this->autoIncrement = $id;
		$oa = $this->dom->getElementsByTagName('meta')->item(0)->getElementsByTagName('autoIncrement')->item(0);
		//pr($oa->nodeValue);
		//echo $this->dom->getElementsByTagName('meta')->item(0)->nodeValue;exit;
		$na = $this->dom->createElement('autoIncrement');
		$na->appendChild($this->dom->createTextNode($id));
		$oa->parentNode->replaceChild($na, $oa);
	}

	private function _create(){
		$xmlData = XML_FILE_HEAD;
		$xmlData .= '<'.$this->objectName.'><meta><autoIncrement>0</autoIncrement></meta><data></data></'.$this->objectName.'>';
		$fp = fopen($this->xmlFile,'w');
		fwrite($fp,$xmlData);
		fclose($fp);
	}

	//reload from xml
	public function reload(){
		$this->dom->load($this->xmlFile);
		$this->autoIncrement = $this->dom->getElementsByTagName('meta')->item(0)->getElementsByTagName('autoIncrement')->item(0)->nodeValue;
	}
	//get row data (array) for give id
	public function getById($id){
		$row = $this->_getById($id);
		$rowArr = array();

		$children = $row->childNodes;
		for($i=0;$i<$children->length;$i++) {
			$child = $children->item($i);
			$rowArr[$child->nodeName] = $child->nodeValue;
			if($child->nodeName == 'id'){
				$id = $child->nodeValue;
			}
		}
		return $rowArr;
	}

	public function getByField($id,$fld){
		$row = $this->_getByField($id,$fld);
		$rowArr = array();

		$children = $row->childNodes;
		for($i=0;$i<$children->length;$i++) {
			$child = $children->item($i);
			$rowArr[$child->nodeName] = $child->nodeValue;
			if($child->nodeName == $fld){
				$id = $child->nodeValue;
			}
		}
		return $rowArr;
	}

	/*
	@param - $data - associative array
	*/
	public function update($data){
		//get
		$row = $this->_getById($data['id']); pr($row);
		if($row !== false){
			foreach($data as $key=>$value){
				$ns = $row->getElementsByTagName($key);
				$n = $ns->item(0);
				$a = $this->dom->createElement($key);
				$a->appendChild($this->dom->createTextNode($value));
			//	if($n!=null)
				$n->parentNode->replaceChild($a, $n)  ;
			}
		}
		$this->saveXML();
	}

	public function updates($data,$fld){
		//get
		$row = $this->_getByField($data['pid'],'pid'); pr($row);
		if($row !== false){
			foreach($data as $key=>$value){
				$ns = $row->getElementsByTagName($key);
				$n = $ns->item(0);
				$a = $this->dom->createElement($key);
				$a->appendChild($this->dom->createTextNode($value));
				$n->parentNode->replaceChild($a, $n)  ;
			}
		}
		$this->saveXML();
	}

	public function endsWith($haystack, $needle) {
	    // search forward starting from end minus needle length characters
	    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	/*
	@param - $data - associative array
	*/
	public function add($data) {
		if (is_null($data)) return false;
		//get autoincrements id
		$id = $this->_getAutoIncrementId() + 1;

		$newRow = $this->dom->createElement("row");

		$n = $this->dom->createElement('id');
		$n->appendChild($this->dom->createTextNode($id));
		$newRow->appendChild($n);
		foreach($data as $key=>$value){
			if (!$this->endsWith('_',$key)) { //sanity check
				$n = $this->dom->createElement(htmlspecialchars($key)); //check whether necessary
				$n->appendChild($this->dom->createTextNode(htmlspecialchars($value)));
				$newRow->appendChild($n);
			}
		}

		$data = $this->dom->getElementsByTagName('data');
		$data->item(0)->appendChild($newRow);

		$this->_setAutoIncrementId($id);
		$this->formatOutput = true;
		$this->saveXML();
	}

	public function deleteById($id){
		$n = $this->_getById($id);
		if($n){
			$n->parentNode->removeChild($n);
		}
		$this->saveXML();
	}
	public function deleteByField($id,$fld){
		$n = $this->_getByField($id,$fld);
		if($n){
			$n->parentNode->removeChild($n);
		}
		$this->saveXML();
	}

	//return all data (array)
	public function getAll(){
		$rows = $this->_getAll();
		$rowsArr = array();

		foreach($rows as $row){
			$children = $row->childNodes;
			$rowArr = array();
			for($i=0;$i<$children->length;$i++) {
			  $child = $children->item($i);
			  $rowArr[$child->nodeName] = $child->nodeValue;
			  if($child->nodeName == 'id'){
			    $id = $child->nodeValue;
			  }
			}
			$rowsArr[$id] = $rowArr;
		}

		return $rowsArr;
	}

	public static function getAllCaseStudies(){
		if ($handle = opendir(PROJECT_DATA_FILE_PATH)) {
			$caseStudies = array();
			while (false !== ($file = readdir($handle))) {

				if($file != '.' && $file != '..' && is_dir(PROJECT_DATA_FILE_PATH.$file)){
					$xmldoc = new DOMDocument();
					$gfile ='geninf_data';
					$xmldoc->load(PROJECT_DATA_FILE_PATH.$file."/".$gfile.".".DATA_FILE_EXT);
					$rows = $xmldoc->getElementsByTagName('row');
					foreach($rows as $row){
					  $children = $row->childNodes;
					  $rowArr = array();

					  for($i=0;$i<$children->length;$i++) {
					    $child = $children->item($i);
					    $rowArr[$child->nodeName] = $child->nodeValue;
					    if($child->nodeName == 'id'){
					      $id = $child->nodeValue;
					    }
					  }
					  $rowsArr[$id] = $rowArr;
					}

				}
			}
			closedir($handle);
			return $rowsArr;
		}
		return false;
	}

	public function getAllbyField($sid,$fld){
		$rows = $this->_getAll();
		$rowsArr = array();

		foreach($rows as $row){
			$children = $row->childNodes;
			$rowArr = array();
			$rowIds = $row->getElementsByTagName($fld);
			$rowId = $rowIds->item(0)->nodeValue;
			if($sid == $rowId){
				for($i=0;$i<$children->length;$i++) {
				  $child = $children->item($i);
				  $rowArr[$child->nodeName] = $child->nodeValue;
				  if($child->nodeName == 'id'){
				    $id = $child->nodeValue;
				  }
				}
			$rowsArr[$id] = $rowArr;
			}
		}
		return $rowsArr;
	}

	public function saveXML(){
		$this->dom->formatOutput = true;
		$this->dom->save($this->xmlFile);
	}
}

?>
