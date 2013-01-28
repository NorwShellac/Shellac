<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
    $layoutName = 'Release: List Web';

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
    $status_links = '';
    $records = '';
    $hide_navigation = '';
    $hide_search_results = '';
    $show_no_results = 'display: none;"';
    if ($result == 'No records match the request' || $result == 'Field is missing') {
        $hide_navigation = 'style="visibility: hidden;"';
        $hide_search_results = 'style="display: none;"';
        $show_no_results = 'style="display: block;"';
    }
    else {
    
        // get status info; page range, found count, total count, first, prev, next, and last links
        $statusLinks = getStatusLinks("release_list.php", $result, $skip, $max);
    
        // get the records
        $records = $result->getRecords();
        
    }  
    
    // General site library
    require_once 'snm.php';
    $snm = new SNM('releases');
    if ($action != 'findall') {
        $snm->releases_list_override = '?-action=findall';
        $snm->releases_list_label .= ' All';
    }
    
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
        var defaultMenu = 'releases';
        
    -->
    </script>
    <script type="text/javascript" src="snm.js"></script>
</head>
<body>
    <?php $snm->display_menu(); ?>
    
    <!-- MAIN CONTENT -->
    <div id="main_content_one_col">
        
        <div id="pagination" <?php echo $hide_navigation ?>>
            <div class="pagi_nav"><?php echo $statusLinks['first'] ?></div>
            <div class="pagi_nav"><?php echo $statusLinks['prev'] ?></div>
            <div class="pagi_count">Viewing Records <?php echo $statusLinks['records']['rangestart'] ?> - <?php echo $statusLinks['records']['rangeend'] ?> of <?php echo $statusLinks['records']['foundcount'] ?></div>
            <div class="pagi_nav"><?php echo $statusLinks['next'] ?></div>
            <div class="pagi_nav"><?php echo $statusLinks['last'] ?></div>
        </div>
        
        <div class="search_no_results" <?php echo $show_no_results ?>"><p>Sorry, nothing matching your search could be found.</p></div>
    
        <table <?php echo $hide_search_results ?> cellpadding="3" cellspacing="0" width="100%" border="0">
            <tr>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_object_VALUELIST%7ELabelID%3A%3AValue&amp;-sortorderone=ascend&-sortfieldtwo=release_OBJECT~ObjectID::c_CatalogNumberDisplay&-sortordertwo=ascend">Label</a></th>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_OBJECT%7EObjectID%3A%3Ac_CatalogNumberDisplay&amp;-sortorderone=ascend">Catalog#</a></th>
                <th style="width: 85px;">Type</th>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_take_VALUELIST%7ECompanyID%3A%3AValue&amp;-sortorderone=ascend&-sortfieldtwo=release_TAKE~TakeID::t_Matrix&-sortordertwo=ascend">Mfg. Company</a></th>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_TAKE%7ETakeID%3A%3At_Matrix&amp;-sortorderone=ascend">Matrix</a></th>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_TAKE%7ETakeID%3A%3Ac_TakeDate&amp;-sortorderone=ascend&-sortfieldtwo=release_take_VALUELIST%7ECompanyID%3A%3AValue&amp;-sortordertwo=ascend&-sortfieldthree=release_TAKE~TakeID::t_Matrix&-sortorderthree=ascend">Recording Date</a></th>
                <th><a href="release_list.php?-skip=0&amp;-sortfieldone=release_take_VALUELIST%7EVenueID%3A%3AValue&amp;-sortorderone=ascend">Location</a></th>
            </tr>

    <?php
    
        $recnum = 1;

        foreach ($records as $fmrecord) {

            $record = new RecordHighlighter($fmrecord, $cgi);
            $row_class = ($recnum % 2 == 0) ? '' : 'class="alt"';
            $recid = $record->getRecordId();
            $pos = strpos($recid, "RID_!");
            if ($pos !== false) {
                $recid = substr($recid,0,5) . urlencode(substr($recid,strlen("RID_!")));
            }
            $music_recid = nl2br(str_replace(' ', '&nbsp;', $record->getField('release_TAKE~TakeID::_fk_MusicID', 0) ));
            $release_length = $record->getField('release_TAKE~TakeID::i_Length', 0);
            if ($release_length) {
                $release_length = preg_replace('/^00:/', '', $release_length);
            }
    ?>
                                                            
            <tr>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_object_VALUELIST~LabelID::ValueAbbreviation', 0) ))?></td>
                <td <?php echo $row_class ?>><a href="release_detail.php?-action=browse&-recid=<?php echo $recid; ?>"><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_OBJECT~ObjectID::c_CatalogNumberDisplay', 0) ))?></a></td>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_OBJECT~ObjectID::n_Size', 0) ))?></td>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_take_VALUELIST~CompanyID::Value', 0) ))?></td>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_TAKE~TakeID::t_Matrix', 0) ))?></td>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_TAKE~TakeID::c_TakeDate', 0) ))?></td>
                <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', $record->getField('release_take_VALUELIST~VenueID::Value', 0) ))?></td>
            </tr>
    <?php
            $recnum++;
        }
    ?>
        </table>
        <br style="clear: both;" />
    </div>
</body>
</html>
