<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */

    require_once 'release_fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    $layoutName = 'Release: Search Web';

    $userName = 'Web';
    $passWord = 'web';

    $fm = & new FileMaker();
    $fm->setProperty('database', $databaseName);
    $fm->setProperty('username', $userName);
    $fm->setProperty('password', $passWord);
    
    ExitOnError($fm);
    $layout = $fm->getLayout($layoutName);
    ExitOnError($layout);

    // formats for dates and times
    $displayDateFormat = '%m/%d/%Y';
    $displayTimeFormat = '%I:%M:%S %P';
    $displayDateTimeFormat = '%m/%d/%Y %I:%M:%S %P';
    $submitDateOrder = 'mdy';

    class EmptyRecord {
        function getRelatedSet($relationName) {
            return array(new EmptyRecord());
        }

        function getField($field, $repetition = 0) {
        }

        function getRecordId() {
        }
    }

    $record = new EmptyRecord();     
    
    // General site library
    require_once 'snm.php';
    $snm = new SNM('releases');
    
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $snm->get_page_title(); ?></title>
    <link rel="shortcut icon" href="favicon.ico" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="snm.css" type="text/css" />
    <script type="text/javascript" src="snm.js"></script>
</head>

<body style="background-image: none;">
    
    <!-- MAIN CONTENT -->
    <div id="main_content_one_col">
        
        <form style="margin-top: 20px;" method="post" action="release_list.php" target="_parent">
            <input type="hidden" name="-lay" value="$layoutName">
            <input type="hidden" name="-action" value="find">
            <input type="hidden" name="-skip" value="0">
            <input type="hidden" name="-lop" value="or" />
            <input type="hidden" name="-max" value="25" />
                
            <!-- LABEL -->
            <?php $fieldName = 'release_object_VALUELIST~LabelID::Value';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('release_object_VALUELIST~LabelID::Value', 0) ; ?>
            <div style="float: left; width: 85px; padding-bottom: 5px; margin-bottom 0px;"><p>Label</p></div>
            <div style="float: left; width: 100px; padding-bottom: 5px; margin-bottom 0px;"><input class="search" type="text" name="<?php echo getFieldFormName($fieldName, 0, $record, true, 'EDITTEXT', 'text');?>" onKeyUp="document.getElementById('label_abbrev').value= this.value" /></div>
            <br style="clear: both;" />
            
            <!-- HIDDEN LABEL ABBREVIATION -->
            <?php $fieldName = 'release_object_VALUELIST~LabelID::ValueAbbreviation';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('release_object_VALUELIST~LabelID::ValueAbbreviation', 0) ; ?>
            <input id="label_abbrev" type="hidden" name="<?php echo getFieldFormName($fieldName, 0, $record, true, 'EDITTEXT', 'text');?>" value="" />
            
            <!-- CATALOG # -->
            <?php $fieldName = 'release_OBJECT~ObjectID::c_CatalogNumberDisplay';?>
            <input type="hidden" name="<?php echo getFieldFormName($fieldName.'.op', 0, $record, true, 'EDITTEXT', 'text');?>" value="cn" />
            <?php $fieldValue = $record->getField('release_OBJECT~ObjectID::c_CatalogNumberDisplay', 0) ; ?>
            <div style="float: left; width: 85px; padding-bottom: 5px; margin-bottom 0px;"><p>Catalog #</p></div>
            <div style="float: left; width: 100px; padding-bottom: 5px; margin-bottom 0px;"><input class="search" type="text" name="<?php echo getFieldFormName($fieldName, 0, $record, false, 'EDITTEXT', 'text');?>" /></div>
            <br style="clear: both;" />
            
            <input class="search_btn" style="margin-left: 85px;" type="submit" value="Search" />
            <input class="search_btn" type="reset" value="Clear" />
        </form>
       
    </div>
</body>
</html>

