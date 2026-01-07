=== Block Theme Color Switcher ===
Contributors: arkenon
Tags: gutenberg, block themes, colors, color palette, theme colors
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.6
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Front-end color palette switcher for Block Themes. Let users pick styles instantly. Perfect for theme demos & developer showcases.

== Description ==

🎨 **Unleash Your Theme's True Colors!**

WordPress Block Themes (Full Site Editing) are powerful, often coming with multiple style variations defined in `theme.json`. However, usually, you select *one* style in the editor, and that's what visitors see.

**Block Theme Color Palette Switcher** breaks this limitation. It parses your theme's `theme.json` and style variations (inside the `/styles` folder) to automatically generate a floating font-end menu.

[Test with WordPress Playground](https://playground.wordpress.net/?plugin=block-theme-color-switcher)

### 🚀 Who is this for?

*   **👨‍💻 For Theme Developers:** Stop creating multiple demo sites for different color schemes! Install this plugin, and let your potential customers preview **every single color palette** your theme offers on a single demo site.
*   **👤 For Site Visitors:** Give your users control. Let them choose a look that suits their mood or accessibility needs (e.g., High Contrast vs. Pastel).

### ✨ Key Features

*   **🔌 Plug & Play:** Automatically detects palettes from `theme.json` and `styles/*.json` files. No manual configuration needed.
*   **💾 Persistent Selection:** Uses Browser Storage (LocalStorage) to remember the user's choice. If they leave and come back, their selected color palette remains active!
*   **⚡ Instant Preview:** Changes CSS variables on the fly. No page reloads required.
*   **🛠️ Customizable UI:**
    *   Change the trigger button text.
    *   Position the button (Left/Right).
    *   Adjust vertical spacing to avoid conflict with other chat widgets or buttons.
*   **🧹 Clean Reset:** Includes a "Reset" button to revert to the theme's default colors instantly.

### 🔧 How it Works

1.  The plugin scans your active Block Theme.
2.  It extracts color definitions from `settings.color.palette`.
3.  It creates a sleek off-canvas menu on the frontend.
4.  When a user clicks a color, the plugin updates the CSS Custom Properties (Variables) on the `<body>` tag instantly.


== Changelog ==

= 1.0.6 =
Tested: For WordPress 6.9 compatibility.

= 1.0.5 =
Updated: Plugin description in readme

= 1.0.4 =
Updated: Plugin description in readme

= 1.0.3 =
Tested up to: WordPress 6.8.2

= 1.0.2 =
Improved: Color palette styling.
Added: Options page to manage the plugin settings.

= 1.0.1 =
Tested with WordPress 6.5

= 1.0.0 =
Release