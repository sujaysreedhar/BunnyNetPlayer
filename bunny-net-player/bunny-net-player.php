<?php
/*
Plugin Name: BunnyNet Player | Private Video Player for BunnyStream
Plugin URI: https://sujaysreedhar.com
Description: Plugin for embedding DRM'ed videos in wordpress, and let you put a private video using Bunny.Net APIs.
Version: 1.0
Author: Sujay Sreedhar
Plugin URI: https://github.com/sujaysreedhar/BunnyNetPlayer
Author URI: https://sujaysreedhar.com
*/

// Add settings menu
add_action('admin_menu', 'playbunny_add_admin_menu');
add_action('admin_init', 'playbunny_settings_init');

function playbunny_add_admin_menu() {
    add_options_page('PlayBunny Settings', 'PlayBunny', 'manage_options', 'playbunny', 'playbunny_options_page');
}

function playbunny_settings_init() {
    register_setting('playbunny_plugin_page', 'playbunny_settings');

    add_settings_section('playbunny_plugin_page_section', 'API Key Settings', 'playbunny_settings_section_callback', 'playbunny_plugin_page');

    add_settings_field('playbunny_api_key_field', 'API Key', 'playbunny_api_key_field_render', 'playbunny_plugin_page', 'playbunny_plugin_page_section');
}

function playbunny_api_key_field_render() {
    $options = get_option('playbunny_settings');
    ?>
    <input type='text' name='playbunny_settings[playbunny_api_key_field]' value='<?php echo $options['playbunny_api_key_field']; ?>'>
    <?php
}

function playbunny_settings_section_callback() {
    echo '<p>Enter your Bunny Stream API Key below.</p>';
}

function playbunny_options_page() {
    ?>
    <form action='options.php' method='post'>

        <h2>PlayBunny Settings</h2>

        <?php
        settings_fields('playbunny_plugin_page');
        do_settings_sections('playbunny_plugin_page');
        submit_button();
        ?>

    </form>
    <?php
}
// Add shortcode
add_shortcode('playbunny', 'playbunny_shortcode');

function playbunny_shortcode($atts) {
    $atts = shortcode_atts(array(
        'videoid' => '',
        'storageid' => ''
    ), $atts);

    $storage = $atts['storageid'];
    $videoId = $atts['videoid'];
    $options = get_option('playbunny_settings', array());
    $apiKey = isset($options['playbunny_api_key_field']) ? $options['playbunny_api_key_field'] : '';

    if (!$apiKey) {
        return '<p style="color: red;">PlayBunny API Key is not set. Please set it in the PlayBunny Settings page.</p>';
    }
$expiretime = time() + 3600;

    // Construct the secure URL
    $secureUrl = 'https://iframe.mediadelivery.net/embed/' . $storage . '/' . $videoId . '?token=' . hash('sha256',$apiKey . $videoId . $expiretime) . '&expires=' .$expiretime;

    return '<div style="position:relative;padding-top:56.25%;"><iframe src="' . $secureUrl . '&autoplay=true&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div> API:' . $apiKey . '<br> V+ID: ' . $secureUrl . '<br> URL: ' . $secureUrl;
}

