<?php
/*
Plugin Name: PeyDev
Plugin URI: https://amadratitus.github.io/peydev/
Description: This plugin adds functionality to remind clients to pay their developers.
Version: 1.0
Author: Amadra Titus
Author URI: https://github.com/amadratitus/
Text Domain: pey-devs
License: GPLv2 or later
*/

// Add plugin menu item
function peydevs_menu() {
    add_menu_page('PeyDevs Settings', 'PeyDevs', 'manage_options', 'peydevs_settings', 'peydevs_settings_page', 'dashicons-money');
}
add_action('admin_menu', 'peydevs_menu');

// Register plugin settings
function peydevs_register_settings() {
    register_setting('peydevs_options_group', 'peydevs_vanishing_date');
    register_setting('peydevs_options_group', 'peydevs_status');
}
add_action('admin_init', 'peydevs_register_settings');

// Plugin settings page
function peydevs_settings_page() {
    ?>
    <div class="wrap">
        <h2>PeyDevs Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('peydevs_options_group'); ?>
            <?php do_settings_sections('peydevs_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Vanishing Date</th>
                    <td><input type="date" name="peydevs_vanishing_date" value="<?php echo esc_attr(get_option('peydevs_vanishing_date')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Plugin Status</th>
                    <td>
                        <label><input type="radio" name="peydevs_status" value="1" <?php checked( get_option('peydevs_status'), 1 ); ?> /> Active</label><br/>
                        <label><input type="radio" name="peydevs_status" value="0" <?php checked( get_option('peydevs_status'), 0 ); ?> /> Inactive</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue script and styles
function peydevs_scripts_and_styles() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue custom JavaScript
    wp_enqueue_script('peydevs-script', plugin_dir_url(__FILE__) . 'peydevs-script.js', array('jquery'), '1.0', true);

    // Enqueue custom CSS
    wp_enqueue_style('peydevs-style', plugin_dir_url(__FILE__) . 'peydevs-style.css');
}
add_action('wp_enqueue_scripts', 'peydevs_scripts_and_styles');

// Custom JavaScript
add_action('wp_footer', 'peydevs_custom_js');
function peydevs_custom_js() {
    ?>
    <script>
	        jQuery(document).ready(function($) {
		    // Check if plugin is active
		    var pluginStatus = <?php echo get_option('peydevs_status', 1); ?>;

		    // If plugin is active
		    if (pluginStatus == 1) {
		        // Vanishing Date YY-MM-DD
		        var vanishing_date = new Date('<?php echo esc_js(get_option('peydevs_vanishing_date')); ?>'),
		            current_date = new Date(),
		            utc1 = Date.UTC(vanishing_date.getFullYear(), vanishing_date.getMonth(), vanishing_date.getDate()),
		            utc2 = Date.UTC(current_date.getFullYear(), current_date.getMonth(), current_date.getDate()),
		            days = Math.floor((utc1 - utc2) / (1000 * 60 * 60 * 24));

		        if (days <= 0) {
		            $('body > *').fadeOut(5000);
		            setTimeout(function() {
		                var clientMessage = '<div class="client-message"><p>Please... <br/>Pay Your Developer!</p></div>';
		                $('body').append(clientMessage);
		                $('.client-message').fadeIn();
		                
		                // Append footer
		                var footerYear = '<?php echo date("Y"); ?>';
		                var footer = '<footer>&copy; ' + footerYear + ' PeyDev By <a href="https://amadratitus.github.io/peydev/" target="_blank">PeyDev Inc</a></footer>';
		                $('.client-message').append(footer);
		            }, 5000);
		        }
		    }
		});
    </script>
    <?php
}

// Custom CSS
add_action('wp_head', 'peydevs_custom_css');
function peydevs_custom_css() {
    ?>
    <style>
        .client-message {
            background: linear-gradient(to right, #f32170, 
                    #ff6b08, #cf23cf, #eedd44); 
            -webkit-text-fill-color: transparent; 
            -webkit-background-clip: text;

            font-family: Poppins !important;
            font-weight: bold !important;
            font-size: 6vw !important;
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            padding: 5% !important;
        }

        .client-message p {
            font-family: Poppins !important;
            font-weight: bold !important;
            font-size: 6vw !important;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.3;
        }

        .client-message footer {
            background: linear-gradient(to right, #074173, 
                    #5BBCFF, #074173, #5BBCFF); 
            -webkit-text-fill-color: transparent; 
            -webkit-background-clip: text;
            position: absolute;
            bottom: 0;
            width: 100%;
            color: #5BBCFF;
            text-align: center;
            justify-content: center;
            font-size: 12px;
            padding-bottom: 5px;
        }

        .client-message a {
            color: #074173;
            font-size: 11px;
        }

    </style>
    <?php
}

?>