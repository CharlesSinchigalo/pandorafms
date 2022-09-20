<?php
/**
 * License form.
 *
 * @category   Form
 * @package    Pandora FMS
 * @subpackage Enterprise
 * @version    1.0.0
 * @license    See below
 *
 *    ______                 ___                    _______ _______ ________
 *   |   __ \.-----.--.--.--|  |.-----.----.-----. |    ___|   |   |     __|
 *  |    __/|  _  |     |  _  ||  _  |   _|  _  | |    ___|       |__     |
 * |___|   |___._|__|__|_____||_____|__| |___._| |___|   |__|_|__|_______|
 *
 * ============================================================================
 * Copyright (c) 2005-2021 Artica Soluciones Tecnologicas
 * Please see http://pandorafms.org for full contribution list
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation for version 2.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * ============================================================================
 */

// File begin.
global $config;

check_login();

if (! check_acl($config['id_user'], 0, 'PM')) {
    db_pandora_audit(
        AUDIT_LOG_ACL_VIOLATION,
        'Trying to change License settings'
    );
    include 'general/noaccess.php';
    return;
}

$update_settings = (bool) get_parameter_post('update_settings');

if (is_metaconsole()) {
    // Metaconsole.
    ui_require_javascript_file_enterprise('load_enterprise', true);
    enterprise_include_once('include/functions_license.php');
} else {
    ui_print_page_header(
        __('License management'),
        'images/extensions.png',
        false,
        '',
        true
    );

    ui_require_javascript_file_enterprise('load_enterprise');
    enterprise_include_once('include/functions_license.php');
}

enterprise_include_once('include/functions_crypto.php');

if ($renew_license_result !== null) {
    echo $renew_license_result;
}

if ($update_settings) {
    if (!is_metaconsole()) {
        // Node.
        foreach ($_POST['keys'] as $key => $value) {
            db_process_sql_update(
                'tupdate_settings',
                [db_escape_key_identifier('value') => $value],
                [db_escape_key_identifier('key') => $key]
            );
        }

        $customer_key = $_POST['keys']['customer_key'];

        $license_encryption_key = get_parameter('license_encryption_key', false);
        if ($license_encryption_key !== false) {
            $check = db_get_value_sql('SELECT `key` FROM tupdate_settings WHERE `key` LIKE "license_encryption_key"');
            if ($check === false) {
                db_process_sql_insert(
                    'tupdate_settings',
                    [
                        db_escape_key_identifier('value') => $license_encryption_key,
                        db_escape_key_identifier('key')   => 'license_encryption_key',
                    ]
                );
            } else {
                db_process_sql_update(
                    'tupdate_settings',
                    [db_escape_key_identifier('value') => $license_encryption_key],
                    [db_escape_key_identifier('key') => 'license_encryption_key']
                );
            }

            $customer_key = openssl_blowfish_encrypt_hex($customer_key, io_safe_output($license_encryption_key));
        }

        // Update the license file.
        $result = file_put_contents($config['remote_config'].'/'.LICENSE_FILE, $customer_key);
        if ($result === false) {
            ui_print_error_message(__('Failed to Update license file'));
        }

        ui_print_success_message(__('License updated'));
    }
}

$license = enterprise_hook('license_get_info');

$rows = db_get_all_rows_in_table('tupdate_settings');

$settings = new StdClass;
foreach ($rows as $row) {
    $settings->{$row['key']} = $row['value'];
}

?>
<script type="text/javascript">
var texts = {
    error_connecting: '<?php echo __('Error while connecting to licence server.'); ?>',
    error_license: '<?php echo __('Invalid response while validating license.'); ?>',
    error_unknown: '<?php echo __('Unknown error'); ?>',
}

<?php
if (enterprise_installed()) {
    print_js_var_enteprise();
}
?>

</script>
<?php
echo '<form method="post" id="form-license">';
// Retrieve UM url configured (or default).
$url = get_um_url();

$table = new stdClass();
$table->width = '100%';
$table->class = 'databox filters';

if (is_metaconsole()) {
    $table->head[0] = __('Licence');
    $table->head_colspan[0] = 3;
    $table->headstyle[0] = 'text-align: center';
    $table->style[0] = 'font-weight: bold;';
}

$table->data = [];

$table->data[0][0] = '<strong>'.__('Customer key').'</strong>';
$table->data[0][1] = html_print_textarea('keys[customer_key]', 10, 255, $settings->customer_key, 'style="width: 450px" class="height_50px w450px"', true);

$table->data[1][0] = '<strong>'.__($license['expiry_caption']).'</strong>';
$table->data[1][1] = html_print_input_text('expires', $license['expiry_date'], '', 10, 255, true, true);

$table->data[2][0] = '<strong>'.__('Platform Limit').'</strong>';
$table->data[2][1] = html_print_input_text('expires', $license['limit'], '', 10, 255, true, true).' '.($license['limit_mode'] == 0 ? __('agents') : __('modules'));

$table->data[3][0] = '<strong>'.__('Current Platform Count').'</strong>';
$table->data[3][1] = html_print_input_text('expires', $license['count'], '', 10, 255, true, true).' '.($license['limit_mode'] == 0 ? __('agents') : __('modules'));

$table->data[4][0] = '<strong>'.__('Current Platform Count (enabled: items)').'</strong>';
$table->data[4][1] = html_print_input_text('expires', $license['count_enabled'], '', 10, 255, true, true).' '.($license['limit_mode'] == 0 ? __('agents') : __('modules'));

$table->data[5][0] = '<strong>'.__('Current Platform Count (disabled: items)').'</strong>';
$table->data[5][1] = html_print_input_text('expires', $license['count_disabled'], '', 10, 255, true, true).' '.($license['limit_mode'] == 0 ? __('agents') : __('modules'));

$table->data[6][0] = '<strong>'.__('License Mode').'</strong>';
$table->data[6][1] = html_print_input_text('expires', $license['license_mode'], '', 10, 255, true, true);

$table->data[7][0] = '<strong>'.__('NMS').'</strong>';
$table->data[7][1] = html_print_input_text('expires', ($license['nms'] == 1 ? __('enabled') : __('disabled')), '', 10, 255, true, true);

$table->data[8][0] = '<strong>'.__('Satellite').'</strong>';
$table->data[8][1] = html_print_input_text('expires', ($license['dhpm'] == 1 ? __('enabled') : __('disabled')), '', 10, 255, true, true);

if ($license['dhpm'] == 1) {
    $table->data[9][0] = '<strong>'.__('License encryption key').'</strong>'.ui_print_help_tip(
        __('This key is used to encrypt your Pandora FMS license when it is shared with other Pandora FMS components'),
        true
    );
    $table->data[9][1] = html_print_input_password(
        'license_encryption_key',
        io_safe_output($settings->license_encryption_key),
        '',
        10,
        255,
        true,
        false
    );
}

$table->data[10][0] = '<strong>'.__('Licensed to').'</strong>';
$table->data[10][1] = html_print_input_text('licensed_to', $license['licensed_to'], '', 64, 255, true, true);

html_print_table($table);

// If DESTDIR is defined the enterprise license is expired.
if (enterprise_installed() || defined('DESTDIR')) {
    echo '<div class="action-buttons" style="width: '.$table->width.'">';
    html_print_input_hidden('update_settings', 1);
    html_print_submit_button(__('Validate'), 'update_button', false, 'class="sub upd"');
    echo '&nbsp;&nbsp;';
    html_print_button(__('Request new license'), '', false, 'generate_request_code()', 'class="sub next"');
    echo '</div>';
}

if (is_metaconsole()) {
    ui_require_css_file('pandora_enterprise', ENTERPRISE_DIR.'/include/styles/');
    ui_require_css_file('register', 'include/styles/');
} else {
    ui_require_css_file('pandora');
    ui_require_css_file('pandora_enterprise', ENTERPRISE_DIR.'/include/styles/');
    ui_require_css_file('register');
}

if (enterprise_hook('print_activate_licence_dialog') == ENTERPRISE_NOT_HOOK) {
    echo '</form>';
    echo '<div id="code_license_dialog" class="invisible left" title="'.__('Request new license').'">';
    echo '<div id="logo">';
    html_print_image(ui_get_custom_header_logo(true));
    echo '</div>';
    echo ''.__('To get your <b>%s Enterprise License</b>:', get_product_name()).'<br />';
    echo '<ul>';
    echo '<li>';
    echo ''.sprintf(__('Go to %s'), '<a target="_blank" href="'.$url.'/index.php?section=generate_key_client">'.$url.'index.php?section=generate_key_client</a>');
    echo '</li>';
    echo '<li>';
    echo ''.__('Enter the <b>auth key</b> and the following <b>request key</b>:');
    echo '</li>';
    echo '</ul>';
    echo '<div id="code"></div>';
    echo '<ul>';
    echo '<li>';
    echo ''.__('Enter your name (or a company name) and a contact email address.');
    echo '</li>';
    echo '<li>';
    echo ''.__('Click on <b>Generate</b>.');
    echo '</li>';
    echo '<li>';
    echo ''.__('Click <a href="javascript: close_code_license_dialog();">here</a>, enter the generated license key and click on <b>Validate</b>.');
    echo '</li>';
    echo '</ul>';
    echo '</div>';
}
