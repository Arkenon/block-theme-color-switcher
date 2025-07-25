<?php
/**
 * Plugin Name: Block Theme Color Switcher
 * Plugin URI: https://github.com/Arkenon/block-theme-color-switcher
 * Description: This plugin allows users to choose a color palette for the Block Theme from the frontend.
 * Version: 1.0.5
 * Author: Kadim Gültekin
 * Author URI: https://kadimgultekin.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: block-theme-color-switcher
 */

// Prevent direct access to the file.
defined('ABSPATH') || exit();

// Define constants
$plugin_data = get_file_data(__FILE__, array('version' => 'Version'));
define('BLOCK_THEME_COLOR_SWITCHER_VERSION', $plugin_data['version']);
define('BLOCK_THEME_COLOR_SWITCHER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Enqueue JavaScript for color switcher.
 */
if (!function_exists('block_theme_color_switcher_enqueue_script')) {

    function block_theme_color_switcher_enqueue_script(): void
    {

        wp_enqueue_script(
            'block-theme-color-switcher',
            BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL . 'js/block-theme-color-switcher.js',
            'jquery',
            BLOCK_THEME_COLOR_SWITCHER_VERSION,
            true // Load in footer.
        );

        $palettes = block_theme_color_switcher_merge_color_palettes();
        $palettes_json = wp_json_encode($palettes);
        $inline_script_for_palette = "const palettes = " . $palettes_json . ";";
        wp_add_inline_script('block-theme-color-switcher', $inline_script_for_palette, 'before');

        $default_colors = block_theme_color_switcher_get_theme_color_palette();
        $default_colors_json = wp_json_encode($default_colors);
        $inline_script_for_default_colors = "const defaultColors = " . $default_colors_json . ";";
        wp_add_inline_script('block-theme-color-switcher', $inline_script_for_default_colors, 'before');

        wp_enqueue_style(
            'block-theme-color-switcher',
            BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL . 'css/block-theme-color-switcher.css',
            array(),
            BLOCK_THEME_COLOR_SWITCHER_VERSION
        );

    }

    add_action('wp_enqueue_scripts', 'block_theme_color_switcher_enqueue_script');
}

if (!function_exists('block_theme_color_switcher_off_canvass_menu')) {

    function block_theme_color_switcher_off_canvass_menu(): void
    {
        $button_text = get_option('block_theme_color_switcher_button_text', __('Show Colors', 'block-theme-color-switcher'));
        $position = get_option('block_theme_color_switcher_position', 'right');
        $top_position = get_option('block_theme_color_switcher_top', '50');
        ?>
        <!-- Off-Canvas Menu -->
        <div id="colorSwitcherMenu" class="off-canvas-menu" style="<?php echo esc_attr($position) ?>: 0;">
            <div style="margin-bottom: 75px;margin-top: 75px;">
                <h6 style="color:black;padding-left: 15px;">
                    <?php echo esc_html_x('Color Palettes', 'color_palette_text', 'block-theme-color-switcher') ?>
                    <a style="font-size: 10px; cursor: pointer;color:darkgray;"
                       onclick="removeSelectedPaletteData()">
                        (<?php echo esc_html_x('Reset', 'reset_text', 'block-theme-color-switcher') ?>)
                    </a>
                </h6>
                <div class="palette-container">
                </div>
            </div>
        </div>

        <!-- Off-Canvas Menu Button -->
        <div id="colorPaletteSelector"
             style="position: fixed; z-index: 9999; <?php echo esc_attr($position); ?>: 0; top: <?php echo esc_attr($top_position); ?>%;">
            <div class="wp-block-button">
                <a class="off-canvas-button"
                   onclick="toggleColorSwitcherMenu()">
                    <span class="off-canvas-button-icon"
                          style="display: inline-block; width: 20px; height: 20px; text-align: center; line-height: 20px;">
                        ⛶
                    </span>
                    <span style="display: none;"
                          id="switcher-button-text"><?php echo esc_html($button_text) ?></span>
                </a>
            </div>
        </div>

        <?php
    }

    add_action('wp_footer', 'block_theme_color_switcher_off_canvass_menu');
}


if (!function_exists('block_theme_color_switcher_get_theme_color_palette')) {

    function block_theme_color_switcher_get_theme_color_palette(): array
    {

        $theme_json_path = get_template_directory() . '/theme.json';

        if (file_exists($theme_json_path)) {

            global $wp_filesystem;
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();

            $theme_json = $wp_filesystem->get_contents($theme_json_path);
            $theme_data = json_decode($theme_json, true);

            $palette = $theme_data['settings']['color']['palette'] ?? [];

            $css_variables = [];
            foreach ($palette as $color) {
                $css_var_name = '--wp--preset--color--' . $color['slug'];
                $css_variables[$css_var_name] = $color['color'];
            }

            return $css_variables;
        }

        return [];
    }

}

if (!function_exists('block_theme_color_switcher_get_additional_color_palettes')) {

    function block_theme_color_switcher_get_additional_color_palettes(): array
    {

        $styles_dir = get_template_directory() . '/styles';
        $palettes = [];

        global $wp_filesystem;
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();

        if (is_dir($styles_dir)) {
            $json_files = glob($styles_dir . '/*.json');

            foreach ($json_files as $file) {

                $content = $wp_filesystem->get_contents($file);
                $data = json_decode($content, true);
                $palette_title = $data['title'];

                if (isset($data['settings']['color']['palette'])) {

                    foreach ($data['settings']['color']['palette'] as $color) {
                        $css_var_name = '--wp--preset--color--' . $color['slug'];
                        $palettes[$palette_title][$css_var_name] = $color['color'];
                    }

                }
            }
        }

        return $palettes;
    }

}

if (!function_exists('block_theme_color_switcher_merge_color_palettes')) {

    function block_theme_color_switcher_merge_color_palettes(): array
    {

        $theme_palette = block_theme_color_switcher_get_theme_color_palette();
        $additional_palettes = block_theme_color_switcher_get_additional_color_palettes();

        return array_merge(["Default" => $theme_palette,], $additional_palettes);

    }

}
if (!function_exists('block_theme_color_switcher_add_settings_page')) {
    function block_theme_color_switcher_add_settings_page(): void
    {
        add_options_page(
            __('Block Theme Color Switcher Settings', 'block-theme-color-switcher'),
            __('Color Switcher', 'block-theme-color-switcher'),
            'manage_options',
            'block-theme-color-switcher',
            'block_theme_color_switcher_render_settings_page'
        );
    }

    add_action('admin_menu', 'block_theme_color_switcher_add_settings_page');
}

if (!function_exists('block_theme_color_switcher_register_settings')) {

    function block_theme_color_switcher_register_settings(): void
    {
        register_setting('block_theme_color_switcher_options', 'block_theme_color_switcher_position', 'sanitize_text_field');
        register_setting('block_theme_color_switcher_options', 'block_theme_color_switcher_top', 'intval');
        register_setting('block_theme_color_switcher_options', 'block_theme_color_switcher_button_text', 'sanitize_text_field');
    }

    add_action('admin_init', 'block_theme_color_switcher_register_settings');
}
if (!function_exists('block_theme_color_switcher_render_settings_page')) {
    function block_theme_color_switcher_render_settings_page(): void
    {
        $button_text = get_option('block_theme_color_switcher_button_text', __('Show Colors', 'block-theme-color-switcher'));
        $position = get_option('block_theme_color_switcher_position', 'right');
        $top_position = get_option('block_theme_color_switcher_top', '80');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Block Theme Color Switcher Settings', '') ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('block_theme_color_switcher_options'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Selector Button Text') ?></th>
                        <td>
                            <label>
                                <input type="text" name="block_theme_color_switcher_button_text"
                                       value="<?php echo esc_html($button_text); ?>"/>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Palette Selector Position') ?></th>
                        <td>
                            <label>
                                <select name="block_theme_color_switcher_position">
                                    <option value="left" <?php selected($position, 'left'); ?>><?php echo esc_html__('Left', 'block-theme-color-switcher') ?></option>
                                    <option value="right" <?php selected($position, 'right'); ?>><?php echo esc_html__('Right', 'block-theme-color-switcher') ?></option>
                                </select>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Palette Selector Height From Top (%)') ?></th>
                        <td>
                            <label>
                                <input type="number" name="block_theme_color_switcher_top"
                                       value="<?php echo esc_attr($top_position); ?>"/>
                            </label>
                            <p class="description"><?php echo esc_html__('CSS top value (e.g. 88%, 100px). Selector\'s vertical position on the screen.', 'block-theme-color-switcher') ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}