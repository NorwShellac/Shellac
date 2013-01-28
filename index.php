<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php

    /**
    * FileMaker PHP Site Assistant Generated File
    */
    require_once 'home_fmview.php';
    require_once 'FileMaker.php';
    require_once 'error.php';

    $cgi = new CGI();
    $cgi->storeFile();
    
    $databaseName = 'Shellac';
    //$layoutName = 'Web: Home';
    $layoutName = 'Homepage';

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
    $snm = new SNM('home');
    
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
        var defaultMenu = 'home';
        
    -->
    </script>
    <script type="text/javascript" src="snm.js"></script>
</head>

<body>
    <?php $snm->display_menu(); ?>
    
    <!-- MAIN CONTENT -->
    <div id="main_content">
        <div id="main_content_left_col">
            <div id="about_box">
                <h2>About Skjellack!!!!!!!!!!</h2>
                
                <p><?php echo nl2br(storeFieldNames('t_About', 0, $record, true, 'EDITTEXT', 'text'))?></p>
            </div>
            
            <h2>Featured Release</h2>
			
			<h3><?php echo nl2br(storeFieldNames('homepage_RELEASE::Name', 0, $record, true, 'EDITTEXT', 'text'))?></h3>
            
			<?php
			
			    $raw_audio_file = nl2br(str_replace(' ', '%20', storeFieldNames('homepage_ATTACHMENT~Audio::c_FileName', 0, $record, true, 'EDITTEXT', 'text')));
                preg_match('/^[^:]+:(.+)</', $raw_audio_file, $rf);
                $audio_file = $rf[1];
				$raw_song_title = $audio_file;
                preg_match('/([^.]+)\./', $raw_song_title, $rst);
                $song_title = str_replace('%20', '&nbsp;', $rst[1]);
				
			?>
		
            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
            <div id="jp_container_1" class="jp-audio">
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
                                <li id="song_title_1" style="padding: 0px;"><?php echo $song_title; ?></li>
                            </ul>
                        </div>
                        <div class="jp-no-solution">
                            <span>Update Required</span>
                            To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                        </div>
                    </div>
                </div>
            </div>
            
			<?php
				$release_thumb = getImageURL(storeFieldNames('ReleaseThumb', 0, $record, true, 'EDITTEXT', 'container'));
			?>
			
            <p style="margin-top: 20px;"><img class="padded" src="<?php echo $release_thumb; ?>" width="180" height="180" align="left" />
                <?php echo nl2br(storeFieldNames('homepage_RELEASE::FeaturedText', 0, $record, true, 'EDITTEXT', 'text'))?>
            </p>
            
        </div>
        
        <div id="main_content_right_col">
            <h2>Featured Artist</h2>
			
			<?php
				$artist_thumb = getImageURL(storeFieldNames('ArtistThumb', 0, $record, true, 'EDITTEXT', 'container'));
			?>
            
            <h3>
                <img class="padded" src="<?php echo $artist_thumb; ?>" width="138" height="142" align="left" />
				<?php echo nl2br(storeFieldNames('homepage_ARTIST::c_PrimaryName_FNF', 0, $record, true, 'EDITTEXT', 'text'))?>
            </h3>
            
            <p>
                <?php echo nl2br(storeFieldNames('homepage_ARTIST::FeaturedText', 0, $record, true, 'EDITTEXT', 'text'))?>
            </p>
        </div>
        
        <br style="clear: both;" />
    </div>
        
    <script type="text/javascript" language="javascript">
    <!--
    
	<?php
		
		$raw_mp3 = nl2br(storeFieldNames('homepage_ATTACHMENT~Audio::c_FileName', 0, $record, true, 'EDITTEXT', 'text'));
		preg_match('/^[^:]+:(.+)</', $raw_mp3, $rf);
        $mp3_file = $rf[1];
		
		$raw_ogg = nl2br(storeFieldNames('homepage_ATTACHMENT~Audio::c_FileName_ogg', 0, $record, true, 'EDITTEXT', 'text'));
		preg_match('/^[^:]+:(.+)</', $raw_ogg, $rf);
        $ogg_file = $rf[1];
		
	?>
	
        $(document).ready(function(){
            $("#jquery_jplayer_1").jPlayer({
                ready: function (event) {
                    $(this).jPlayer("setMedia", {
                        m4a:"./attachments/<?php echo $mp3_file; ?>",
                        oga:"./attachments/<?php echo $ogg_file; ?>"
                    });
                },
                swfPath: "/js",
                supplied: "m4a, oga",
                //errorAlerts: true,
            });
            $("#jplayer_inspector").jPlayerInspector({jPlayer:$("#jquery_jplayer_1")});
        });
        
    -->
    </script>
	
</body>
</html>
