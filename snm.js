/*  General JavaScript for the Skjellakk Norges Musikkhogskole website */

/*  ----------------------------------------------------------------------------
    Menuing System
----------------------------------------------------------------------------- */

var onMenu = defaultMenu;
var menuTimerId = '';
var searchMenu = 'off';

function menuHighlight(menuID) {
/*  Given a menu ID, switch the styling over to the "on" version of
    the menu. If no menuID is given, set everything back to the
    default state.
*/

    // Cancel the timer that automatically resets to default from inactivity
    clearTimeout(menuTimerId);
       
    // Set menuID back to defaults if no menuID given
    if (! menuID) menuID = defaultMenu;
    
    // If a menu's currently on display, hide it.
    if (onMenu) menuUnhighlight(onMenu);

    // Hide the search menu if it's open
    hideSearch();
    
    // Turn the toplevel nav on for the menuID given
    var mainID = menuID + '_nav';
    document.getElementById(mainID).style.backgroundImage = 'url(images/nav_background_selected.png)';
    
    // If this menu has a submenu, turn it on, too.
    var subID = menuID + '_subnav';
    if (document.getElementById(subID)) {
        document.getElementById(subID).style.display = 'block';
    }
    
    // Update the value of onMenu to the currently highlighted menuID
    onMenu = menuID;
}

function menuUnhighlight(menuID) {
/*  Given a menu ID, reset the top level and submenu back to the
    "off" states.
*/
    var mainID = menuID + '_nav';
    document.getElementById(mainID).style.backgroundImage = 'none';
    
    var subID = menuID + '_subnav';
    if (document.getElementById(subID)) {
        document.getElementById(subID).style.display = 'none';
    }
}

function resetNav() {
/*  Set a timer to turn off the menus and bring everything back to
    default after 1/2 second of inactivity.
*/
    if (searchMenu == 'off') menuTimerId = setTimeout("menuHighlight()", 500);
}

function toggleSearch(search_form) {
/*  If the search form is visible, hide it, if it's hidden, display it  */

    if (document.getElementById('search').style.display == 'none') {
        
        // Hide all of the search forms
        document.getElementById('artists_search').style.display = 'none';
        document.getElementById('music_search').style.display = 'none';
        document.getElementById('releases_search').style.display = 'none';
        
        // Display the search div and only the search form requested
        document.getElementById('search').style.display = 'block';
        document.getElementById(search_form).style.display = 'block';
        searchMenu = 'on';
    }
    else {
        document.getElementById('search').style.display = 'none';
        document.getElementById(search_form).style.display = 'none';
        searchMenu = 'off';
    }
}

function hideSearch() {
/*  Hide the search form and set searchMenu to "off"  */

    document.getElementById('search').style.display = 'none';
    searchMenu = 'off';
    
}

/*  ----------------------------------------------------------------------------
    Music Player
----------------------------------------------------------------------------- */

function showPlayer(id) {
    
    if (document.getElementById('player_' + id)) {
        document.getElementById('player_on_btn_' + id).style.display = 'none';
        document.getElementById('player_' + id).style.display = 'block';
        document.getElementById('player_off_btn_' + id).style.display = 'block';
    }
    
}

function hidePlayer(id) {
    
    if (document.getElementById('player_' + id)) {
        document.getElementById('player_off_btn_' + id).style.display = 'none';
        document.getElementById('player_' + id).style.display = 'none';
        document.getElementById('player_on_btn_' + id).style.display = 'block';
    }
    
}

/*  ----------------------------------------------------------------------------
    Select and deselect takes on the Music Details page
----------------------------------------------------------------------------- */
var selectedTake = 0;

function selectTake(id) {
    
    // If a take is currently selected, deselect it.
    if (document.getElementById('detail_row_' + selectedTake)) {
        deselectTake(selectedTake);
    }
    
    // Swap the Select button out and put the Deselect button in
    document.getElementById('select_btn_' + id).style.display = 'none';
    document.getElementById('deselect_btn_' + id).style.display = 'block';
    
    // Change the color of the row to the "selected" color
    document.getElementById('detail_row_' + id).style.backgroundColor = '#dfe1ed';
    
    // Display the details box if details exist for this take
    if (document.getElementById('take_details_' + id)) {
        document.getElementById('selected_take_detail_container').style.display = 'block';
        document.getElementById('take_details_' + id).style.display = 'block';
    }
    
    // Set the value of selectedTake to id
    selectedTake = id;
    
}

function deselectTake(id) {
    
    // Swap the Deselect button out and put the Select button in
    document.getElementById('select_btn_' + id).style.display = 'block';
    document.getElementById('deselect_btn_' + id).style.display = 'none';
    
    // Change the color of the row to the "deselected" color
    document.getElementById('detail_row_' + id).style.backgroundColor = '#fff';
    
    // Hide the details box if details exist for this take
    if (document.getElementById('take_details_' + id)) {
        document.getElementById('selected_take_detail_container').style.display = 'none';
        document.getElementById('take_details_' + id).style.display = 'none';
    }
    
    // Set the value of selectedTake to 0
    selectedTake = 0;
    
}
