<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */

    require_once 'artist_fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    $layoutName = 'Artist: Detail Web';

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
    $snm = new SNM('artists');
    
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
        var defaultMenu = 'artists';
            
        function loadSong(playerID, fileName, songTitle) {
        /*  Given a playerID, fileName, and songTitle, load an instance of JPlayer
            with the requested song and set the display text to songTitle
        */
        
            //$("#jquery_jplayer_"+playerID).jPlayer("setMedia", {m4a: 'http://216.88.4.36/~administrator/Streaming/' + fileName + '.m4a'});
            //$("#jquery_jplayer_"+playerID).jPlayer("setMedia", {oga: 'http://216.88.4.36/~administrator/Streaming/' + fileName + '.ogg'});
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
        
        <?php
        //  Get the artist thumbnail if there is one.
        
            $display_image = 'style="display: none;"';
            $img_url = getImageURL(storeFieldNames('artist_ATTACHMENT~TableID|TableName|Thumbnail|Cre|Del::r_File', 0, $record, true, 'EDITTEXT', 'container'));
            if (preg_match('/=.+$/', $img_url)) {
                $display_image = '';
            }
        
        ?>
        <div id="artist_photo"><img <?php echo $display_image; ?> src="<?php echo $img_url; ?>" /></div>
        
        <div id="artist_stats">
            <h2><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('c_PrimaryName_FNF', 0, $record, false, 'EDITTEXT', 'text')))?></h2>
            <h4>
                 b. <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('c_BirthDate', 0, $record, false, 'EDITTEXT', 'text')))?><br />
                 d. <?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('c_DeathDate', 0, $record, false, 'EDITTEXT', 'text')))?>
             </h4>
        </div>
        
        <h4 style="clear: both;" />Takes</h4>
        
        <table cellpadding="3" cellspacing="0" width="100%" border="0">
            <tr>
                <th><a href="artist_detail.php?-action=browse&-sortfieldone=artist_takejoinartist_TAKE~TakeID::c_TakeDate&-sortorderone=descend&-sortfieldtwo=artist_takejointartist_take_VALUELIST~CompanyID::Value&-sortordertwo=ascend&-recid=<?php echo $recid; ?>">Take Date</a></th>
                <th>Manufacturing Company</th>
                <th>Matrix</th>
                <th>Role</th>
                <th>Composer</th>
                <th>Title</th>
                <th>&nbsp</th>
            </tr>
        <?php
		                   
            $relatedRecords = $record->getRelatedSet("artist_TAKEJOINARTIST~ArtistID");
            $portal = $layout->getRelatedSet("artist_TAKEJOINARTIST~ArtistID");

            $jplayer_id = 0;
            $audio_file_map = array();
            if (FileMaker::isError($relatedRecords) === false) {
                $recnum = 0;
                $master_record = $record;
                foreach ($relatedRecords as $record) {
					
                    $row_class = ($recnum % 2 == 0) ? 'class="alt"' : '';
                    $button_class = ($recnum % 2 == 0) ? '-white' : '';
					$recnum++;
					
					if ($recnum == 1) {
						//$cst = var_dump(get_object_vars($record));
						//echo '<pre>' . $cst . '</pre>';
					}
					else {
						//$cst = '';
					}
                    $audio_file = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_take_ATTACHMENT~TableID|TableName||Audio::t_Path', 0, $record, true, 'EDITTEXT', 'text')));
                    $jplayer = '';
                    $jplayer_buttons = '&nbsp;';
                    if ($audio_file) {
                        
                        $jplayer_id++;
                        $audio_filename = $audio_file;
                        $audio_file_map[$jplayer_id] = $audio_filename;
                        $song_title = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_sessionjoinartist_session_MUSIC~MusicID::c_TitlePrimary', 0, $record, false, 'EDITTEXT', 'text')));
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
                        $jplayer_buttons = '<a id="player_on_btn_' . $jplayer_id . '" href="javascript:;" onClick="loadSong(\'' . $jplayer_id . '\', \'' . $audio_filename . '\', \'' . $song_title . '\'); showPlayer(\'' . $jplayer_id . '\')"><img src="images/button-listen' . $button_class . '.png" width="77" height="18" border="0" /></a>';
                        $jplayer_buttons .= '<a id="player_off_btn_' . $jplayer_id . '" style="display: none;" href="javascript:;" onClick="hidePlayer(\'' . $jplayer_id . '\')"><img src="images/button-hide' . $button_class . '.png" width="77" height="18" border="0" /></a>';
                        $audio_file = '';

                    }		
        ?>
                    <tr>
                        <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_TAKE~TakeID::c_TakeDate', 0, $record, false, 'EDITTEXT', 'text')))?></td>
                        <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejointartist_take_VALUELIST~CompanyID::Value', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                        <!-- <td <?php echo $row_class ?>><a href="music_detail.php?-action=browse&-recid=<?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_TAKE~TakeID::_fk_MusicID', 0, $record, true, 'EDITTEXT', 'number')))?>"><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_TAKE~TakeID::t_Matrix', 0, $record, true, 'EDITTEXT', 'text')))?></a></td> -->
                        <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_TAKE~TakeID::t_Matrix', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                        <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_TAKEJOINARTIST~ArtistID::t_Role', 0, $record, false, 'EDITTEXT', 'text')))?></td>
                        <td <?php echo $row_class ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_sessionjoinartist_session_music_ARTIST~ComposerID::c_PrimaryName_FNF', 0, $record, false, 'EDITTEXT', 'text')))?></td>
                        <td <?php echo $row_class ?>><a href="music_detail.php?-action=browse&-recid=<?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_takejoinartist_TAKE~TakeID::_fk_MusicID', 0, $record, true, 'EDITTEXT', 'number')))?>"><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_sessionjoinartist_session_MUSIC~MusicID::c_TitlePrimary', 0, $record, false, 'EDITTEXT', 'text')))?></a></td>
                        <td <?php echo $row_class; ?>><?php echo $jplayer_buttons; ?></td>
                    </tr>
        
        <?php
                        if ($jplayer) {
        ?>
                            <tr <?php echo $take_id; ?>">
                                <td  <?php echo $row_class ?> colspan="7"><?php echo $jplayer; ?></td>
                            </tr>
         <?php                
                        }                       
                    }
                    $record = $master_record;
                }
        ?>
        </table>
        
        <h4 style="clear: both;" />Additional Files, Audio &amp; Images</h4>

        <table cellpadding="3" cellspacing="0" width="100%" border="0">
            <tr>
                <th>Description</th>
                <th>File Name</th>
            </tr>
        
        <?php
                                                                            
            $relatedRecords = $record->getRelatedSet("artist_ATTACHMENT~TableID|TableName|File|Cre|Del");
            $portal = $layout->getRelatedSet("artist_ATTACHMENT~TableID|TableName|File|Cre|Del");
            if (FileMaker::isError($relatedRecords) === false) {
                $recnum = 0;
                $master_record = $record;
                foreach ($relatedRecords as $record) {
                    $row_class = ($recnum % 2 == 0) ? '' : 'class="alt"';
                    $recnum++;
                    
                    $raw_filename = nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_ATTACHMENT~TableID|TableName|File|Cre|Del::c_FileName', 0, $record, false, 'EDITTEXT', 'text')));
                    preg_match('/^[^:]+:(.+)</', $raw_filename, $rf);
                    $filename = $rf[1];
                    $link_url = './attachments/' . $filename;
            ?>

            <tr>
                <td <?php echo $row_class; ?>><?php echo nl2br(str_replace(' ', '&nbsp;', storeFieldNames('artist_ATTACHMENT~TableID|TableName|File|Cre|Del::Description', 0, $record, true, 'EDITTEXT', 'text')))?></td>
                <td <?php echo $row_class; ?>><a href="<?php echo $link_url; ?>"><?php echo $filename; ?></a></td>
            </tr>

        <?php
                }
            }
        ?>
        </table>
    </div>
    
    <?php
    
        if ($jplayer_id > 0) {
            
    ?>
            <script type="text/javascript" language="javascript">
            <!--
            
                $(document).ready(function(){
            
    <?php
            foreach ($audio_file_map as $player_id => $audio_file) {
    ?>            
                    $("#jquery_jplayer_<?php echo $player_id; ?>").jPlayer({
                        ready: function (event) {
                            $(this).jPlayer("setMedia", {
                                m4a:"http://216.88.4.36/~administrator/Streaming/<?php echo $audio_file; ?>",
                                oga:"http://216.88.4.36/~administrator/Streaming/<?php echo $audio_file; ?>.ogg"
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
