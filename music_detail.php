<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */
    require_once 'music_fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    $layoutName = 'Music: Detail Web';

    $userName = 'web';
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
    $snm = new SNM('music');
    
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
        var defaultMenu = 'music';
            
        function loadSong(playerID, fileName, songTitle) {
        /*  Given a playerID, fileName, and songTitle, load an instance of JPlayer
            with the requested song and set the display text to songTitle
        */
        
            document.getElementById('song_title_'+playerID).innerHTML = songTitle;
            
        }
        
    -->
    </script>
    <script type="text/javascript" src="snm.js"></script>
</head>
<body>
    <?php $snm->display_menu(); ?>
    
    <!-- MAIN CONTENT -->
    <div id="main_content_one_col">
        
        <div id="artist_stats">
           <h2><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_ARTIST~ComposerID::c_PrimaryName_FNF', 0, $record, false, 'EDITTEXT', 'text')))?></h2>
    <?php
                                                                            
        $relatedRecords = $record->getRelatedSet("music_TITLE~MusicID|CasDel");
        $portal = $layout->getRelatedSet("music_TITLE~MusicID|CasDel");
        $titles;
        if (FileMaker::isError($relatedRecords) === false) {
            $master_record = $record;
            foreach ($relatedRecords as $record) {
                if ($titles) $titles .= '<br />';
                $titles .= nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_TITLE~MusicID|CasDel::Title', 0, $record, true, 'EDITTEXT', 'text')));
            }
            $record = $master_record;
        }
    ?>
            <h4><?php echo $titles; ?></h4>    
        </div>
        
        <table style="padding-top: 10px; margin: 20px 0px;" cellpadding="3" cellspacing="0" width="100%" border="0">
            <tr>
                <th>&nbsp;</th>
                <th>Take Date</th>
                <th>Manufacturing Company</th>
                <th>Matrix</th>
                <th>Location</th>
                <th>&nbsp</th>
            </tr>
    <?php
            
        // Get the artists and map them to takes
        $relatedRecords = $record->getRelatedSet("music_take_TAKEJOINARTIST~TakeID|CasDel");
        $portal = $layout->getRelatedSet("music_take_TAKEJOINARTIST~TakeID|CasDel");
        $all_artists = array();
        $take_artists = array();
        if (FileMaker::isError($relatedRecords) === false) {
            $master_record = $record;
            foreach ($relatedRecords as $record) {
                $artist_name = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_takejoinartist_ARTIST~ArtistID::c_PrimaryName_FNF', 0, $record, false, 'EDITTEXT', 'text')));
                $take_id = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_TAKEJOINARTIST~TakeID|CasDel::_fk_TakeID', 0, $record, true, 'EDITTEXT', 'number')));
                array_push($all_artists[$artist_name], $take_id);
                $artist_id = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_TAKEJOINARTIST~TakeID|CasDel::_fk_ArtistID', 0, $record, true, 'EDITTEXT', 'number')));
                $role = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_TAKEJOINARTIST~TakeID|CasDel::C_Role', 0, $record, false, 'EDITTEXT', 'text')));
                if ($take_artists[$take_id]) $take_artists[$take_id] .= '::';
                $take_artists[$take_id] .= $artist_name . '||' . $artist_id . '||' . $role;
            }
            $record = $master_record;
        }
        
        // Get all the releases
        $relatedRecords = $record->getRelatedSet("music_take_RELEASE~TakeID|CasDel");
        $portal = $layout->getRelatedSet("music_take_RELEASE~TakeID|CasDel");
        $all_releases = array();
		$unique_releases = array();
        if (FileMaker::isError($relatedRecords) === false) {
            $master_record = $record;
            foreach ($relatedRecords as $record) {
                $catalog_num = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_release_OBJECT~ObjectID::c_CatalogNumberDisplay', 0, $record, false, 'EDITTEXT', 'text')));
                $label = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_release_object_VALUELIST~LabelID::Value', 0, $record, true, 'EDITTEXT', 'text')));
                $take_id = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_RELEASE~TakeID|CasDel::_fk_TakeID', 0, $record, true, 'EDITTEXT', 'number')));
                $unique_key = $catalog_num . '-' . $label . '-' . $take_id;
                $release_id = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_RELEASE~TakeID|CasDel::__pk_ReleaseID', 0, $record, true, 'EDITTEXT', 'number')));
                if (! array_key_exists($all_releases[$unique_key])) $all_releases[$unique_key] = array();
                array_push($all_releases[$unique_key], array($label, $catalog_num, $take_id, $release_id));
				$unique_id = $catalog_num . '-' . $label;
				$unique_releases[$unique_id] = array($catalog_num, $label);
            }
            $record = $master_record;
        }
            
        // Get the take records
        $relatedRecords = $record->getRelatedSet("music_TAKE~MusicID|CasDel|Sorted");
        $portal = $layout->getRelatedSet("music_TAKE~MusicID|CasDel|Sorted");
        $unique_take_ids = array();
        $jplayer_id = 0;
        $audio_file_map = array();
        if (FileMaker::isError($relatedRecords) === false) {
            $master_record = $record;
            foreach ($relatedRecords as $record) {
                
                $take_id = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_TAKE~MusicID|CasDel|Sorted::__pk_TakeID', 0, $record, true, 'EDITTEXT', 'number')));
                $unique_take_ids[$take_id] = '';
 
                $raw_audio_file = nl2br(str_replace(' ', '%20', storeFieldNames('music_take_ATTACHMENT~TableID|TableName|Audio::c_FileName', 0, $record, true, 'EDITTEXT', 'text')));
                preg_match('/^[^:]+:(.+)</', $raw_audio_file, $rf);
                $audio_file = $rf[1];
 
                $raw_ogg_file = nl2br(str_replace(' ', '%20', storeFieldNames('music_take_ATTACHMENT~TableID|TableName|Audio::c_FileName_ogg', 0, $record, true, 'EDITTEXT', 'text')));
                preg_match('/^[^:]+:(.+)</', $raw_ogg_file, $ro);
                $ogg_file = $ro[1];
 
                $jplayer = '';
                $jplayer_buttons = '&nbsp;';
                $hide_player_call = '';
                if ($audio_file) {
                    
                    $jplayer_id++;
                    $audio_filename = $audio_file;
                    $audio_file_map[$jplayer_id] = array("$audio_filename", "$ogg_file");
                    $raw_song_title = $audio_file;
                    preg_match('/([^.]+)\./', $raw_song_title, $rst);
                    $song_title = str_replace('%20', '&nbsp;', $rst[1]);
                    $jplayer = '<div class="player" style="float: right;" id="player_' . $jplayer_id . '">' . "\n";
                    $jplayer .= '<div id="jquery_jplayer_' . $jplayer_id . '" class="jp-jplayer"></div>' . "\n";
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
                    $jplayer .= '                    <li id="song_title_' . $jplayer_id . '" style="padding: 0px;"></li>' . "\n";
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
                    
                    //$jplayer = '<div class="player" id="player_' . $jplayer_id . '"><img src="images/artist-detail-player.png" width="427" height="33" align="right" /></div>';
                    $jplayer_buttons = '<a id="player_on_btn_' . $jplayer_id . '" href="javascript:;" onClick="loadSong(\'' . $jplayer_id . '\', \'' . $audio_filename . '\', \'' . $song_title . '\'); showPlayer(\'' . $jplayer_id . '\')"><img src="images/button-listen' . $button_class . '.png" width="77" height="18" border="0" /></a>';
                    $jplayer_buttons .= '<a id="player_off_btn_' . $jplayer_id . '" style="display: none;" href="javascript:;" onClick="hidePlayer(\'' . $jplayer_id . '\')"><img src="images/button-hide-white.png" width="77" height="18" border="0" /></a>';
                    $hide_player_call = 'hidePlayer(\'' . $jplayer_id . '\')';
                    $audio_file = '';

                }
    ?>
    
                <tr id="detail_row_<?php echo $take_id; ?>">
                    <td>
                        <div id="select_btn_<?php echo $take_id; ?>" class="select_btn"><a href="javascript:;" onClick="selectTake(<?php echo $take_id; ?>)">Select</a></div>
                        <div id="deselect_btn_<?php echo $take_id; ?>" class="selected_btn"><a href="javascript:;" onClick="deselectTake(<?php echo $take_id; ?>); <?php echo $hide_player_call; ?>">Selected</a></div>
                    </td>
                    <td><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_TAKE~MusicID|CasDel|Sorted::c_TakeDate', 0, $record, false, 'EDITTEXT', 'text')))?></td>
                    <td><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_VALUELIST~CompanyID::Value', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                    <td><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_TAKE~MusicID|CasDel|Sorted::t_Matrix', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                    <td><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('music_take_VALUELIST~VenueID::Value', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                    <td <?php echo $row_class; ?>><?php echo $jplayer_buttons; ?></td>
                </tr>
                                                                
    <?php
                if ($jplayer) {
    ?>
                    <tr id="jplayer_row_<?php echo $take_id; ?>">
                        <td colspan="6"><?php echo $jplayer; ?></td>
                    </tr>
    <?php                
                }
            }
            $record = $master_record;
        }
    ?>
    
        </table>
        
        <div style="clear: both; float: left; width: 45%;">
            <h4 />All Takes</h4>
            
            <table style="float: left;" cellpadding="3" cellspacing="0" width="100%" border="0">
                <tr>
                    <th style="border-bottom: 1px solid #000;">Artists</th>
                    <th>&nbsp;&nbsp;</th>
                    <th style="border-bottom: 1px solid #000;">Releases</th>
                    <th style="border-bottom: 1px solid #000;">&nbsp;</th>
                </tr>
    <?php
            
        // Populate the "All Takes" table
        $unique_artists = sizeof($all_artists);
        $table_rows = ($unique_artists > sizeof($unique_releases)) ? $unique_artists : sizeof($unique_releases);
        
        $artists_by_index = array();
        foreach ($all_artists as $artist => $take_ids) {
            array_push($artists_by_index, $artist);
        }
        
        $labels_by_index = array();
        $catalog_nums_by_index = array();
        foreach ($unique_releases as $key => $release_data) {
            array_push($labels_by_index, $release_data[0]);
            array_push($catalog_nums_by_index, $release_data[1]);
        }
        
        $row_class = '';
        for ($i=0; $i<$table_rows; $i++) {
            $row_class = ($row_class == '') ? 'class="alt"' : '';
    ?>
    
                <tr>
                    <td style="border-left: 1px solid #000; border-right: 1px solid #000;"><?php echo $artists_by_index[$i]; ?>&nbsp;</td>
                    <td>&nbsp;&nbsp;</td>
                    <td style="border-left: 1px solid #000;"><?php echo $labels_by_index[$i]; ?>&nbsp;</td>
                    <td style="border-right: 1px solid #000;"><?php echo $catalog_nums_by_index[$i]; ?>&nbsp;</td>
                </tr>
    
    <?php
        }
    ?>
                <tr>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                </tr>
            </table>
        </div>
    
        <div id="selected_take_detail_container" style="padding: 5px; margin-top: 15px; width: 45%; float: right; background-color: #dfe1ed;">
            <h4 style="padding-top: 0px;" />Selected Take</h4>
            
    <?php
    
        // Create the hidden take_details tables to be displayed when the user
        //  presses the "Select" button.
            foreach ($unique_take_ids as $take_id => $null) {
    ?>
                <table class="take_details" id="take_details_<?php echo $take_id; ?>" cellpadding="3" cellspacing="0" width="100%" border="0">
                    <tr>
                        <th style="width: 200px;  border-bottom: 1px solid #000;" colspan="2">Artists</th>
                        <th>&nbsp;&nbsp;</th>
                        <th style="border-bottom: 1px solid #000;">Releases</th>
                        <th style="border-bottom: 1px solid #000;">&nbsp;</th>
                    </tr>
    <?php
                
                $my_take_artists = explode('::', $take_artists[$take_id]);
                $labels_by_index = array();
                $catalog_nums_by_index = array();
                foreach ($all_releases as $key => $release_data) {
                    foreach ($all_releases[$key] as $value_pair) {
                        if ($value_pair[2] == $take_id) {
                            array_push($labels_by_index, Array($value_pair[0], $value_pair[3]));
                            array_push($catalog_nums_by_index, $value_pair[1]);
                        }
                    }
                }
                
                $artist_count = sizeof($my_take_artists);
                $release_count = sizeof($labels_by_index);
                $table_rows = ($artist_count > $release_count) ? $artist_count : $release_count;
                for ($i=0; $i<$table_rows; $i++) {
                    
                    $my_artist_data = explode('||', $my_take_artists[$i]);
    ?>
                    <tr>
                        <td style="border-left: 1px solid #000;"><a href="artist_detail.php?-action=browse&-recid=<?php echo $my_artist_data[1]; ?>"><?php echo $my_artist_data[0]; ?></a></td>
                        <td style="border-right: 1px solid #000;"><?php echo $my_artist_data[2]; ?></td>
                        <td>&nbsp;</td>
                        <td style="border-left: 1px solid #000;"><?php echo $labels_by_index[$i][0]; ?></td>
                        <td style="border-right: 1px solid #000;"><a href="release_detail.php?-action=browse&-recid=<?php echo $labels_by_index[$i][1]; ?>"><?php echo $catalog_nums_by_index[$i]; ?></a></td>
                    </tr>
    <?php
                }
    ?>
                <tr>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                    <td style="border-top: 1px solid #000;">&nbsp;</td>
                </tr>
            </table>
    <?php
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
                        swfPath: "./js",
                        supplied: "m4a, oga",
                        errorAlerts: true,
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
