<?php

require_once 'music_fmview.php';
require_once 'FileMaker.php';
//require_once "error.php";
//require_once "Date.php";
//
//session_name("Shellac_Music_List_1336769717638");
//
//session_start();
//require_once "error.php";
//require_once "Date.php";
//
//define('DEBUG', 0);
//
//// formats for dates and times
//$displayDateFormat = '%m/%d/%Y';
//$displayTimeFormat = '%I:%M:%S %P';
//$displayDateTimeFormat = '%m/%d/%Y %I:%M:%S %P';
//$submitDateOrder = 'mdy';

class EmptyRecord {
    function getRelatedSet($relationName) {
        return array(new EmptyRecord());
    }

    function getField($field, $repetition = 0) {
    }

    function getRecordId() {
    }
}


//class CGI {
//
//	function get($property) {
//
//
//		if($property == "userName"){
//			return "Web";
//		}
//		if($property == "passWord"){
//			return "web";
//		}
//		if (isset ($_SESSION[$property]))
//			return $_SESSION[$property];
//		else
//			return NULL;
//	}
//
//	function testActionRequest($testvalue) {
//		$result = (isset ($_REQUEST['-action']) && $_REQUEST['-action'] == $testvalue);
//		return $result;
//	}
//
//	function storeFile() {
//		$path = $_SERVER['PHP_SELF'];
//		$nodes = split('/', $path);
//		$this->store('file', $nodes[count($nodes)-1]);
//	}
//	
//	function checkStoredFile() {
//		if(isset($_SESSION)){
//			if(array_key_exists('file', $_SESSION)){
//				$f = $_SESSION['file'];
//				$pos = strpos($f, '?');
//				if(!($pos === false)){
//					$f = substr($f, 0, $pos - 1);
//				}
//				if($f == 'authentication.php'){
//					$this->store('file', 'home.php?');
//				}
//			}
//			else{
//				$this->store('file', 'home.php?');
//			}
//		}
//	}
//		
//	function store($property, $value) {
//		if ($property == '-delete') {
//			$_SESSION['-action'] = 'delete';
//		} elseif ($property == '-duplicate') {
//			$_SESSION['-action'] = 'duplicate';
//		} else
//			$_SESSION[$property] = $value;
//	}
//
//	function clear($property) {
//		unset ($_SESSION[$property]);
//	}
//
//	function __construct() {
//
//	//	 Request parameters are saved in the session and accessed via the CGI.
//
//		foreach ($_GET as $key => $value) {
//			$this->store($key, $value);
//		}
//
//	//  the record data submitted
//
//		$recordData = array ();
//
//		foreach ($_POST as $key => $value) {
//
//		/*  If a key does not start with '-' then it is a field parameter.
//		Capture the field value pairs in a record data array
//		and store it in the session separately under the key 'storedfindrequest'
//		when handling a '-find' request, or in the 'recorddata' key for '-edit' or '-new'  */
//			
//			if(strpos($key, '-', 0) === 0){
//   				 $isCommand = true;
//			}else{
//   				 $isCommand = false;
//			}
//
//			if ($key === "userName" || $key === "passWord" || $isCommand ) {
//				$this->store($key, $value);
//			} else {
//				$recordData[$key] = $value;
//			}
//		}
//
//	//  get the field names
//
//		$fieldEditRecords = $this->get("fieldEditRecords");
//
//	//  always replace the existing find request
//
//		if ($this->testActionRequest("find")) {
//		
//		// formats for dates and times
//			$displayDateFormat = '%m/%d/%Y';
//
//			if (isset($fieldEditRecords) === true) {
//
//			// 	move the submitted data to the stored find request; an array, keys: field names, values: submitted query
//
//				$storedFind = array();
//				foreach ($recordData as $index => $value) {
//						if(!($index % 2 == 0)){
//				 		//Convert any time/date formatted field 
//                            if(array_key_exists($index, $fieldEditRecords)){
//                                $fieldEditRecord = $fieldEditRecords[$index];
//                            }
//                    
//                            if (is_null($fieldEditRecord) === false && $fieldEditRecord->isEditBox()){
//                                $resultType = $fieldEditRecord->getResultType();
//                                    if ($resultType == "date")
//                                        $value = submitDate($value, $displayDateFormat);
//                                    else if ($resultType == "timestamp")
//                                        $value = submitTimeStampForSearch($value, $displayDateFormat);
//							}
//                     }
//                     $storedFind[$fieldEditRecords[$index]->getFieldName()] = $value;
//                }     
//				$this->store('storedfindrequest', $storedFind);
//			}
//
//	// clear it for a findall
//
//		} else {
//			if ($this->testActionRequest("findall"))
//				$this->clear('storedfindrequest');
//
//		// 	store edit or new request record data
//
//			else {
//				if ($this->testActionRequest("edit") || $this->testActionRequest("new")) {
//					$this->store('recorddata', $recordData);
//				} else {
//
//				// 	clear out recorddata if not an edit
//
//					$this->clear('recorddata');
//				}
//			}
//		}
//	}
//
//	function reset() {
//		$this->clear('recorddata');
//		$this->clear('storedfindrequest');
//		$this->clear('fieldEditRecords');
//		$_SESSION = array();
//	}
//}	// CGI
//
//
//class FieldEditRecord {
//	private $_fieldName;
//	private $_repetition;
//	private $_recID;
//	private $_submittedValue = null;
//	private $_isEditable = true;
//	private $_style;
//	private $_resultType;
//
//	function FieldEditRecord ($name, $rep, $rec, $isEditable, $style, $resultType) {
//		$this->_fieldName = $name;
//		$this->_repetition = $rep;
//		$this->_recID = $rec;
//		$this->_isEditable = $isEditable;
//		$this->_style = $style;
//		$this->_resultType = $resultType;
//	}
//	function getFieldName() {
//		return $this->_fieldName;
//	}
//	function getRepetition() {
//		return $this->_repetition;
//	}
//	function getRecID() {
//		return $this->_recID;
//	}
//	function getIsEditable() {
//		return $this->_isEditable;
//	}
//	function getResultType() {
//		return $this->_resultType;
//	}
//	function isCheckBox(){
//		if($this->_style == "CHECKBOX"){
//			return true;
//		}else{
//			return false;
//		}
//	}
//	function isEditBox(){
//		if($this->_style ==	"SCROLLTEXT" || $this->_style ==	"EDITTEXT" || $this->_style ==	"CALENDAR"){
//			return true;
//		}else{
//			return false;
//		}
//	}
//}
//
///* 	  This a wrapper for a FileMaker_Record that checks the find request
//		and encloses any data matching the request in a span marked with the 'found' class.
//		The css files define the look of found items.  */
//
//class RecordHighlighter {
//	private $_findRequest;
//	private $_record;
//
//	function __construct($record, $cgi) {
//		$this->_record = $record;
//
//	// 	if there's a stored find request save a reference 
//
//		$find = 	$cgi->get('storedfindrequest');
//		if (isset($find))
//			$this->_findRequest = $find;
//		else
//			$this->_findRequest = NULL;
//	}
//
//	function getRelatedSet($relationName) {
//		return $this->_record->getRelatedSet($relationName);
//	}
//
//	function getField($fieldname, $repetition = 0) {
//
//	// 	call the inherited version to get the data 
//
//		$result = $this->_record->getField($fieldname, $repetition);
//		$field = $this->_record->getLayout()->getField($fieldname);
//		
//		if(isset($this->_findRequest[$fieldname])  && is_array($this->_findRequest[$fieldname])){
//			$stringValue = implode("\n", $this->_findRequest[$fieldname]);
//			$this->_findRequest[$fieldname] = $stringValue;
//		}
//		
//		if ($this->_findRequest != NULL && !FileMaker::isError($field)) {
//
//		// 	if the find request is for a field specified highlight the target 
//
//			if (isset($this->_findRequest[$fieldname]) && strlen($this->_findRequest[$fieldname]) &&
//			    		$field->getResult() != 'date' && $field->getResult() != 'timestamp' && $field->getResult() != 'time')
//			{
//				$target = $this->_findRequest[$fieldname];
//				$replace = "<strong>" . $target . "</strong>";
//				$result = str_replace($target, $replace, stripslashes($result));
//			}
//		}
//		return $result;
//	}
//
//	function getRecordId() {
//		return $this->_record->getRecordId();
//	}
//};	// RecordHighlighter


class SNMMusic {
    
    public function display_search_form() {
        
        $cgi = new CGI();
        $cgi->storeFile();
        
        $databaseName = 'Shellac';
        $layoutName = 'Music: List Web';
        
        $userName = 'Web';
        $passWord = 'web';
        
        $fm = & new FileMaker();
        $fm->setProperty('database', $databaseName);
        $fm->setProperty('username', $userName);
        $fm->setProperty('password', $passWord);
        
        $layout = $fm->getLayout($layoutName);

        // formats for dates and times
        $displayDateFormat = '%m/%d/%Y';
        $displayTimeFormat = '%I:%M:%S %P';
        $displayDateTimeFormat = '%m/%d/%Y %I:%M:%S %P';
        $submitDateOrder = 'mdy';
        
        $record = new EmptyRecord();
        ?>
        
        <form method="post" action="music_list.php">
            <input type="hidden" name="-lay" value="<?php echo '$layoutName'?>">
            <input type="hidden" name="-action" value="find">
            <input type="hidden" name="-skip" value="0">
            <input type="hidden" name="-lop" value="and" />
            <input type="hidden" name="-max" value="25" />
            
            <!-- ARTIST -->
            <?php $fieldName = 'x_ArtistName';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('x_ArtistName', 0) ; ?>
            <div style="float: left; width: 80px; padding-bottom: 5px; margin-bottom 0px;">Artist Name</div>
            <div style="float: left; width: 150px; padding-bottom: 5px; margin-bottom 0px;"><input class="search" type="text" name="<?php echo getFieldFormName($fieldName, 0, $record, true, 'EDITTEXT', 'text');?>" /></div>           
        
            <!-- Composer -->
            <?php $fieldName = 'x_Composer';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('x_Composer', 0) ; ?>
            <div style="float: left; width: 80px; margin-left: 10px; padding-bottom: 5px; margin-bottom 0px;">Composer</div>
            <div style="float: left; width: 150px; padding-bottom: 5px; margin-bottom 0px;"><input class="search" type="text" name="<?php echo getFieldFormName($fieldName, 0, $record, true, 'EDITTEXT', 'text');?>" value="<?php echo $fieldValue;?>" /></div>
            <br style="clear: both;" />
                    
            <!-- Title -->
            <?php $fieldName = 'Title';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('Title', 0) ; ?>
            <div style="float: left; width: 80px; padding-bottom: 5px; margin-bottom 0px;">Titles</div>
            <div style="float: left; width: 150px; padding-bottom: 5px; margin-bottom 0px;"><input class="search" type="text" name="<?php echo getFieldFormName($fieldName, 0, $record, true, 'EDITTEXT', 'text');?>" /></div><br style="clear: both;" />
            <br />
            
            <input type="submit" class="buttons" name="-find" value="Find Records">
        
        </form>
        
        <?php
    }
    
    //function getFieldFormName($fieldName, $repetition, $record, $isEditable, $style, $resultType) {
    //    
    //    global $cgi;
    //    $duplicateEntry = false;
    //    
    //    if (isset($cgi) === false) {
    //        $cgi = new CGI();
    //    }
    //    global $i;
    //    if (isset($i) === false) {
    //        $i = -1;
    //    }
    //    $recID = 0;
    //    if ($record != null)
    //        $recID = $record->getRecordID();
    //    $newFieldEditRecord = new FieldEditRecord($fieldName, $repetition, $recID, $isEditable, $style, $resultType);
    //    $fieldEditRecords = $cgi->get("fieldEditRecords");
    //    if (isset($fieldEditRecords) === false) {
    //        $fieldEditRecords = array();
    //    }
    //
    //    $i++;
    //    $fieldEditRecords[$i] = $newFieldEditRecord;
    //    $cgi->store("fieldEditRecords", $fieldEditRecords);
    //
    //    return $i;
    //}

    
}



?>