<?php

require_once(CLASS_PATH."Base.php");
class XmlCollection extends Base{
	private $xmlFile; //the file containing the data
	private $objectName; //name of the object equivalent to table name
	private $dom; //the php5 dom object
	private $caseStudyId;


	public function __construct($caseStudyId, $objectName){
		$this->caseStudyId = $caseStudyId;
		$this->objectName = $objectName;		
		$dir = USER_CASE_PATH.$this->caseStudyId;
		$this->xmlFile = USER_CASE_PATH.$this->caseStudyId.'/'.$this->objectName.'.'.DATA_FILE_EXT;
		$this->dom = new DOMDocument();
		$this->dom->validateOnParse = true;
		$this->dom->load( $this->xmlFile);

	}

	//get row node (xml )for give id

	private function _getoneById(){
		$rows = $this->_getAll();
		foreach($rows as $row){
			$rowIds = $row->getElementsByTagName("id");
			$rowId = $rowIds->item(0)->nodeValue;
			return $row;

		}
		return false;
	}
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

	//return row nodes (xml)
	private function _getAll(){
		return $this->dom->getElementsByTagName('row');
	}


	//get row data (array) for give id


	//This is to get information from xml containing only one row and without id
		public function getoneById(){
		$row = $this->_getoneById();
		$rowArr = array();

		$children = $row->childNodes;
		for($i=0;$i<$children->length;$i++) {
			$child = $children->item($i);
			$rowArr[$child->nodeName] = $child->nodeValue;
			$id = $child->nodeValue;

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
				$n->parentNode->replaceChild($a, $n)  ;
			}
		}
		$this->saveXML();
	}


	/*
	@param - $data - associative array
	*/

	//return all data (array)

	public function saveXML(){
		$this->dom->save($this->xmlFile);
	}

}

function pr($v){
	//disabled for prod
	//echo "<pre>";
	//print_r($v);
	//echo "</pre>";
	return true;
}

?>
