<?php
/*
Plugin Name: Fantazy Sidebar | Wordpress floating sidebar
Plugin URI: https://github.com/Jakiboy/Fantazy-Sidebar
Description: Makes wordpress's sidebar floatable, compatible with version <a href="https://fr.wordpress.org/">4.2.2</a>
Version: 1.5
Author: JIHAD SINNAOUR
Author URI: http://info.jihadsinnaour.com
License: GPL2
**/
/**
*@Company   : Viaprestige
*@author    : JIHAD SINNAOUR
*@package   : Wordpress sidebar
*/
function fsGlobals(){
    return array(
        'contentID'=>'#content',  // Base of page's height, for #sidebar
        'sidebarID'=>'#sidebar', // Wordpress sidbare's ID
        'waitingTime'=>2000,    // Time before moving sidebar
        'debounce'=>500,       // Sidebar limits
        'animate'=>500,       // Animation duration
        'offsetTop'=>0,      // Margin top
        'offsetBottom'=>0,  // Margin bottom
        'minHDiff'=>0      // Min height difference
    );
}
function fsGetOptions(){
    $options=fsGlobals();
    foreach($options as $option=>$val){
        $options[$option]=get_option('wp-fantazy-sidebar-'.$option, $val);
    }
    return $options;
}
function fsStartApp(){
    $options=fsGetOptions();
    echo '<script type="text/javascript">fs.start('.json_encode($options).');</script>';
}
function fsMenuSettings(){
    add_options_page(__('Fantazy Sidbar','fs'), __('Fantazy Sidbar','fs'), 'manage_options', 'fsSettings', 'fsSettings');
}

function fsSettingsInput($name, $value, $label, $cls='regular-text'){
    $data ='   <tr>';
    $data.='    <th><label for="wp-fantazy-sidebar-'.$name.'">'.$label.'</label></th>';
	$data.='   </tr><tr>';
    $data.='    <td><input class="'.$cls.'" id="wp-fantazy-sidebar-'.$name.'" name="wp-fantazy-sidebar-'.$name.'" value="'.$value.'"></td>';
    $data.='   </tr>';
    return $data;
}
function fsSettings(){
    if (!current_user_can('manage_options')){ wp_die( __('You do not have sufficient permissions to access this page.') ); }
    //Previous Saved Values or Default Ones
    $options=fsGetOptions();

    //Update options
    if( isset($_POST[ 'wp-fantazy-sidebar' ]) ) {
        foreach($options as $option=>$val){
            $options[$option]=$_POST['wp-fantazy-sidebar-'.$option];
            update_option( 'wp-fantazy-sidebar-'.$option, $options[$option] );
        }
    }
    extract($options);
    // input output settings
    $data ='<div class="wrap">';
    $data.='<h2>Fantazy Sidbar 1.5</h2>';
    $data.='<form id="wp-fantazy-sidebar-form" name="wp-fantazy-sidebar-form" method="post" action="">';
    $data.='<input id="wp-fantazy-sidebar" name="wp-fantazy-sidebar" type="hidden" value="1">';
    $data.='<table class="form-table">';
	$data.=fsSettingsInput('contentID',$contentID,
	'<b>Content Selector</b>');
    $data.=fsSettingsInput('sidebarID',$sidebarID,
	'<b>Sidebar Selector</b>');
	$data.=fsSettingsInput('waitingTime',$waitingTime,
	'<b>Wait</b> Milliseconds Before Activation, after page has loaded','small-text');
    $data.=fsSettingsInput('debounce',$debounce,
	'Milliseconds Of <b>Inactivity Before Every Reposition</b>. '.
	'Sidebar will start moving only after this time from when the user has stopped scrolling up or down','small-text');
    $data.=fsSettingsInput('animate',$animate,
	'<b>Animate Speed</b> in Milliseconds; how much time will the sidebar take to go to align itself with the content','small-text');
    $data.=fsSettingsInput('offsetTop',$offsetTop,
    '<b>Offset Top</b>; lets you adjust settings for a pixel perfect result; accepts positive and negative values','small-text');
    $data.=fsSettingsInput('offsetBottom',$offsetBottom,
    '<b>Offset Bottom</b>','small-text');
    $data.=fsSettingsInput('minHDiff',$minHDiff,
	'<b>Minimum Height Difference</b>; if (container height - sidebar height < minHDiff) then the plugin is not activated; if <i>dynamicTop</i> is checked, this option is not considered','small-text');
    $data.='<tr><td><p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p></td></tr>';
    $data.='</table>';
    $data.='</form>';
    $data.='</div>';
    echo $data;
}
//Registering scripts and plugin hooks
if ( !is_admin() ) {
    function fantazySidebarRun(){
    wp_register_script('debounce', plugins_url('/core/js/debounce.js', __FILE__ ));
    wp_register_script('wp-fantazy-sidebar', plugins_url('/core/js/wp-fantazy-sidebar.js',__FILE__ ));
    wp_enqueue_script('debounce');
    wp_enqueue_script('wp-fantazy-sidebar');
    }
    add_action( 'wp_enqueue_scripts', 'fantazySidebarRun' );
    //add_action('wp_footer','fsStartApp');
    $option=fsGetOptions();
    add_action( ($option['jsInHead']?'wp_head':'wp_footer') , 'fsStartApp');
}
else{add_action('admin_menu', 'fsMenuSettings');}