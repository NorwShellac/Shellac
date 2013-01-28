<?php

	/**
	* FileMaker PHP Site Assistant Generated File
	*/

	 require_once 'FileMaker.php';
	 
	 function ExitOnError($result) {
	 
		$errorMessage = NULL;
		
		if (FileMaker :: isError($result)) {
			if ($result->isValidationError())
			{
				$errors = $result->getErrors();
				$firstError = $errors[0];
				$field = $firstError[0];
				$errorCode = $firstError[1];
				$errorMessage = "<p>Error: " . GetPreValidationErrStr($errorCode) . "<br></p>";
			} else {
				$errorCode = $result->getCode();
				$errorMessage = "<p>Error: " . $errorCode . " - " . $result->getErrorString() . "<br></p>";
			}
			
				DisplayError($errorMessage);
			
			exit;
		} else if ($result === NULL) {
			$errorMessage = "<p>Error: Error result is NULL!</p>";
			DisplayError($errorMessage);
			exit;
		}
	}
	
	function DisplayErrorandExit($message) {
		global $errormessage;
		$errormessage = $message . "<br>";
		include "errorpage.php";
		exit;
	}
	
	function DisplayError($message) {
		global $errormessage;
		$errormessage = $message . "<br>";
		include "errorpage.php";
	}

	
	/* Returns FileMaker error strings corresponding to the 
	 * FILEMAKER_RULE_* constant returned by pre-validation 
	 */
	function GetPreValidationErrStr($code)
    {
        $fm = & new FileMaker();
        $lang = $fm->getProperty('locale');
        
        if (!$lang) {
            $lang = 'en';
        }

        static $strings = array();
        if (empty($strings[$lang])) {
            if (!@include_once 'FileMaker/Error/' . $lang . '.php') {
                include_once 'FileMaker/Error/en.php';
            }
            $strings[$lang] = $__FM_ERRORS;
        }
		
		$errorCode = $code;
        
        switch ($code)
        {
        	case 1:
        		$errorCode = 509;
        		break;
        	case 2:
        		$errorCode = 502;
        		break;
        	case 3:
        		$errorCode = 511;
        		break;
        	case 4:
        	case 6:
        	case 7:
        		$errorCode = 500;
        		break;
        	case 5:
        	case 8:
        		$errorCode = 501;
        		break;
        	default:
        		break;		
        }
        
        if (isset($strings[$lang][$errorCode])) {
            $errorString = $strings[$lang][$errorCode];
        }
		else {
			$errorString = $strings[$lang][-1];
		}
        
        return $errorCode . " - " . $errorString;
    }?>