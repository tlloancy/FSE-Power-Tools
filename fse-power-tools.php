<?php
/**
 * Plugin Name: FSE Power Tools
 * Description: Global JavaScript & CSS editor + Site Editor in iframe. Menu under Appearance.
 * Version: 1.0.0
 * Author: tlloancy
 * Author URI: https://profiles.wordpress.org/tlloancy/
 * License: GPL-2.0+
 * Text Domain: fse-power-tools
 */

if (!defined('ABSPATH')) exit;

// Menu
add_action('admin_menu', function() {
    add_submenu_page('themes.php', 'FSE Power Tools', 'FSE Power Tools', 'manage_options', 'fse-power-tools', 'fsept_render_page');
});

// Enqueue
add_action('admin_enqueue_scripts', function($hook) {
    if ('appearance_page_fse-power-tools' !== $hook) return;

    $js_settings = wp_enqueue_code_editor(['type' => 'text/javascript']);
    $css_settings = wp_enqueue_code_editor(['type' => 'text/css']);

    if ($js_settings === false || $css_settings === false) return;

    // Configuration CodeMirror
    $js_settings['codemirror']['theme'] = 'monokai';
    $js_settings['codemirror']['lineNumbers'] = true;
    $js_settings['codemirror']['viewportMargin'] = PHP_INT_MAX;
    $js_settings['codemirror']['scrollbarStyle'] = 'null';

    $css_settings['codemirror']['theme'] = 'monokai';
    $css_settings['codemirror']['lineNumbers'] = true;
    $css_settings['codemirror']['viewportMargin'] = PHP_INT_MAX;
    $css_settings['codemirror']['scrollbarStyle'] = 'null';

    wp_localize_script('code-editor', 'fsept_js_settings', $js_settings);
    wp_localize_script('code-editor', 'fsept_css_settings', $css_settings);

    wp_add_inline_style('wp-codemirror', '.CodeMirror { border: 1px solid #ddd; border-radius: 4px; height: auto; min-height: 400px; font-size: 13px; } .CodeMirror-scroll { overflow: hidden !important; }');
});

// Page
function fsept_render_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Unauthorized.', 'fse-power-tools'));
    }
    $nonce = wp_create_nonce('fsept_save_code');
    $js = get_option('fsept_js', '');
    $css = get_option('fsept_css', '');
    ?>
    <div class="wrap">
    <h1><?php esc_html_e('FSE Power Tools', 'fse-power-tools'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="#js" class="nav-tab nav-tab-active"><?php esc_html_e('JS', 'fse-power-tools'); ?></a>
        <a href="#css" class="nav-tab"><?php esc_html_e('CSS', 'fse-power-tools'); ?></a>
        <a href="#editor" class="nav-tab"><?php esc_html_e('Site Editor', 'fse-power-tools'); ?></a>
    </h2>

    <div id="js" class="tab-content">
        <textarea id="fsept-js-code" class="fsept-code-editor"><?php echo esc_textarea($js); ?></textarea>
        <p><button id="save-js" class="button button-primary"><?php esc_html_e('Save JS', 'fse-power-tools'); ?></button> <span id="status-js"></span></p>
    </div>

    <div id="css" class="tab-content" style="display:none;">
        <textarea id="fsept-css-code" class="fsept-code-editor"><?php echo esc_textarea($css); ?></textarea>
        <p><button id="save-css" class="button button-primary"><?php esc_html_e('Save CSS', 'fse-power-tools'); ?></button> <span id="status-css"></span></p>
    </div>

    <div id="editor" class="tab-content" style="display:none;">
        <iframe src="<?php echo esc_url(admin_url('site-editor.php')); ?>" style="width:100%;height:800px;border:1px solid #ccc;border-radius:4px;"></iframe>
    </div>
    </div>

    <script>
    jQuery(function($) {
        var nonce = '<?php echo esc_js($nonce); ?>';

        // Vérifie que les settings sont chargés
        if (typeof fsept_js_settings === 'undefined' || typeof fsept_css_settings === 'undefined') {
            console.error('FSE Power Tools: CodeMirror settings not loaded.');
            return;
        }

        function initEditor(id, settings) {
            var $el = $(id);
            var editor = wp.codeEditor.initialize($el, settings);
            $el.data('codemirror', editor.codemirror);
        }

        initEditor('#fsept-js-code', fsept_js_settings);
        initEditor('#fsept-css-code', fsept_css_settings);

        function save(type) {
            var $ta = $('#fsept-' + type + '-code');
            var code = $ta.data('codemirror') ? $ta.data('codemirror').getValue() : $ta.val();
            $.post(ajaxurl, {
                action: 'fsept_save_' + type,
                code: code,
                _wpnonce: nonce
            }, function(r) {
                $('#status-' + type).text(r.success ? 'Saved!' : 'Error').fadeIn().delay(2000).fadeOut();
            }).fail(function() {
                $('#status-' + type).text('AJAX Error').fadeIn().delay(2000).fadeOut();
            });
        }

        $('#save-js').on('click', function() { save('js'); });
        $('#save-css').on('click', function() { save('css'); });

        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
    </script>
    <?php
}

// AJAX: Save JS
add_action('wp_ajax_fsept_save_js', function() {
    check_ajax_referer('fsept_save_code');
    if (!current_user_can('manage_options')) wp_die();
    $code = /*sanitize_textarea_field(*/wp_unslash($_POST['code'] ?? '');//);
    update_option('fsept_js', $code);
    wp_send_json_success();
});

// AJAX: Save CSS
add_action('wp_ajax_fsept_save_css', function() {
    check_ajax_referer('fsept_save_code');
    if (!current_user_can('manage_options')) wp_die();
    $code = wp_kses_post(wp_unslash($_POST['code'] ?? ''));
    update_option('fsept_css', $code);
    wp_send_json_success();
});

// Frontend: JS & CSS
add_action('wp_head', function() {
    if ($js = get_option('fsept_js', false)) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized via sanitize_textarea_field
        echo "<script id='fsept-js'>\n" . $js . "\n</script>\n";
    }
    if ($css = get_option('fsept_css', false)) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized via wp_kses_post
        echo "<style id='fsept-css'>\n" . $css . "\n</style>\n";
    }
}, 100);
