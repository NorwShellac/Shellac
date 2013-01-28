<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */
    require_once 'news_fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    $layoutName = 'NEWS';

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
    $record = NULL;
    $findCom = NULL;
    $findAll = NULL;
    
    $value = $cgi->get('-sortfieldone');
    $restore = $cgi->get('-restore');
    if(!isset($value) || $restore == 'true'){
        
        $cgi->clear("-restore");
    }

    //  handle the action cgi
    $action = $cgi->get('-action');
    if ($action == "findall")
    {
        $cgi->clear('skip');
        $findAll = true;
    }
        
    // clear the recid
    $cgi->clear('recid');

    // create a find command
    $findCommand = $fm->newFindCommand($layoutName);
    ExitOnError($findCommand);

    // get the posted record data from the findrecords page
    $findrequestdata = $cgi->get('storedfindrequest');
    if (isset($findrequestdata)) {
       $findCom = prepareFindRequest($findrequestdata, $findCommand, $cgi);

        // set the logical operator
       $logicalOperator = $cgi->get('-lop');
       if (isset($logicalOperator)) {
               $findCom->setLogicalOperator($logicalOperator);
       }
    } else
       $findCom = $fm->newFindAllCommand($layoutName);
    
    ExitOnError($findCom);

    // read and set, or clear the sort criteria
    $sortfield = $cgi->get('-sortfieldone');
    if (isset($sortfield)) {
        addSortCriteria($findCom);
    } else {
        clearSortCriteria($findCom);
    }

    // get the skip and max values
    $skip = $cgi->get('-skip');
    if (isset($skip) === false) {
        $skip = 0;
    }
    $max = $cgi->get('-max');
    if (isset($max) === false) {
        $max = 25;
    }

    // set skip and max values
    $findCom->setRange($skip, $max);

    // perform the find
    $result = $findCom->execute();
    ExitOnError($result);
    
    // get status info; page range, found count, total count, first, prev, next, and last links
    $statusLinks = getStatusLinks("news.php", $result, $skip, $max);

    // get the records
    $records = $result->getRecords();      
    
    // General site library
    require_once 'snm.php';
    $snm = new SNM('news');
    
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $snm->get_page_title(); ?></title>
    <link rel="shortcut icon" href="favicon.ico" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="snm.css" type="text/css" />
    <script type="text/javascript" language="javascript">
    <!--
    
        // Set the default menu for the menuing system
        var defaultMenu = 'news';
        
    -->
    </script>
    <script type="text/javascript" src="snm.js"></script>
</head>

<body>
    <?php $snm->display_menu(); ?>
    
    <!-- MAIN CONTENT -->
    <div id="main_content_one_col">
    <?php
        
        $recnum = 1;
        foreach ($records as $fmrecord) {
            
            $record = new RecordHighlighter($fmrecord, $cgi);

        ?>
        
            <h2><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('Subject', 0) ))?></h2>
            
            <p><?php echo nl2br($record->getField('Body', 0))?></p>
            
            <p class="attribute">Posted by <?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('z_CreatedBy', 0) ))?> <?php echo displayTimeStamp( $record->getField('z_CreatedOn', 0) , $displayDateTimeFormat)?></p>
            
        <?php
            $recnum++;
        }
    ?>
        
    </div>
</body>
</html>