<?php
/**
 * @package User Time Zone
 * @version 2.0
 */
/*
/*
Plugin Name: User Time Zone
Plugin URI:
Description: Set personal time zones.
Version: 2.0
Author: Salerat, saleratwork@gmail.com




*/
ini_set('display_errors', 'Off');


class UseClientsTimezone
{


    protected $fallback_timezone;
    protected $plugin_path = '';

    public function __construct()
    {
        require(ABSPATH . WPINC . '/pluggable.php');
        global $current_user;

        get_currentuserinfo();

        $this->fallback_timezone = get_usermeta($current_user->ID, 't-zone');

        if (empty($this->fallback_timezone)) {
            $this->fallback_timezone = 0;
            update_usermeta($current_user->ID, 't-zone', $this->fallback_timezone);

        }

        if (isset($_POST['tz'])) {

            $this->fallback_timezone = $_POST['tz'];
            update_usermeta($current_user->ID, 't-zone', $this->fallback_timezone);
        }

        add_action('show_user_profile', array(&$this, 'draw_options_page'));
        add_action('edit_user_profile', array(&$this, 'draw_options_page'));
        add_action('profile_update', array(&$this, 'update_time_zone'));

        add_action('admin_menu', array(&$this, 'admin_add_page'));

    }

    public function update_time_zone()
    {

    }

    public function initialize_admin()
    {
        if (function_exists('register_setting')) {
            $page_for_settings = 'use_clients_timezone_plugin';
            $section_for_settings = 'use_clients_timezone_section';
            add_settings_section(
                $section_for_settings,
                'Use Client&#039;s Time Zone Settings',
                array(&$this, 'use_clients_timezone_section_heading'),
                $page_for_settings
            );
            add_settings_field(
                'use_clients_timezone_fallback_timezone_id',
                'Fallback time zone',
                array(&$this, 'use_clients_timezone_setting_values'),
                $page_for_settings,
                $section_for_settings
            );
            register_setting(
                'use_clients_timezone_settings',
                'use_clients_timezone_fallback_timezone',
                'wp_filter_nohtml_kses'
            );
        }
    }


    public function use_clients_timezone_setting_values()
    {
        global $current_user;
        $use_clients_timezone_fallback_timezone = get_usermeta($current_user->ID, 't-zone');

        echo '<input id="use_clients_timezone_fallback_timezone_input" name="use_clients_timezone_fallback_timezone" size="35" type="text" value="' . $use_clients_timezone_fallback_timezone . '" />';
    }

    public function admin_add_page()
    {
        add_options_page(
            'Use Client&#039;s Time Zone Settings',
            'Use Client&#039;s Time Zone',
            'read',
            'use_clients_timezone_plugin',
            array(&$this, 'draw_options_page')
        );
//add_options_page('Use Client&#039;s Time Zone', 'Use Client&#039;s Time Zone', 'read', __FILE__, 'use_clients_timezone_plugin');

    }

    public function draw_options_page()
    {
        echo '<div><h2>Time Zone Options</h2>';
        echo '<input type="hidden" id="utc_offset" value="' . $this->fallback_timezone . '">';

        echo '<select name="tz" value="">';
        echo '<option selected value="">Select UTC</option>';
        for ($i = -11; $i <= 12; $i = $i + 0.5) {
            echo '<option value="' . $i . '" >UTC ' . $i . '</option>';
        }
        echo '</select>';

        echo '
	<script src="http://code.jquery.com/jquery-1.10.1.min.js" ></script>
	
	<script>
	    utc=$("#utc_offset").val();
	    var str = new String(utc);

	    $("select[name=tz] option").each(function() {
	    if($(this).val()==str) {
	    $(this).attr("selected","selected");
	    }
	    });
	</script>
	';

        echo '</div>';
        echo '

</div></div>';
    }


    /**
     * В этой функции ставим время для локальных фукнций ВП и date_default_timezone_set пхп (потомучто мы не знаем, какие функции будет юзать юзер для вывода времени
     */
    public function setTimezone()
    {
        update_option('gmt_offset', $this->fallback_timezone, '', 'yes');


    }


}

if (class_exists('UseClientsTimezone')) {
    $use_clients_timezone = new UseClientsTimezone();
    if (isset($use_clients_timezone)) {
        add_action('plugins_loaded', array(&$use_clients_timezone, 'setTimezone'), 1);
    }
}

/**
 * Накладываем фильтр на the_time, но по хорошему, нужно накладывать на все функции времени.
 */
function user_time_zone_filter($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_date_gmt) + ($tz * 3600);


    return date($time_format, $time);
}

function user_time_zone_get_the_time($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_date_gmt) + ($tz * 3600);


    return date(" g:i a", $time);

}

function user_time_zone_get_the_date($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_date_gmt) + ($tz * 3600);


    return date("F j, Y", $time);

}

function get_the_modified_time_new($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_modified_gmt) + ($tz * 3600);


    return date(" g:i a", $time);

}

function the_modified_time_new($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_modified_gmt) + ($tz * 3600);


    return date($time_string, $time);

}

function get_the_modified_date_new($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_modified_gmt) + ($tz * 3600);


    return date("F j, Y", $time);

}

function the_date_new($time_string, $time_format)
{

    global $post;

    global $current_user;

    get_currentuserinfo();


    $tz = get_usermeta($current_user->ID, 't-zone');

    if (!is_numeric($tz)) {
        return $time_string;
    }
    $tz = (float)$tz;
    if ($tz < -12 || $tz > 12) {
        return $time_string;
    }


    $time = strtotime($post->post_date_gmt) + ($tz * 3600);


    return date("F j, Y", $time);

}

add_filter('the_date', 'the_date_new', 1, 2);
add_filter('get_the_modified_date', 'get_the_modified_date_new', 1, 2);
add_filter('the_modified_time', 'the_modified_time_new', 1, 2);
add_filter('get_the_modified_time', 'get_the_modified_time_new', 1, 2);

add_filter('get_the_time', 'user_time_zone_get_the_time', 1, 2);
add_filter('get_the_date', 'user_time_zone_get_the_date', 1, 2);
add_filter('the_time', 'user_time_zone_filter', 1, 2);


?>