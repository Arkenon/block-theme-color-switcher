<?php
/**
 * Plugin Name: Block Theme Color Switcher
 * Plugin URI:  https://blockthemecolorswitcher.iyziweb.site
 * Description: This plugin allows users to switch color themes for the Block Theme.
 * Version:     1.0.0
 * Author:      Kadim Gültekin
 * Author URI:  https://kadimgultekin.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: block-theme-color-switcher
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

// Define constants
$plugin_data = get_file_data( __FILE__, array( 'version'     => 'Version' ) );
define( 'BLOCK_THEME_COLOR_SWITCHER_VERSION', $plugin_data['version'] );
define( 'BLOCK_THEME_COLOR_SWITCHER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Enqueue JavaScript for color switcher.
 */
function block_theme_color_switcher_enqueue_script() {

	$asset = include BLOCK_THEME_COLOR_SWITCHER_PLUGIN_PATH . 'build/block-theme-color-switcher.asset.php';

	wp_enqueue_script(
		'block-theme-color-switcher',
		BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL . 'build/block-theme-color-switcher.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? BLOCK_THEME_COLOR_SWITCHER_VERSION,
		true // Load in footer.
	);

	$palettes = block_theme_color_switcher_merge_color_palettes();
	$palettes_json = json_encode($palettes);
	$inline_script = "const palettes = ".$palettes_json.";";
	wp_add_inline_script('block-theme-color-switcher', $inline_script , 'before');

	$defaultColors = block_theme_color_switcher_get_theme_color_palette();
	$palettes_json2 = json_encode($defaultColors);
	$inline_script2 = "const defaultColors = ".$palettes_json2.";";
	wp_add_inline_script('block-theme-color-switcher', $inline_script2 , 'before');

	wp_enqueue_style(
		'block-theme-color-switcher',
		BLOCK_THEME_COLOR_SWITCHER_PLUGIN_URL . 'css/block-theme-color-switcher.css',
		array(),
		BLOCK_THEME_COLOR_SWITCHER_VERSION
	);

}

add_action( 'wp_enqueue_scripts', 'block_theme_color_switcher_enqueue_script' );

function block_theme_color_switcher_enqueue_footer_script() {
	?>

	<!-- Off-Canvas Menü -->
	<div id="colorSwitcherMenu" class="off-canvas-menu">
        <div style="margin-bottom: 75px;margin-top: 75px;">
            <h6 style="color:black;padding-left: 15px;">Color Palettes <a style="font-size: 10px; cursor: pointer" onclick="removeSelectedPaletteData()">(Reset)</a></h6>
            <div class="palette-container">
            </div>
        </div>
	</div>

	<!-- Renk Paleti Seçici Butonu -->
	<div id="colorPaletteSelector" style="position: fixed; right: 20px; bottom: 20px; cursor: pointer; z-index: 9999;">
        <div class="wp-block-button">
            <a class="wp-block-button__link wp-element-button off-canvas-button" onclick="toggleColorSwitcherMenu()" style="padding:15px;">⛶ Theme Colors</a>
        </div>
	</div>

	<?php
}

add_action( 'wp_footer', 'block_theme_color_switcher_enqueue_footer_script' );


function block_theme_color_switcher_get_theme_color_palette() {

	$theme_json_path = get_template_directory() . '/theme.json';

	if (file_exists($theme_json_path)) {

		$theme_json = file_get_contents($theme_json_path);
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

function block_theme_color_switcher_get_additional_color_palettes() {

	$styles_dir = get_template_directory() . '/styles';
	$palettes = [];

	if (is_dir($styles_dir)) {
		$json_files = glob($styles_dir . '/*.json');

		foreach ($json_files as $file) {

			$content = file_get_contents($file);
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

function block_theme_color_switcher_merge_color_palettes() {

	$theme_palette = block_theme_color_switcher_get_theme_color_palette();
	$additional_palettes = block_theme_color_switcher_get_additional_color_palettes();

	return array_merge(["Default" => $theme_palette,],$additional_palettes);

}

