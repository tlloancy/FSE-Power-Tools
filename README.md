# FSE-Power-Tools
=== FSE Power Tools ===

- Contributors: tlloancy
- Tags: fse, javascript, css, site-editor, custom-code
- Requires at least: 6.0
- Tested up to: 6.8
- Stable tag: 1.0.0
- Requires PHP: 7.4
- License: GPL-2.0+
- License URI: https://www.gnu.org/licenses/gpl-2.0.html

**Global JS/CSS Editor + Site Editor in iframe. Full security. Works with all themes.**

== Description ==

A powerful tool to add **custom JavaScript and CSS** globally, plus **Site Editor integration** via iframe. Accessible under **Appearance**.

= Features =
- JS/CSS editor with line numbers & auto-resize
- No horizontal scroll or white bar
- Site Editor in iframe
- Injection via `wp_head`
- Full security
- Works with **all themes**

== Installation ==

1. Upload the plugin
2. Activate it
3. Go to **Appearance > FSE Power Tools**

== Changelog ==

= 1.0.0 =
* Initial release

# Komplainte de KRANK X2

# FSE Power Tools

**Global JavaScript & CSS Editor + Site Editor in iframe**  
A powerful tool to add custom JS and CSS globally with a clean interface. Also includes direct access to the Site Editor via iframe.

**Works with all themes** — Clean editor with line numbers, no horizontal scroll, no white bars.

## Why this plugin is not available on WordPress.org

This plugin was **rejected** by the WordPress.org plugin review team.

**Main reason:**  
They consider that allowing administrators to freely add **arbitrary JavaScript and CSS** is **too dangerous**.

Even though the plugin:
- Uses proper nonces and capability checks (`manage_options`)
- Sanitizes CSS with `wp_kses_post()`
- Only accessible to Administrators

WordPress.org has become very strict about plugins that let users inject unrestricted custom code (especially JavaScript). They see it as a potential security risk (XSS, malicious code injection if an admin account is compromised, etc.).

This is why many small/limited JS & CSS editors get approved, but a clean, full-featured, and powerful one like this gets blocked.

**Therefore, the plugin is released freely here on GitHub only.**

## Features

- Full-featured JS and CSS editors powered by **CodeMirror** (Monokai theme, line numbers, auto-resize)
- Clean, comfortable interface (no horizontal scroll, no annoying white bars)
- Integrated **Site Editor** tab (opens in an iframe for better workflow)
- Code injected via `wp_head()` (priority 100)
- Works with **all themes** (Block themes and Classic themes)
- Secure admin interface

## Installation

1. Download the latest release or clone this repository
2. Upload the `fse-power-tools` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the WordPress Plugins screen
4. Go to **Appearance → FSE Power Tools**

## Usage

- **JS tab**: Your JavaScript code will be injected in a `<script id="fsept-js">` tag
- **CSS tab**: Your CSS code will be injected in a `<style id="fsept-css">` tag
- **Site Editor tab**: Quick access to the Full Site Editor inside the plugin page

**Warning**: Only users with the **Administrator** role can access this page.

## Security Notice

This plugin intentionally allows you to add raw JavaScript and CSS.  
While the admin side is properly secured, **be careful** who you give admin access to.

If an administrator account is compromised, this plugin could be used to inject malicious scripts.

Use it responsibly — it is a power tool.

## Changelog

### 1.0.0
- Initial release

---

**Made for developers and power users** who want a proper, clean custom code editor in WordPress.

Feel free to fork and improve it!
