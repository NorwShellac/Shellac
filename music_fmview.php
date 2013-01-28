<?php


/**
* FileMaker PHP Site Assistant Utility Classes and Functions
*/

session_name("Shellac_Music_1336767668750");

session_start();
require_once "error.php";
require_once "Date.php";

define('DEBUG', 0);

// formats for dates and times
$displayDateFormat = '%m/%d/%Y';
$displayTimeFormat = '%I:%M:%S %P';
$displayDateTimeFormat = '%m/%d/%Y %I:%M:%S %P';
$submitDateOrder = 'mdy';

function debugPrint($var, $value) {
	if (DEBUG == 1) {
		print $var . " = ";
		switch (gettype($value)) {
			case 'string' :
				print $value . "<br>";
				break;
			case 'array' :
			case 'object' :
			default :
				print_r($value) . "<br>";
				break;
		}
	}
}

class CGI {

	function get($property) {


		if($property == "userName"){
			return "Web";
		}
		if($property == "passWord"){
			return "web";
		}
		if (isset ($_SESSION[$property]))
			return $_SESSION[$property];
		else
			return NULL;
	}

	function testActionRequest($testvalue) {
		$result = (isset ($_REQUEST['-action']) && $_REQUEST['-action'] == $testvalue);
		return $result;
	}

	function storeFile() {
		$path = $_SERVER['PHP_SELF'];
		$nodes = split('/', $path);
		$this->store('file', $nodes[count($nodes)-1]);
	}
	
	function checkStoredFile() {
		if(isset($_SESSION)){
			if(array_key_exists('file', $_SESSION)){
				$f = $_SESSION['file'];
				$pos = strpos($f, '?');
				if(!($pos === false)){
					$f = substr($f, 0, $pos - 1);
				}
				if($f == 'authentication.php'){
					$this->store('file', 'home.php?');
				}
			}
			else{
				$this->store('file', 'home.php?');
			}
		}
	}
		
	function store($property, $value) {
		if ($property == '-delete') {
			$_SESSION['-action'] = 'delete';
		} elseif ($property == '-duplicate') {
			$_SESSION['-action'] = 'duplicate';
		} else
			$_SESSION[$property] = $value;
	}

	function clear($property) {
		unset ($_SESSION[$property]);
	}

	function __construct() {

	//	 Request parameters are saved in the session and accessed via the CGI.

		foreach ($_GET as $key => $value) {
			$this->store($key, $value);
		}

	//  the record data submitted

		$recordData = array ();

		foreach ($_POST as $key => $value) {

		/*  If a key does not start with '-' then it is a field parameter.
		Capture the field value pairs in a record data array
		and store it in the session separately under the key 'storedfindrequest'
		when handling a '-find' request, or in the 'recorddata' key for '-edit' or '-new'  */
			
			if(strpos($key, '-', 0) === 0){
   				 $isCommand = true;
			}else{
   				 $isCommand = false;
			}

			if ($key === "userName" || $key === "passWord" || $isCommand ) {
				$this->store($key, $value);
			} else {
				$recordData[$key] = $value;
			}
		}

	//  get the field names

		$fieldEditRecords = $this->get("fieldEditRecords");

	//  always replace the existing find request

		if ($this->testActionRequest("find")) {
		
		// formats for dates and times
			$displayDateFormat = '%m/%d/%Y';

			if (isset($fieldEditRecords) === true) {

			// 	move the submitted data to the stored find request; an array, keys: field names, values: submitted query

				$storedFind = array();
				foreach ($recordData as $index => $value) {
						if(!($index % 2 == 0)){
				 		//Convert any time/date formatted field 
                            if(array_key_exists($index, $fieldEditRecords)){
                                $fieldEditRecord = $fieldEditRecords[$index];
                            }
                    
                            if (is_null($fieldEditRecord) === false && $fieldEditRecord->isEditBox()){
                                $resultType = $fieldEditRecord->getResultType();
                                    if ($resultType == "date")
                                        $value = submitDate($value, $displayDateFormat);
                                    else if ($resultType == "timestamp")
                                        $value = submitTimeStampForSearch($value, $displayDateFormat);
							}
                     }
                     $storedFind[$fieldEditRecords[$index]->getFieldName()] = $value;
                }     
				$this->store('storedfindrequest', $storedFind);
			}

	// clear it for a findall

		} else {
			if ($this->testActionRequest("findall"))
				$this->clear('storedfindrequest');

		// 	store edit or new request record data

			else {
				if ($this->testActionRequest("edit") || $this->testActionRequest("new")) {
					$this->store('recorddata', $recordData);
				} else {

				// 	clear out recorddata if not an edit

					$this->clear('recorddata');
				}
			}
		}
	}

	function reset() {
		$this->clear('recorddata');
		$this->clear('storedfindrequest');
		$this->clear('fieldEditRecords');
		$_SESSION = array();
	}
}	// CGI

function getSortRecordsLink($fieldName, $fieldDisplayName) {
	global $cgi;
	$sortFieldOne = $cgi->get('-sortfieldone');
	
	$escapedFieldName = urlencode ($fieldName);
	$escapedFieldDisplayName = str_replace(' ','&nbsp; ',htmlentities($fieldDisplayName,ENT_NOQUOTES,'UTF-8',false));

	// 	ascending is the default

	$direction = 'ascend';
	$sortfieldlink = "<a href='recordlist.php?-skip=0&amp;-sortfieldone=$escapedFieldName&amp;-sortorderone=$direction'>$escapedFieldDisplayName</a>";

	// 	was the -sortfieldone query parameter set in the session?

	if (isset ($sortFieldOne)) {

		// 	if so, did it specify the sort order parameter?

		if ($fieldName == $sortFieldOne) {
			$direction = $cgi->get('-sortorderone');

			// 	now flip the direction for the column header link

			if (isset ($direction) && $direction == 'ascend')
				$direction = 'descend';
			else
				$direction = 'ascend';
			$sortfieldlink = "<a class='sorted' href='recordlist.php?-skip=0&amp;-sortfieldone=$escapedFieldName&amp;-sortorderone=$direction'>$escapedFieldDisplayName</a>";
		}
	}
	return $sortfieldlink;
}

function getStatusLinks($resultPage, $rs, $skip, $max) {
	
	$links = array (
		'first' => 'First',
		'prev' => 'Prev',
		'records' => array (
			'rangestart' => 0,
			'rangeend' => 0,
			'foundcount' => 0
		),
		'next' => 'Next',
		'last' => 'Last'
	);
	
	
	$fetchcount = $rs->getFetchCount();
	$foundcount = $rs->getFoundSetCount();
	$total = $rs->getTableRecordCount();

	if ($total == 0 || $fetchcount == 0) {
		return $links;
	} else {
		if ($fetchcount > 0) {
			if ($skip > 0) {
				$links['first'] = "<a href='$resultPage?-skip=0&-max=$max'>" . $links['first'] . "</a>";
				if ($skip >= $max) {
					$prevskip = $skip - $max;
					$links['prev'] = "<a href='$resultPage?-skip=$prevskip&-max=$max'>" . $links['prev'] . "</a>";
				}
			}
			if ($foundcount - $skip > $max) {
				$nextskip = $skip + $max;
				$links['next'] = "<a href='$resultPage?-skip=$nextskip&-max=$max'>" . $links['next'] . "</a>";
				$lastskip = $foundcount - $max;
				$links['last'] = "<a href='$resultPage?-skip=$lastskip&-max=$max'>" . $links['last'] . "</a>";
			}
			$links['records']['rangestart'] = max($skip + 1, 1);
			$links['records']['rangeend'] = min($foundcount + $skip, $fetchcount + $skip);
			$links['records']['foundcount'] = $foundcount;
		}
	}
	return $links;
}

/* 	 formatDate parses an input string containing a date and returns a Date object.
		dateString The string containing the unparsed date
		dateOrder A string describing the order of date elements; 'mdy', 'dmy', etc.
		delimiter A character delimiter to be used for parsing the input $dateString.
		returns null if the date can't be parsed. */

function formatDate($dateString, $dateOrder, $delimiter) {
 	$day = null;
 	$month = null;
 	$year = null;
 	
 	 	
  	$dateOrder = str_replace("%", "", $dateOrder);
 	$dateOrder = str_replace("/", "", $dateOrder);
 	$dateOrder = str_replace("-", "", $dateOrder);
 	$dateOrder = str_replace(".", "", $dateOrder);
 	$dateOrder = strtolower($dateOrder);
 	
	$temp = split($delimiter, $dateString);
	$numOfVariables = sizeof($temp);
 	switch($dateOrder) {

	// 	format xml

 		case "mdy": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($month, $day) = array_values($temp);
 					break;
 				case 3:
 					list($month, $day, $year) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		case "dmy": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($day, $month) = array_values($temp);
 					break;
 				case 3:
 					list($day, $month, $year) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		case "ymd": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($year, $month) = array_values($temp);
 					break;
 				case 3:
 					list($year, $month, $day) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		case "ydm": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($year, $day) = array_values($temp);
 					break;
 				case 3:
 					list($year, $day, $month) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		case "myd": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($month, $year) = array_values($temp);
 					break;
 				case 3:
 					list($month, $year, $day) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		case "dym": {
 			switch($numOfVariables){
 				case 1:
 					list($month) = array_values($temp);
 					break;
 				case 2:
 					list($day, $year) = array_values($temp);
 					break;
 				case 3:
 					list($day, $year, $month) = array_values ($temp); 			
 					break;
 			
 			}
 			break;
 		}
 		default:
 			return null;
 			break;
 	}
 	$d = new Date();
	if(isset($day)) 	
		$d->setDay($day);
	if(isset($month))
		$d->setMonth($month);
	if(isset($year))
		$d->setYear($year);
 	return $d;
}

// 	Parses a string containing a time and returns a Date object, or null if the string can't be parsed.

function formatTime($timeString) {
	$timeDelimiter = "[.:]";
	$ampmDelimiter = " ";
 	$hour = null;
 	$minute = null;
 	$second = null;
 	$ampm = null;

	$timeArray = split($timeDelimiter, $timeString);
	
	if (count($timeArray) == 3) {
		list($hour, $minute, $second) = $timeArray;
		$ampmArray = split($ampmDelimiter, $second);
		
		if (count($ampmArray) == 2) {
			list ($second, $ampm) = $ampmArray;
		}
	}		
	else {
		list($hour, $minute) = $timeArray;
		$ampmArray = split($ampmDelimiter, $minute);
		
		if (count($ampmArray) == 2) {
			list ($minute, $ampm) = $ampmArray;
		}
	}
	
	if (is_null($ampm) === false && strtolower($ampm) == "pm" && $hour != 12){
		$hour += 12;
	}
	
	$d = new Date();
	$d->setHour($hour);	
	$d->setMinute($minute);
	$d->setSecond($second);

 	return $d;
 }

// 	display date obtained in xml format in a given output format

function displayDate($dateString, $outputFormat) {
	if (is_null($dateString) === false && strlen($dateString) > 0) {
		$d = formatDate($dateString, "mdy", "/");
		return $d->format($outputFormat);
	}
}

// 	display time in given format

function displayTime($timeString, $outputFormat) {
	if (is_null($timeString) === false && strlen($timeString) > 0) {
		$t = formatTime($timeString);
		return $t->format($outputFormat);
	}
}

// 	display time stamp in given format

function displayTimeStamp($dateString, $format) {
	if (is_null($dateString) === false && strlen($dateString) > 0) {
		$ampm = "";
		$dateArray = split(" ", $dateString);
		if (count($dateArray) == 2) {
			if (sizeof(split(" ", $dateString . " " . $format)) == 5){
					list($date, $time, $dateFormat, $timeFormat, $amPm) = split(" ", $dateString . " " . $format);
			}else{
					list($date, $time, $dateFormat, $timeFormat) = split(" ", $dateString . " " . $format);
			}
		} else {
			list($date, $time, $ampm) = $dateArray;
			$pmFormat = null;
			$formatArray = split(" ", $format);
			if (count($formatArray) == 3) {
				list($dateFormat, $timeFormat, $pmFormat) = $formatArray;
				$timeFormat = $timeFormat . " " . $pmFormat;
			} else {
				list($dateFormat, $timeFormat) = $formatArray;
			}
		}
		$d = displayDate($date, $dateFormat);
		if(isset($amPm)){
			$t = displayTime($time . " " . $ampm, $timeFormat . " " . $amPm);
		}else{
			$t = displayTime($time . " " . $ampm, $timeFormat);
		}
		return $d . " " . $t;
	}
}

function isPortalField($record, $fieldName){
	return !in_array($fieldName, $record->getLayout()->listFields());
}

// 	convert date from given format to xml format for submission

function submitDate($dateString, $inputFormat) {
	if (is_null($dateString) === false && strlen($dateString) > 0) {
		$d = formatDate($dateString, $inputFormat, "[./-]");
		return $d->format("%m/%d/%Y");
	}
}

// 	convert time to xml format for submission

function submitTime($timeString) {
	if (is_null($timeString) === false && strlen($timeString) > 0) {
		$t = formatTime($timeString);
		return $t->format("%I:%M:%S %P");
	}
}

function submitTimeStampForSearch($dateString, $format){
	if (is_null($dateString) === false && strlen($dateString) > 0) {
			list($date, $time) = split(" ", $dateString, 2);
			$d = submitDate($date, $format);
			return $d . " " . $time;
	}
}

// 	convert time stamp from given date order to xml format for submission

function submitTimeStamp($dateString, $format) {
	if (is_null($dateString) === false && strlen($dateString) > 0) {
		list($date, $time) = split(" ", $dateString, 2);
		$d = submitDate($date, $format);
		$t = submitTime($time);
		return $d . " " . $t;
	}
}

function submitRecordData($recorddata, $command, $cgi, $fieldslist = null) {
	$fieldEditRecords = $cgi->get("fieldEditRecords");
	if (isset($fieldEditRecords)) {
	
	
	//This is to get around the fact that emptied out checkboxes are not passed in to be committed. 
		if(sizeof($fieldEditRecords) != sizeof($recorddata)){
				for( $i=0; $i < sizeof($fieldEditRecords); $i++){
					$isFieldEditable = $fieldEditRecords[$i]->getIsEditable();
					$isCheckBox = $fieldEditRecords[$i]->isCheckBox();
					 if(!isset($recorddata[$i]) && $isFieldEditable && $isCheckBox){
						$recorddata[$i] = "";
					}  
				} 
			}
	
			
		foreach ($recorddata as $field => $value) {

		// 	lookup the field's edit record within the session

			
			if(array_key_exists($field, $fieldEditRecords)){
				$fieldEditRecord = $fieldEditRecords[$field];
			}

			if (is_null($fieldEditRecord) === false) {
				$fieldName = $fieldEditRecord->getFieldName();
				$repetition = $fieldEditRecord->getRepetition();
				$recID = $fieldEditRecord->getRecID();

			// 	handle related fields

				if ($fieldslist != null && !in_array($fieldName, $fieldslist)) {
					if (isset($recID) === false) {

					// 	creating a new related value

						$recID = 0;
					}

					// 	related field names end with '.relatedRecID'

					$fieldName .= '.' . $recID;
				}
				
				if(is_array($value)){
					$value = implode("\r", $value);
				}
				
				if($action = $cgi->get('-action') === "new"){ 
					 if(strlen($value) > 0){
				
						$command->setField($fieldName, $value, $repetition);
					}
				}else{
					$command->setField($fieldName, $value, $repetition);
				}
				
			}
		} // foreach
	}	

	// 	execute the command

	if (($result = $command->execute()) === false) {
		DisplayError("commit failed!");
	}
	
	$cgi->clear('recorddata');
	$cgi->clear('fieldEditRecords');
	return $result;
}

// 	add the sort criteria from the session to the find command

function addSortCriteria($findCommand) {
	$sortCriteria = array(
		'-sortfieldone' => '-sortorderone',
		'-sortfieldtwo' => '-sortordertwo',
		'-sortfieldthree' => '-sortorderthree',
		'-sortfieldfour' => '-sortorderfour',
		'-sortfieldfive' => '-sortorderfive',
		'-sortfieldsix' => '-sortordersix',
		'-sortfieldseven' => '-sortorderseven',
		'-sortfieldeight' => '-sortordereight',
		'-sortfieldnine' => '-sortordernine'
	);
	global $cgi;
	$i = 1;
	foreach ($sortCriteria as $field => $position) {
		$sortField = urldecode($cgi->get($field));
		$order = $cgi->get($position);
        if (isset($sortField) && isset($order)) {
	        if ($order == "ascend")
	           $order = FILEMAKER_SORT_ASCEND;
	        elseif ($order == "descend")
	           $order = FILEMAKER_SORT_DESCEND;

	    // 	otherwise the order is a value list name 

	        $findCommand->addSortRule($sortField, $i, $order);
	        $i++;
        } else
        		break;
    }
}

// 	clear the sort criteria from the find command and the session 

function clearSortCriteria($findCommand) {
	$findCommand->clearSortRules();
	$sortCriteria = array(
		'-sortfieldone' => '-sortorderone',
		'-sortfieldtwo' => '-sortordertwo',
		'-sortfieldthree' => '-sortorderthree',
		'-sortfieldfour' => '-sortorderfour',
		'-sortfieldfive' => '-sortorderfive',
		'-sortfieldsix' => '-sortordersix',
		'-sortfieldseven' => '-sortorderseven',
		'-sortfieldeight' => '-sortordereight',
		'-sortfieldnine' => '-sortordernine'
	);
	global $cgi;
	foreach ($sortCriteria as $field => $position) {
		$cgi->clear($field);
		$cgi->clear($position);
    }
}

function prepareFindRequest($storedfindrequest, $findcommand, $cgi) {

// 	map from cgi to fm php api format 


	$findops = array('cn' => '*',
		'bw' => '*',
		'ew' => '==*',
		'eq' => '==',
		'neq' => '!=',
		'lt' => '<',
		'lte' => '<=',
		'gt' => '>',
		'gte' => '>=');

// 	go through the submitted data and convert to a form appropriate for fm php api 

	foreach ($storedfindrequest as $fieldName => $value) {

	// 	look for operators 

		if (($oppos = strrpos($fieldName, '.op')) > 0) {

		// 	create the fieldname by stripping the operator 

			$fieldName = substr($fieldName, 0, $oppos);
			
			if(isset($storedfindrequest[$fieldName])  && is_array($storedfindrequest[$fieldName])){
				$stringValue = implode("\r", $storedfindrequest[$fieldName]);
				$storedfindrequest[$fieldName] = $stringValue;
			}

		// 	prepend the value with the find operator retrieved from the operator map 

			if (isset($storedfindrequest[$fieldName]) && strlen($storedfindrequest[$fieldName]) > 0) {
				switch ($value) {

				// 	begins with becomes the search value followed by wildcard 

					case 'bw':
					$storedfindrequest[$fieldName] = "==" . $storedfindrequest[$fieldName] . "*";
					break;

				// 	contains surrounds the value with '*' 

					case 'cn':
					$storedfindrequest[$fieldName] = $findops[$value] . $storedfindrequest[$fieldName] . $findops[$value];
					break;
					
					case 'ew':
					$storedfindrequest[$fieldName] = $findops[$value] . $storedfindrequest[$fieldName];
					break;

				// 	all the others precede the value 

					default:
					$storedfindrequest[$fieldName] = $findops[$value] . $storedfindrequest[$fieldName];
					break;
				}
			}
		}
	}

// 	now, go through and add the find criteria to the find command 

	foreach ($storedfindrequest as $field => $value) {

	// 	skip the operators, they are handled above 

		if ((strrpos($field, '.op') > 0) === false) {

		// 	ignore empty values 

			if (strlen($value) > 0) {
				$findcommand->addFindCriterion($field, $value);
			}

		} else
			unset($storedfindrequest[$field]);
	}

	return $findcommand;
}

function getMenu($valuelist, $fieldvalue, $menutitle, $fieldType, $submitDateOrder) {
	global $cgi;
	$selected = "";
	$options = "";
	$selectedFound = false;
	
	foreach ($valuelist as $eachvalue => $storedValue) {
		if ($fieldType == "time") {
			$storedValue = submitTime($storedValue, $submitDateOrder);
		}elseif ($fieldType == "timestamp") {
			$storedValue = submitTimeStamp($storedValue, $submitDateOrder);
		}
	
		if ($storedValue == $fieldvalue) {
			$selected = " selected";
			$selectedFound = true;
		}
		else
			$selected = "";
	
		$encodedEachValue = htmlentities($eachvalue,ENT_NOQUOTES,'UTF-8',false);
		$encodedStoredValue = htmlentities($storedValue,ENT_NOQUOTES,'UTF-8',false);
		
		$options .= "<option value=$encodedStoredValue $selected>$encodedEachValue</option>";
	}
	
	$cgi->store('menuSelectedFound',$selectedFound);
		
	return $options;
	
}

// 	Return the source attribute value for a container field image 

function getImageURL($fieldData) {
	return "img.php?-url=" . urlencode($fieldData);
}

/* 	  Create a fieldEditRecord for the specified field, repetition, and record id.
		Store it in the fieldEditRecords array, store it in the session and return
		the index to be used to look up the original field, repetition, and record id
		during form submission.  */

function getFieldFormName($fieldName, $repetition, $record, $isEditable, $style, $resultType) {
	global $cgi;
	$duplicateEntry = false;
	
	if (isset($cgi) === false) {
		$cgi = new CGI();
	}
	global $i;
	if (isset($i) === false) {
		$i = -1;
	}
	$recID = 0;
	if ($record != null)
		$recID = $record->getRecordID();
	$newFieldEditRecord = new FieldEditRecord($fieldName, $repetition, $recID, $isEditable, $style, $resultType);
	$fieldEditRecords = $cgi->get("fieldEditRecords");
	if (isset($fieldEditRecords) === false) {
		$fieldEditRecords = array();
	}

	$i++;
	$fieldEditRecords[$i] = $newFieldEditRecord;
	$cgi->store("fieldEditRecords", $fieldEditRecords);

	return $i;
}

function storeFieldNames($fieldName, $repetition, $record, $isEditable, $style, $resultType) {
	getFieldFormName($fieldName, $repetition, $record, $isEditable, $style, $resultType);
	return $record->getField($fieldName, $repetition);
}

// 	Extract the field name, repetition number, and record id for a submitted field value 

class FieldEditRecord {
	private $_fieldName;
	private $_repetition;
	private $_recID;
	private $_submittedValue = null;
	private $_isEditable = true;
	private $_style;
	private $_resultType;

	function FieldEditRecord ($name, $rep, $rec, $isEditable, $style, $resultType) {
		$this->_fieldName = $name;
		$this->_repetition = $rep;
		$this->_recID = $rec;
		$this->_isEditable = $isEditable;
		$this->_style = $style;
		$this->_resultType = $resultType;
	}
	function getFieldName() {
		return $this->_fieldName;
	}
	function getRepetition() {
		return $this->_repetition;
	}
	function getRecID() {
		return $this->_recID;
	}
	function getIsEditable() {
		return $this->_isEditable;
	}
	function getResultType() {
		return $this->_resultType;
	}
	function isCheckBox(){
		if($this->_style == "CHECKBOX"){
			return true;
		}else{
			return false;
		}
	}
	function isEditBox(){
		if($this->_style ==	"SCROLLTEXT" || $this->_style ==	"EDITTEXT" || $this->_style ==	"CALENDAR"){
			return true;
		}else{
			return false;
		}
	}
}

/* 	  This a wrapper for a FileMaker_Record that checks the find request
		and encloses any data matching the request in a span marked with the 'found' class.
		The css files define the look of found items.  */

class RecordHighlighter {
	private $_findRequest;
	private $_record;

	function __construct($record, $cgi) {
		$this->_record = $record;

	// 	if there's a stored find request save a reference 

		$find = 	$cgi->get('storedfindrequest');
		if (isset($find))
			$this->_findRequest = $find;
		else
			$this->_findRequest = NULL;
	}

	function getRelatedSet($relationName) {
		return $this->_record->getRelatedSet($relationName);
	}

	function getField($fieldname, $repetition = 0) {

	// 	call the inherited version to get the data 

		$result = $this->_record->getField($fieldname, $repetition);
		$field = $this->_record->getLayout()->getField($fieldname);
		
		if(isset($this->_findRequest[$fieldname])  && is_array($this->_findRequest[$fieldname])){
			$stringValue = implode("\n", $this->_findRequest[$fieldname]);
			$this->_findRequest[$fieldname] = $stringValue;
		}
		
		if ($this->_findRequest != NULL && !FileMaker::isError($field)) {

		// 	if the find request is for a field specified highlight the target 

			if (isset($this->_findRequest[$fieldname]) && strlen($this->_findRequest[$fieldname]) &&
			    		$field->getResult() != 'date' && $field->getResult() != 'timestamp' && $field->getResult() != 'time')
			{
				$target = $this->_findRequest[$fieldname];
				$replace = "<strong>" . $target . "</strong>";
				$result = str_replace($target, $replace, stripslashes($result));
			}
		}
		return $result;
	}

	function getRecordId() {
		return $this->_record->getRecordId();
	}
};	// RecordHighlighter
function getInputChoices($type, $valuelist, $fieldvalue, $fieldName, $fieldType, $submitDateOrder) {
	// formats for dates and times
	$displayDateFormat = '%m/%d/%Y';
	$displayTimeFormat = '%I:%M:%S %P';
	$displayDateTimeFormat = '%m/%d/%Y %I:%M:%S %P';
	$submitDateOrder = 'mdy';
	
	$selected = "";
	$fieldValueArray = explode(" ", str_replace("\n"," ", $fieldvalue));
	foreach ($valuelist as $eachvalue => $storedValue) {
				$temp = $storedValue;
			if ($fieldType == "date"){
				$temp =  displayDate($temp, $displayDateFormat);
				$eachvalue =  displayDate($eachvalue, $displayDateFormat);
			}elseif($fieldType == "time"){
				$temp =  displayTime($temp, $displayTimeFormat);
				$eachvalue =  displayTime($eachvalue, $displayTimeFormat);
			}elseif($fieldType == "timestamp"){
				$temp =  displayTimeStamp($temp, $displayDateTimeFormat);
				$eachvalue =  displayTimeStamp($eachvalue, $displayDateTimeFormat);
			}
		$storedValueArray = explode(" ", str_replace("\n"," ", $temp));
		
		if (sizeof(array_intersect($storedValueArray, $fieldValueArray)) === sizeof($storedValueArray) ){
			$selected = " checked";
		}else{
			$selected = "";
		}
		
		$encodedEachValue = htmlentities($eachvalue,ENT_NOQUOTES,'UTF-8',false);
		$encodedStoredValue = htmlentities($storedValue,ENT_NOQUOTES,'UTF-8',false);
		
		if ($type == "checkbox"){
			echo "<input type='$type' name='$fieldName" . "[]'" . "value=$encodedStoredValue $selected>$encodedEachValue";
		}else{
			echo "<input type='$type' name='$fieldName' value=$encodedStoredValue $selected>$encodedEachValue";
		}
	}
}

function echoTextArea($fieldName, $i, $record, $isEditable, $style, $resultType, $fieldValue) {
	echo "<textarea class=\"fieldinput\" type=\"text\" cols=\"30\" rows=\"5\" name=\"";
	echo getFieldFormName($fieldName, $i, $record, $isEditable, $style, $resultType);
	echo "\">".$fieldValue."</textarea>";
}

?>