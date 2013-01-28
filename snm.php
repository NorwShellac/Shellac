<?php

class SNM {
    
    
    function __construct($section='') {
        $this->section = $section;
        $this->artist_list_override = '';
        $this->artist_list_label = 'List';
        $this->music_list_override = '';
        $this->music_list_label = 'List';
        $this->releases_list_override = '';
        $this->releases_list_label = 'List';
    }
    
    
    public function display_menu() {
    /*  Using the object's section and page values, set the menu up to have
        the right things highlighted and displayed.
    */
    
        // Define the sections and sub sections
        $site_sections = array('home', 'artists', 'music', 'releases', 'news');
        $main_nav_style = array();
        $sub_nav_style = array();
        
        // Set the CSS style of each section to either 'main_nav_on' or 'main_nav';
        // Set the CSS style of each sub section either 'secondary_nav_on' or 'secondary_nav'
        foreach ($site_sections as $section) {
            $main_nav_style[$section] = ($this->section == $section) ? 'main_nav_on' : 'main_nav';
            $sub_nav_style[$section] = ($this->section == $section) ? 'secondary_nav_on' : 'secondary_nav'; 
        }
        
        // Set the CSS style of the current sub page to underline ... if we're
        // currently on a sub page.
        preg_match('/\/([^\/]+)$/', $_SERVER["SCRIPT_NAME"], $m);
        $calling_script = $m[1];
        $sub_page_style = array($calling_script => 'style="text-decoration: underline; display: inline;" ');
        
        // If we're on a detail page, then bump the search fields for this
        // section a little over to the right so that they line up with the
        // word "Search".  The array below indicates:
        // 'site section' => array("index in this array to use as the CSS position", "default position in pixels", "alternate position in pixels")
        $search_pos = array(
            'artists'   => array("1", "140", "205"),
            'music'     => array("1", "215", "280"),
            'releases'  => array("1", "280", "345"),
        );
        if (preg_match('/_detail/', $calling_script)) {
        // If we're on a detail page, set the array for this section to use
        // the alternate CSS position.
            $search_pos[$this->section][0] = "2";
        }
        
        ?>
        <!-- HEADER -->
        <div id="header">
            <a href="."><img src="images/logo.png" width="330" height="71" alt="Skjellakk Norges Musikkhogskole" border="0" /></a>
        </div>
        
        <!-- NAVIGATION -->
        <div id="navigation1">
            <div id="home_nav" class="<?php echo $main_nav_style['home'] ?>" style="width: 50px;"><a href="." onMouseOver="menuHighlight('home')" onMouseOut="resetNav()">Home</a></div>
            <div id="artists_nav" class="<?php echo $main_nav_style['artists'] ?>" style="width: 60px;"><a href="javascript:;" onMouseOver="menuHighlight('artists')" onMouseOut="resetNav()">Artists</a></div>
            <div id="music_nav" class="<?php echo $main_nav_style['music'] ?>" style="width: 50px;"><a href="javascript:;" onMouseOver="menuHighlight('music')" onMouseOut="resetNav()">Music</a></div>
            <div id="releases_nav" class="<?php echo $main_nav_style['releases'] ?>" style="width: 70px;"><a href="javascript:;" onMouseOver="menuHighlight('releases')" onMouseOut="resetNav()">Releases</a></div>
            <div id="news_nav" class="<?php echo $main_nav_style['news'] ?>" style="float: right; margin-right: 15px; width: 50px;"><a href="news.php" onMouseOver="menuHighlight('news')" onMouseOut="resetNav()">News</a></div>
        </div>
        
        <div id="navigation2">
            <div id="artists_subnav" class="<?php echo $sub_nav_style['artists'] ?>" style="margin-left: 80px;">
                <a <?php echo $sub_page_style['artist_list.php'] ?>href="artist_list.php<?php echo $this->artist_list_override ?>" onMouseOver="menuHighlight('artists')" onMouseOut="resetNav()"><?php echo $this->artist_list_label; ?></a>
                <a class="details" <?php echo $sub_page_style['artist_detail.php'] ?>href="javascript:;" onMouseOver="menuHighlight('artists')" onMouseOut="resetNav()">Detail</a>
                <a href="javascript:;" onMouseOver="menuHighlight('artists')" onClick="toggleSearch('artists_search')" onMouseOut="resetNav()">Search</a>
<!--                <a href="artist_search.php" onMouseOver="menuHighlight('artists');" onMouseOut="resetNav()">Search</a>-->
            </div>

            <div id="music_subnav" class="<?php echo $sub_nav_style['music'] ?>" style="margin-left: 155px;">
                <a <?php echo $sub_page_style['music_list.php'] ?>href="music_list.php<?php echo $this->music_list_override ?>" onMouseOver="menuHighlight('music')" onMouseOut="resetNav()"><?php echo $this->music_list_label; ?></a>
                <a class="details" <?php echo $sub_page_style['music_detail.php'] ?>href="javascript:;" onMouseOver="menuHighlight('music')" onMouseOut="resetNav()">Detail</a>
                <a href="javascript:;" onMouseOver="menuHighlight('music')" onClick="toggleSearch('music_search')" onMouseOut="resetNav()">Search</a>
                <!--<a href="music_search.php" onMouseOver="menuHighlight('music');" onMouseOut="resetNav()">Search</a>-->
            </div>

            <div id="releases_subnav" class="<?php echo $sub_nav_style['releases'] ?>" style="margin-left: 220px;">
                <a <?php echo $sub_page_style['release_list.php'] ?>href="release_list.php<?php echo $this->releases_list_override ?>" onMouseOver="menuHighlight('releases')" onMouseOut="resetNav()"><?php echo $this->releases_list_label; ?></a>
                <a class="details" <?php echo $sub_page_style['release_detail.php'] ?>href="javascript:;" onMouseOver="menuHighlight('releases')" onMouseOut="resetNav()">Detail</a>
                <a href="javascript:;" onMouseOver="menuHighlight('releases')" onClick="toggleSearch('releases_search')" onMouseOut="resetNav()">Search</a>
                <!--<a href="release_search.php" onMouseOver="menuHighlight('releases');" onMouseOut="resetNav()">Search</a>-->
            </div>
        </div>
        
        <div id="search" style="display: none;">
            
            <!-- ARTIST SEARCH -->
            <div id="artists_search" style="display: none; padding-top: 10px; margin-left: <?php echo $search_pos['artists'][$search_pos['artists'][0]] ?>px;">
                <iframe src="artist_search_iframe.php" frameborder="0" scrolling="no"></iframe>
            </div>
            
            <!-- MUSIC SEARCH -->
            <div id="music_search" style="display: none; padding-top: 10px; margin-left: <?php echo $search_pos['music'][$search_pos['music'][0]] ?>px;">
                <iframe style="width: 525px;" src="music_search_iframe.php" frameborder="0" scrolling="no"></iframe>
            </div>
            
            <!-- RELEASES SEARCH -->
            <div id="releases_search" style="display: none; padding-top: 10px; margin-left: <?php echo $search_pos['releases'][$search_pos['releases'][0]] ?>px;">
                <iframe style="width: 550px;" src="release_search_iframe.php" frameborder="0" scrolling="no"></iframe>
            </div>
            
        </div>
        
        <?php
        
    }
    
    public function get_page_title() {
    /*  Return the appropriate text for the title tag for the requesting page */
    
        return 'Norwegian Shellac Artists';
        
    }
}

?>