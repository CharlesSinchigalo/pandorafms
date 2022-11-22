<?php
/**
 * Configure agent groups.
 *
 * @category   Agents group management.
 * @package    Pandora FMS
 * @subpackage User interface.
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

// Begin.
global $config;

check_login();

enterprise_hook('open_meta_frame');

if (! check_acl($config['id_user'], 0, 'AW')) {
    db_pandora_audit(
        AUDIT_LOG_ACL_VIOLATION,
        'Trying to access Group Management2'
    );
    include 'general/noaccess.php';
    return;
}

require_once $config['homedir'].'/include/functions_groups.php';
require_once $config['homedir'].'/include/functions_users.php';
enterprise_include_once('meta/include/functions_agents_meta.php');

// Default values.
$icon = '';
$name = '';
$id_parent = 0;
$group_pass = '';
$alerts_disabled = 0;
$custom_id = '';
$propagate = 0;
$skin = 0;
$contact = '';
$other = '';
$description = '';
$max_agents = 0;

$create_group = (bool) get_parameter('create_group');
$id_group = (int) get_parameter('id_group');

if ($id_group) {
    $group = db_get_row('tgrupo', 'id_grupo', $id_group);
    if ($group) {
        $name = $group['nombre'];
        if (empty($group['icon'])) {
            $icon = false;
        } else {
            $icon = $group['icon'].'.png';
        }

        $group_pass = io_safe_output($group['password']);
        $alerts_disabled = $group['disabled'];
        $id_parent = $group['parent'];
        $custom_id = $group['custom_id'];
        $propagate = $group['propagate'];
        $skin = $group['id_skin'];
        $description = $group['description'];
        $contact = $group['contact'];
        $other = $group['other'];
        $max_agents = $group['max_agents'];
    } else {
        ui_print_error_message(__('There was a problem loading group'));
        echo '</table>';
        echo '</div>';
        echo '<div id="both">&nbsp;</div>';
        echo '</div>';
        echo '<div id="foot">';
        // include 'general/footer.php';
        echo '</div>';
        echo '</div>';
        exit;
    }
}

// Header
if (is_metaconsole() === true) {
    agents_meta_print_header();
    $sec = 'advanced';
} else {
    if ($id_group) {
        $title_in_header = __('Update group');
    } else {
        $title_in_header = __('Create group');
    }

    // Header.
    ui_print_standard_header(
        $title_in_header,
        'images/group.png',
        false,
        '',
        false,
        [],
        [
            [
                'link'  => '',
                'label' => __('Profiles'),
            ],
            [
                'link'  => '',
                'label' => __('Manage agents group'),
            ],
        ]
    );
    $sec = 'gagente';
}

$table = new stdClass();
$table->width = '100%';
$table->class = 'databox filters';
if (defined('METACONSOLE')) {
    if ($id_group) {
        $table->head[0] = __('Update Group');
    } else {
        $table->head[0] = __('Create Group');
    }

    $table->head_colspan[0] = 4;
    $table->headstyle[0] = 'text-align: center';
}

$table->style[0] = 'font-weight: bold';

$table->data = [];
$table->data[0][0] = __('Name');
$table->data[0][1] = html_print_input_text('name', $name, '', 35, 100, true);

$table->data[1][0] = __('Icon');
$files = list_files('images/groups_small/', 'png', 1, 0);
foreach ($files as $key => $f) {
    // Remove from the list the non-desired .png files
    if (strpos($f, '.bad.png') !== false || strpos($f, '.default.png') !== false || strpos($f, '.ok.png') !== false || strpos($f, '.warning.png') !== false) {
        unset($files[$key]);
    }
}

$table->data[1][1] = html_print_select($files, 'icon', $icon, '', 'None', '', true);
$table->data[1][1] .= ' <span id="icon_preview">';
if ($icon) {
    $table->data[1][1] .= html_print_image('images/groups_small/'.$icon, true);
}

$table->data[1][1] .= '</span>';

$table->data[2][0] = __('Parent');

$acl_parent = true;
if ($id_group) {
    // The user can access to the parent, but she want to edit the group.
    if (!check_acl($config['id_user'], $id_parent, 'AR')) {
        $acl_parent = false;

        $table->data[2][1] = __('You have not access to the parent.').html_print_input_hidden('id_parent', $id_parent, true);
    } else {
        $table->data[2][1] = '<div class="w250px inline">';
        $table->data[2][1] .= html_print_select_groups(
            false,
            'AR',
            false,
            'id_parent',
            $id_parent,
            '',
            __('None'),
            -1,
            true,
            false,
            true,
            '',
            false,
            false,
            false,
            $id_group
        );
        $table->data[2][1] .= '</div>';
    }
} else {
    $table->data[2][1] = '<div class="w250px inline">';
    $table->data[2][1] .= html_print_input(
        [
            'type'           => 'select_groups',
            'name'           => 'id_parent',
            'selected'       => $id_parent,
            'return'         => true,
            'returnAllGroup' => false,
            'nothing'        => __('None'),
            'nothing_value'  => -1,
        ]
    );
    $table->data[2][1] .= '</div>';
}

if ($acl_parent) {
    $table->data[2][1] .= ' <span id="parent_preview">';
    $table->data[2][1] .= html_print_image('images/groups_small/'.( $id_parent != 0 ? groups_get_icon($id_parent) : 'without_group').'.png', true);
    $table->data[2][1] .= '</span>';
}

$i = 3;
if ($config['enterprise_installed']) {
    $i = 4;
    $table->data[3][0] = __('Group Password');
    $table->data[3][1] = html_print_input_password('group_pass', $group_pass, '', 16, 255, true);
}

$table->data[$i][0] = __('Alerts').ui_print_help_tip(__('Enable alert use in this group.'), true);
$table->data[$i][1] = html_print_checkbox_switch('alerts_enabled', 1, ! $alerts_disabled, true);
$i++;

$table->data[$i][0] = __('Propagate ACL').ui_print_help_tip(__('Propagate the same ACL security into the child subgroups.'), true);
$table->data[$i][1] = html_print_checkbox_switch('propagate', 1, $propagate, true);
$i++;

$table->data[$i][0] = __('Custom ID');
$table->data[$i][1] = html_print_input_text('custom_id', $custom_id, '', 16, 255, true);
$i++;

$table->data[$i][0] = __('Description');
$table->data[$i][1] = html_print_input_text('description', $description, '', 60, 255, true);
$i++;

$table->data[$i][0] = __('Contact').ui_print_help_tip(__('Contact information accessible through the _groupcontact_ macro'), true);
$table->data[$i][1] = html_print_textarea('contact', 4, 40, $contact, "class='min-height-0px'", true);
$i++;

$table->data[$i][0] = __('Other').ui_print_help_tip(__('Information accessible through the _group_other_ macro'), true);
$table->data[$i][1] = html_print_textarea('other', 4, 40, $other, "class='min-height-0px'", true);
$i++;

// $isFunctionSkins = enterprise_include_once('include/functions_skins.php');
// if ($isFunctionSkins !== ENTERPRISE_NOT_HOOK && !defined('METACONSOLE')) {
// $table->data[10][0] = __('Skin');
// $table->data[10][1] = skins_print_select($config['id_user'], 'skin', $skin, '', __('None'), 0, true);
// }
$table->data[$i][0] = __('Max agents allowed').'&nbsp;'.ui_print_help_tip(__('Set the maximum of agents allowed for this group. 0 is unlimited.'), true);
$table->data[$i][1] = html_print_input_text('max_agents', $max_agents, '', 10, 255, true);
$i++;

if (defined('METACONSOLE')) {
    $sec = 'advanced';
} else {
    $sec = 'gagente';
}

echo '<form name="grupo" method="post" action="index.php?sec='.$sec.'&sec2=godmode/groups/group_list&pure='.$config['pure'].'" >';
html_print_table($table);
echo '<div class="action-buttons" style="width: '.$table->width.'">';
    html_print_button(__('Back'), 'button_back', false, '', 'class="sub cancel"');
if ($id_group) {
    html_print_input_hidden('update_group', 1);
    html_print_input_hidden('id_group', $id_group);
    html_print_submit_button(__('Update'), 'updbutton', false, 'class="sub upd"');
} else {
    html_print_input_hidden('create_group', 1);
    html_print_submit_button(__('Create'), 'crtbutton', false, 'class="sub wand"');
}

echo '</div>';
echo '</form>';

enterprise_hook('close_meta_frame');

?>
<script language="javascript" type="text/javascript">
function icon_changed () {
    var inputs = [];
    var data = this.value;
    $('#icon_preview').fadeOut ('normal', function () {
        $('#icon_preview').empty ();
        if (data != "") {
            var params = [];
            params.push("get_image_path=1");
            params.push("img_src=images/groups_small/" + data);
            params.push("page=include/ajax/skins.ajax");
            params.push("only_src=1");
            jQuery.ajax ({
                data: params.join ("&"),
                type: 'POST',
                url: action="<?php echo ui_get_full_url('ajax.php', false, false, false); ?>",
                success: function (result) {
                    $('#icon_preview').append ($('<img />').attr ('src', result));
                }
            });
        }
        $('#icon_preview').fadeIn ();
    });
}

function parent_changed () {
    var inputs = [];
    inputs.push ("get_group_json=1");
    inputs.push ("id_group=" + this.value);
    inputs.push ("page=godmode/groups/group_list");
    jQuery.ajax ({
        data: inputs.join ("&"),
        type: 'GET',
        url: action="<?php echo ui_get_full_url('ajax.php', false, false, false); ?>",
        dataType: 'json',
        success: function (data) {
            var data_ = data;
            $('#parent_preview').fadeOut ('normal', function () {
                $('#parent_preview').empty ();
                if (data_ != null) {
                    if(data['icon'] == '') {
                        data['icon'] = 'without_group';
                    }
                    var params = [];
                    params.push("get_image_path=1");
                    params.push("img_src=images/groups_small/" + data['icon'] + ".png");
                    params.push("page=include/ajax/skins.ajax");
                    params.push("only_src=1");
                    jQuery.ajax ({
                        data: params.join ("&"),
                        type: 'POST',
                        url: action="<?php echo ui_get_full_url('ajax.php', false, false, false); ?>",
                        success: function (result) {
                            $('#parent_preview').append ($('<img />').attr ('src', result));
                        }
                    });
                }
                $('#parent_preview').fadeIn ();
            });
        }
    });
}

$(document).ready (function () {
    $('#icon').change (icon_changed);
    $('#id_parent').change (parent_changed);
    $('#button-button_back').on('click', function(){
        window.location = '<?php echo ui_get_full_url('index.php?sec='.$sec.'&sec2=godmode/groups/group_list'); ?>';
    });
});
</script>
