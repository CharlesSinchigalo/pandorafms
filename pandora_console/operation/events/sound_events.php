<?php
/**
 * Events sounds.
 *
 * @category   Sounds
 * @package    Pandora FMS
 * @subpackage Community
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

require_once '../../include/config.php';
require_once '../../include/functions.php';
require_once '../../include/functions_db.php';
require_once '../../include/auth/mysql.php';

global $config;

// Check user.
check_login();
$config['id_user'] = $_SESSION['id_usuario'];

$event_a = check_acl($config['id_user'], 0, 'ER');
$event_w = check_acl($config['id_user'], 0, 'EW');
$event_m = check_acl($config['id_user'], 0, 'EM');
$access = ($event_a == true) ? 'ER' : (($event_w == true) ? 'EW' : (($event_m == true) ? 'EM' : 'ER'));

if (check_acl($config['id_user'], 0, 'ER') === false
    && check_acl($config['id_user'], 0, 'EW') === false
    && check_acl($config['id_user'], 0, 'EM') === false
) {
    db_pandora_audit(
        AUDIT_LOG_ACL_VIOLATION,
        'Trying to access event viewer'
    );
    include 'general/noaccess.php';

    return;
}

$agents = agents_get_group_agents(0, false, 'none', false, true);
ob_start('ui_process_page_head');
ob_start();
echo '<html>';
echo '<head>';

echo '<title>'.__('Sound Events').'</title>';
?>
<style type='text/css'>
    * {
        margin: 0;
        padding: 0;
    }

    img {
        border: 0;
    }
</style>
<?php
echo '<link rel="icon" href="../../'.ui_get_favicon().'" type="image/ico" />';
if ($config['style'] === 'pandora_black' && !is_metaconsole()) {
    echo '<link rel="stylesheet" href="../../include/styles/pandora_black.css" type="text/css" />';
} else {
    echo '<link rel="stylesheet" href="../../include/styles/pandora.css" type="text/css" />';
}

echo '</head>';
echo "<body class='sound_events'>";
echo "<h1 class='modalheaderh1'>".__('Sound console').'</h1>';

// Connection lost alert.
ui_require_css_file('register', 'include/styles/', true);
$conn_title = __('Connection with server has been lost');
$conn_text = __('Connection to the server has been lost. Please check your internet connection or contact with administrator.');
ui_require_javascript_file('connection_check');
set_js_value('absolute_homeurl', ui_get_full_url(false, false, false, false));
ui_print_message_dialog(
    $conn_title,
    $conn_text,
    'connection',
    '/images/error_1.png'
);

$table = new StdClass;
$table->width = '100%';
$table->class = 'w16px sound_div_background ';
$table->size[0] = '10%';
$table->rowclass[0] = 'bold_top';
$table->rowclass[1] = 'bold_top';
$table->rowclass[2] = 'bold_top';

$table->data[0][0] = __('Group');
$table->data[0][1] = html_print_select_groups(
    false,
    $access,
    true,
    'group',
    '',
    'changeGroup();',
    '',
    0,
    true,
    false,
    true,
    '',
    false,
    'max-width:200px;'
).'<br /><br />';

$table->data[0][2] = __('Type');
$table->data[0][3] = html_print_checkbox(
    'alert_fired',
    'alert_fired',
    true,
    true,
    false,
    'changeType();'
);
$table->data[0][3] .= __('Alert fired').'<br />';
$table->data[0][3] .= html_print_checkbox(
    'critical',
    'critical',
    true,
    true,
    false,
    'changeType();'
);
$table->data[0][3] .= __('Monitor critical').'<br />';
$table->data[0][3] .= html_print_checkbox(
    'unknown',
    'unknown',
    true,
    true,
    false,
    'changeType();'
);
$table->data[0][3] .= __('Monitor unknown').'<br />';
$table->data[0][3] .= html_print_checkbox(
    'warning',
    'warning',
    true,
    true,
    false,
    'changeType();'
);
$table->data[0][3] .= __('Monitor warning').'<br />';

$table->data[1][0] = __('Agent');
$table->data[1][1] = html_print_select(
    $agents,
    'id_agents[]',
    true,
    false,
    '',
    '',
    true,
    true,
    '',
    '',
    '',
    'max-width:200px; height:100px',
    '',
    false,
    '',
    '',
    true
);

$table->data[1][2] = __('Event');
$table->data[1][3] = html_print_textarea(
    'events_fired',
    200,
    20,
    '',
    'readonly="readonly" style="max-height:100px; resize:none;"',
    true
);

html_print_table($table);

$table = new StdClass;
$table->width = '100%';
$table->class = 'w16px sound_div_background text_center';

$table->data[0][0] = '<a href="javascript: toggleButton();">';
$table->data[0][0] .= html_print_image(
    'images/play.button.png',
    true,
    ['id' => 'button']
);
$table->data[0][0] .= '</a>';

$table->data[0][1] = '<a href="javascript: ok();">';
$table->data[0][1] .= html_print_image(
    'images/ok.button.png',
    true,
    ['style' => 'margin-left: 15px;']
);
$table->data[0][1] .= '</a>';

$table->data[0][2] = '<a href="javascript: test_sound_button();">';
$table->data[0][2] .= html_print_image(
    'images/icono_test.png',
    true,
    [
        'id'    => 'button_try',
        'style' => 'margin-left: 15px;',
    ]
);
$table->data[0][2] .= '</a>';

$table->data[0][3] = html_print_image(
    'images/tick_sound_events.png',
    true,
    [
        'id'    => 'button_status',
        'style' => 'margin-left: 15px;',
    ]
);

html_print_table($table);
?>

<script type="text/javascript">
var group = 0;
var alert_fired = true;
var critical = true;
var warning = true;
var unknown = true;

var running = false;

var id_row = 0;

var button_play_status = "play";

var test_sound = false;

function test_sound_button() {
    if (!test_sound) {
        $("#button_try").attr('src', '../../images/icono_test.png');
        $('body').append("<audio src='../../include/sounds/Star_Trek_emergency_simulation.wav' autoplay='true' hidden='true' loop='false'>");
        test_sound = true;
    }
    else {
        $("#button_try").attr('src', '../../images/icono_test.png');
        $('body audio').remove();
        test_sound = false;
    }
}

function changeGroup() {
    group = $("#group").val();

    jQuery.post ("../../ajax.php",
        {"page" : "include/ajax/agent",
            "get_agents_group": 1,
            "id_group": group
        },
        function (data) {
            $("#id_agents").empty();
            jQuery.each (data, function (id, value) {
                if (value != "") {
                    $("#id_agents")
                        .append(
                            '<option value="' + id + '">' + value + '</option>'
                        );
                }
            });
        },
        "json"
    );
}

function changeType() {
    alert_fired = false;
    critical = false;
    warning = false;
    unknown = false;

    if($("input[name=alert_fired]").is(':checked') ) {
        alert_fired = true;
    }

    if($("input[name=critical]").is(':checked') ) {
        critical = true;
    }

    if($("input[name=warning]").is(':checked') ) {
        warning = true;
    }

    if($("input[name=unknown]").is(':checked') ) {
        unknown = true;
    }
}

function toggleButton() {
    if (button_play_status == 'pause') {
        $("#button").attr('src', '../../images/play.button.png');
        stopSound();

        button_play_status = 'play';
    }
    else {
        $("#button").attr('src', '../../images/pause.button.png');
        forgetPreviousEvents();
        startSound();

        button_play_status = 'pause';
    }
}

function ok() {
    $('#button_status').attr('src','../../images/tick_sound_events.png');
    $('audio').remove();
    $('#textarea_events_fired').val("");
}

function stopSound() {
    $('audio').remove();
    $('body').css('background', '#494949');
    running = false;
}

function startSound() {
    running = true;
}

function forgetPreviousEvents() {
    var agents = $("#id_agents").val();

    jQuery.post ("../../ajax.php",
        {"page" : "include/ajax/events",
            "get_events_fired": 1,
            "id_group": group,
            "alert_fired": alert_fired,
            "critical": critical,
            "warning": warning,
            "unknown": unknown,
            "id_row": id_row,
            "agents[]" : agents
        },
        function (data) {
            firedId = parseInt(data['fired']);
            if (firedId != 0) {
                id_row = firedId;
            }
            running = true;
        },
        "json"
    );
}

function check_event() {
    var agents = $("#id_agents").val();
    if (running) {
        jQuery.post ("../../ajax.php",
            {"page" : "include/ajax/events",
                "get_events_fired": 1,
                "id_group": group,
                "alert_fired": alert_fired,
                "critical": critical,
                "warning": warning,
                "unknown": unknown,
                "id_row": id_row,
                "agents[]" : agents,
            },
            function (data) {
                firedId = parseInt(data['fired']);
                if (firedId != 0) {
                    id_row = firedId;
                    var actual_text = $('#textarea_events_fired').val();
                    if (actual_text == "") {
                        $('#textarea_events_fired').val(data['message'] + "\n");
                    } else {
                        $('#textarea_events_fired')
                            .val(actual_text + "\n" + data['message'] + "\n");
                    }
                    $('#button_status')
                        .attr(
                            'src','../../images/sound_events_console_alert.gif'
                        );
                    $('audio').remove();
                    if(data['sound'] == '') {
                        data['sound'] = 'include/sounds/Star_Trek_emergency_simulation.wav';
                    }

                    $('body')
                        .append("<audio src='../../" + data['sound'] + "' autoplay='true' hidden='true' loop='true'>");
                }
            },
            "json"
        );
    }
}

$(document).ready (function () {
    //10 seconds between ajax request
    setInterval("check_event()", (10 * 1000));
});

</script>

<?php
echo '</body>';

while (ob_get_length() > 0) {
    ob_end_flush();
}

echo '</html>';

