<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */
    require_once 'fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    $layoutName = 'Release: Detail Web';

    $userName = $cgi->get('userName');
    $passWord = $cgi->get('passWord');

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
    $response = NULL;
    $action = $cgi->get('-action');
    $recid = $cgi->get('-recid');

    switch ($action) {
        default :
            {
                $recid = $cgi->get('-recid');
                if (!isset ($recid))
                    $recid = 1;
                $record = $fm->getRecordById($layoutName, $recid);
                ExitOnError($record);
                break;
            }
    }   
    
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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
    <link type="text/css" href="skins/snm/jplayer.snm.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
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
    <div id="main_content">
    <?php
    
        $release_id;
        if (nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::t_Matrix', 0, $record, true, 'EDITTEXT', 'text')))) {
            $release_id = '[' . nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::t_Matrix', 0, $record, true, 'EDITTEXT', 'text'))) . ']';
        }
        
        $length = storeFieldNames('release_TAKE~TakeID::i_Length', 0, $record, true, 'EDITTEXT', 'time');
        if (preg_match('/^00/', $length)) {
            $length = preg_replace('/^00:/', '', $length);
        }
        
        $date = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::c_TakeDate', 0, $record, false, 'EDITTEXT', 'text')));
        $composer = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_take_MUSIC|Music ID::c_Composer_FNF', 0, $record, false, 'EDITTEXT', 'text')));
        $company = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_take_VALUELIST~CompanyID::Value', 0, $record, true, 'EDITTEXT', 'text')));
        $title = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_take_MUSIC|Music ID::c_TitlePrimary', 0, $record, false, 'EDITTEXT', 'text')));
        $matrix = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::t_Matrix', 0, $record, true, 'EDITTEXT', 'text')));
        $location = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_take_VALUELIST~VenueID::Value', 0, $record, true, 'EDITTEXT', 'text')));
        $details = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::Details', 0, $record, true, 'EDITTEXT', 'text')));
        
        $relatedRecords = $record->getRelatedSet("release_take_takejoinartist_ARTIST");
        $portal = $layout->getRelatedSet("release_take_takejoinartist_ARTIST");
        $artists = '';
		$master_record = $record;
        foreach ($relatedRecords as $record) {
            $artist = nl2br(str_replace('%20', ' ', storeFieldNames('release_take_takejoinartist_ARTIST::c_PrimaryName_LNF', 0, $record, true, 'EDITTEXT', 'text')));
            if ($artists) {
				$artists .= '; ';
			}
			$artists .= $artist;
        }
		$record = $master_record;
        
    ?>
        <h2><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_object_VALUELIST~LabelID::Value', 0, $record, true, 'EDITTEXT', 'text')))?> <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_OBJECT~ObjectID::c_CatalogNumberDisplay', 0, $record, false, 'EDITTEXT', 'text')))?> <?php echo $release_id; ?></h2>
        
        <div id="main_content_left_col">
            <div class="select_btn"><a href="music_detail.php?-action=browse&-recid=<?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_TAKE~TakeID::_fk_MusicID', 0, $record, true, 'EDITTEXT', 'number')))?>">View Related Music</a></div>
            
            <table cellpadding="3" cellspacing="0" width="100%" border="0">
                <tr>
                    <th colspan="2">Take Detail</th>
                </tr>
                <tr>
                    <td>Date: <?php echo $date ?></td>
                    <td>Composer: <?php echo $composer ?></td>
                </tr>
                <tr>
                    <td>Company: <?php echo $company ?></td>
                    <td>Title: <?php echo $title ?></td>
                </tr>
                <tr>
                    <td>Matrix: <?php echo $matrix ?></td>
                    <td>Artist: <?php echo $artists ?></td>
                </tr>
                <tr>
                    <td>Location: <?php echo $location ?></td>
                    <td>Details: <?php echo $details ?></td>
                </tr>
                <tr>
                    <!-- <td>Length: <?php echo displayTime(storeFieldNames('release_TAKE~TakeID::i_Length', 0, $record, true, 'EDITTEXT', 'time'), $displayTimeFormat)?></td> -->
                    <td>Length: <?php echo $length; ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <?php
            /*  If there's a music file attached to this release, display the J
                Player for it here.
            */
            
                $jplayer_id = 0;
                $audio_file_map = array();
                $raw_audio_file = nl2br(str_replace(' ', '%20', storeFieldNames('release_transfer_ATTACHMENT~TableID|TableName|Cre|Del::c_FileName', 0, $record, true, 'EDITTEXT', 'text')));
                preg_match('/^[^:]+:(.+)</', $raw_audio_file, $rf);
                $audio_file = $rf[1];
                
                $raw_ogg_file = nl2br(str_replace(' ', '%20', storeFieldNames('release_transfer_ATTACHMENT~TableID|TableName|Cre|Del::c_FileName_ogg', 0, $record, true, 'EDITTEXT', 'text')));
                preg_match('/^[^:]+:(.+)</', $raw_ogg_file, $ro);
                $ogg_file = $ro[1];
                

                $jplayer = '';
                $jplayer_buttons = '&nbsp;';
                if ($audio_file) {
                    
                    $jplayer_id++;
                    $audio_filename = $audio_file;
                    $audio_file_map[$jplayer_id] = array("$audio_filename", "$ogg_file");
                    $raw_song_title = $audio_file;
                    preg_match('/([^.]+)\./', $raw_song_title, $rst);
                    $song_title = str_replace('%20', '&nbsp;', $rst[1]);

                    $jplayer .= '<div id="jquery_jplayer_' . $jplayer_id . '" class="jp-jplayer" style="margin-top: 30px;"></div>' . "\n";
                    $jplayer .= '<div id="jp_container_' . $jplayer_id . '" class="jp-audio">' . "\n";
                    $jplayer .= <<<JPLAYER
                        <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                            <div id="jp_container_1" class="jp-audio">
                                <div class="jp-type-single">
                                    <div class="jp-gui jp-interface">
                                        <ul class="jp-controls">
                                            <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                            <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                            <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                            <li style="display: none;"><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                                            <li style="display: none;"><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
                                            <li style="margin-left: 200px;"><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
                                        </ul>
                                        <div class="jp-progress">
                                            <div class="jp-seek-bar">
                                                <div class="jp-play-bar"></div>
                                            </div>
                                        </div>
                                        <div class="jp-volume-bar">
                                            <div class="jp-volume-bar-value"></div>
                                        </div>
                                        <div class="jp-time-holder">
                                            <div class="jp-current-time"></div>
                                            <div class="jp-duration"></div>
                                            <ul class="jp-toggles" style="display: none;">
                                                <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
                                                <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                <div class="jp-title">
                                    <ul>
JPLAYER;
                    $jplayer .= '                    <li id="song_title_' . $jplayer_id . '" style="padding: 0px;">' . $song_title . '</li>' . "\n";
                    $jplayer .= <<< JPLAYER
                                    </ul>
                                </div>
                                <div class="jp-no-solution">
                                    <span>Update Required</span>
                                    To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                                </div>
                            </div>
                        </div>
JPLAYER;
                    $jplayer .= '</div>';
                    $audio_file = '';
                    echo $jplayer;

                }
            ?>
            
            <table style="margin-top: 25px;" cellpadding="3" cellspacing="0" width="100%" border="0">
                <tr>
                    <th colspan="2">Object Details</th>
                </tr>
                <tr>
                    <td>Label: <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_object_VALUELIST~LabelID::Value', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                </tr>
                <tr>
                    <td>Catalog#: <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_OBJECT~ObjectID::c_CatalogNumberDisplay', 0, $record, false, 'EDITTEXT', 'text')))?></td>
                </tr>
                <tr>
                    <td>Alternate Cat#: <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('release_OBJECT~ObjectID::t_CatalogNumberAlt', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                </tr>
            </table>            
        </div>
            
        <div id="main_content_right_col">
    <?php
    
            $relatedRecords = $record->getRelatedSet("release_ATTACHMENT~TableID|TableName|Cre|Del");
            $portal = $layout->getRelatedSet("release_ATTACHMENT~TableID|TableName|Cre|Del");
            if (FileMaker::isError($relatedRecords) === false) {
                $master_record = $record;
                foreach ($relatedRecords as $record) {
					$img_url = getImageURL(storeFieldNames('release_ATTACHMENT~TableID|TableName|Cre|Del::c_FileName', 0, $record, true, 'EDITTEXT', 'container'));
    ?>
					<img style="margin-bottom: 30px;" src="<?php echo getImageURL(storeFieldNames('release_ATTACHMENT~TableID|TableName|Cre|Del::r_File', 0, $record, true, 'EDITTEXT', 'container'))?>" />
    <?php       }
                $record = $master_record;
            }
    ?>
        </div>
        <br style="clear: both;" />
    </div>
    <?php
    
        if ($jplayer_id > 0) {
            
    ?>
            <script type="text/javascript" language="javascript">
            <!--
            
                $(document).ready(function(){
            
    <?php
            foreach ($audio_file_map as $player_id => $files) {
    ?>            
                    $("#jquery_jplayer_<?php echo $player_id; ?>").jPlayer({
                        ready: function (event) {
                            $(this).jPlayer("setMedia", {
                                m4a:"./attachments/<?php echo $files[0]; ?>",
                                oga:"./attachments/<?php echo $files[1]; ?>"
                            });
                        },
                        swfPath: "/js",
                        supplied: "m4a, oga",
                        //errorAlerts: true,
                    });
    <?php
            }
    ?>
                $("#jplayer_inspector").jPlayerInspector({jPlayer:$("#jquery_jplayer_1")});
                });
            -->
            </script>
    <?php
        }
    ?>
    
    
    
</body>
</html>
