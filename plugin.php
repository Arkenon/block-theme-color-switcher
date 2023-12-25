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
 * Text Domain: btc-switcher
 */

// Prevent direct access to the file.
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Enqueue JavaScript for color switcher.
 */
function btc_switcher_enqueue_script() {

	$asset = include plugin_dir_path( __FILE__ ) . 'build/chroma.asset.php';

	wp_enqueue_script(
		'btc-chroma',
		plugin_dir_url( __FILE__ ) . 'build/chroma.js',
		isset($asset['dependencies']) ? $asset['dependencies'] : array(), // Bağımlılıklar
		isset($asset['version']) ? $asset['version'] : false,
		true // Load in footer.
	);

	wp_enqueue_script(
		'btc-switcher',
		plugin_dir_url( __FILE__ ) . 'js/btc-switcher.js',
		array('btc-chroma'), // Dependencies (e.g., jQuery).
		'1.0.0',
		true // Load in footer.
	);

	$palettes = btc_switcher_merge_color_palettes();
	$palettes_json = json_encode($palettes);
	$inline_script = "const palettes = ".$palettes_json.";";
	wp_add_inline_script('btc-chroma', $inline_script , 'before');

	$defaultColors = btc_switcher_get_theme_color_palette();
	$palettes_json2 = json_encode($defaultColors);
	$inline_script2 = "const defaultColors = ".$palettes_json2.";";
	wp_add_inline_script('btc-chroma', $inline_script2 , 'before');

	wp_enqueue_style(
		'btc-switcher',
		plugin_dir_url( __FILE__ ) . 'css/btc-switcher.css',
		array(),
		'1.0.0'
	);
}

add_action( 'wp_enqueue_scripts', 'btc_switcher_enqueue_script' );

function btc_switcher_enqueue_footer_script() {
	?>

	<!-- Off-Canvas Menü -->
	<div id="colorSwitcherMenu" class="off-canvas-menu">
        <div style="margin-bottom: 75px;margin-top: 75px;">
            <h6 style="color:black;padding-left: 15px;">Renk Paletleri</h6>
            <div class="palette-container">
            </div>
        </div>
	</div>

	<!-- Renk Paleti Seçici Butonu -->
	<div id="colorPaletteSelector" style="position: fixed; right: 20px; bottom: 20px; cursor: pointer; z-index: 9999;">
		<button class="off-canvas-button" onclick="toggleColorSwitcherMenu()">Select Color Palette</button>
	</div>

	<?php
}

add_action( 'wp_footer', 'btc_switcher_enqueue_footer_script' );


function btc_switcher_get_theme_color_palette() {
	// theme.json dosyasının yolu
	$theme_json_path = get_template_directory() . '/theme.json';

	// Dosyanın varlığını kontrol et
	if (file_exists($theme_json_path)) {
		// theme.json içeriğini oku
		$theme_json = file_get_contents($theme_json_path);
		$theme_data = json_decode($theme_json, true);

		// Renk paletini al
		$palette = $theme_data['settings']['color']['palette'] ?? [];

		// CSS değişkenlerine dönüştür
		$css_variables = [];
		foreach ($palette as $color) {
			$css_var_name = '--wp--preset--color--' . $color['slug'];
			$css_variables[$css_var_name] = $color['color'];
		}

		return $css_variables;
	}

	return [];
}

function btc_switcher_get_additional_color_palettes() {
	$styles_dir = get_template_directory() . '/styles';
	$palettes = [];

	if (is_dir($styles_dir)) {
		$json_files = glob($styles_dir . '/*.json');

		foreach ($json_files as $file) {
			$content = file_get_contents($file);
			$data = json_decode($content, true);

			if (isset($data['settings']['color']['palette'])) {
				$palette_name = basename($file, '.json'); // Dosya adını palet adı olarak kullan
				foreach ($data['settings']['color']['palette'] as $color) {
					$css_var_name = '--wp--preset--color--' . $color['slug'];
					$palettes[$palette_name][$css_var_name] = $color['color'];
				}
			}
		}
	}

	return $palettes;
}

function btc_switcher_merge_color_palettes() {
	$theme_palette = btc_switcher_get_theme_color_palette(); // Tema JSON'dan alınan palet
	$additional_palettes = btc_switcher_get_additional_color_palettes(); // /styles klasöründen alınan paletler

	// Varsayılan paleti ilk palet olarak ekleyelim
	$palettes = ["PALET_1" => $theme_palette];

	// Ek paletleri numaralandırarak ekleyelim
	$palette_number = 2;
	foreach ($additional_palettes as $palette_name => $palette_colors) {
		$palettes["PALET_" . $palette_number] = $palette_colors;
		$palette_number++;
	}

	return $palettes;
}

