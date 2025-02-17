<?php
/**
 * Tree view.
 *
 * @category   Tree
 * @package    Pandora FMS
 * @subpackage Community
 * @version    1.0.0
 * @license    See below
 *
 *    ______                 ___                    _______ _______ ________
 * |   __ \.-----.--.--.--|  |.-----.----.-----. |    ___|   |   |     __|
 * |    __/|  _  |     |  _  ||  _  |   _|  _  | |    ___|       |__     |
 * |___|   |___._|__|__|_____||_____|__| |___._| |___|   |__|_|__|_______|
 *
 * ============================================================================
 * Copyright (c) 2005-2023 Pandora FMS
 * Please see https://pandorafms.com/community/ for full contribution list
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation for version 2.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * ============================================================================
 */

global $config;

require_once $config['homedir'].'/include/class/Tree.class.php';

/**
 * Tree group edition.
 */
class TreeGroupEdition extends TreeGroup
{


    /**
     * Construct.
     *
     * @param string  $type           Type.
     * @param string  $rootType       Root.
     * @param integer $id             Id.
     * @param integer $rootID         Root Id.
     * @param boolean $serverID       Server.
     * @param string  $childrenMethod Method children.
     * @param string  $access         Access ACL.
     */
    public function __construct(
        $type,
        $rootType='',
        $id=-1,
        $rootID=-1,
        $serverID=false,
        $childrenMethod='on_demand',
        $access='AR'
    ) {
        global $config;

        parent::__construct(
            $type,
            $rootType,
            $id,
            $rootID,
            $serverID,
            $childrenMethod,
            $access
        );
    }


    /**
     * Get data.
     *
     * @return void
     */
    protected function getData()
    {
        if ($this->id == -1) {
            $this->getFirstLevel();
        }
    }


    /**
     * Get process group.
     *
     * @return mixed
     */
    protected function getProcessedGroups()
    {
        $processed_groups = [];
        // Index and process the groups.
        $groups = $this->getGroupCounters();

        // If user have not permissions in parent, set parent node to 0 (all)
        // Avoid to do foreach for admins.
        if (users_can_manage_group_all('AR') === false) {
            foreach ($groups as $id => $group) {
                if (isset($this->userGroups[$groups[$id]['parent']]) === false) {
                    $groups[$id]['parent'] = 0;
                }
            }
        }

        // Build the group hierarchy.
        if (isset($this->filter['show_full_hirearchy']) === false
            || (isset($this->filter['show_full_hirearchy']) === true && (bool) $this->filter['show_full_hirearchy'] === true)
        ) {
            foreach ($groups as $id => $group) {
                if (isset($groups[$id]['parent']) === true
                    && ($groups[$id]['parent'] != 0)
                ) {
                    $parent = $groups[$id]['parent'];
                    // Parent exists.
                    if (isset($groups[$parent]['children']) === false) {
                        $groups[$parent]['children'] = [];
                    }

                    // Store a reference to the group into the parent.
                    $groups[$parent]['children'][] = &$groups[$id];
                    // This group was introduced into a parent.
                    $groups[$id]['have_parent'] = true;
                }
            }
        }

        // Sort the children groups.
        foreach ($groups as $id => $group) {
            if (isset($groups[$id]['children']) === true) {
                usort($groups[$id]['children'], ['Tree', 'cmpSortNames']);
            }
        }

        // Filter groups and eliminates the reference
        // to children groups out of her parent.
        $groups = array_filter(
            $groups,
            function ($group) {
                return !($group['have_parent'] ?? false);
            }
        );

        // Filter groups that user has permission.
        $groups = array_filter(
            $groups,
            function ($group) {
                global $config;
                return check_acl($config['id_user'], $group['id'], 'AR');
            }
        );

        usort($groups, ['Tree', 'cmpSortNames']);
        return $groups;
    }


    /**
     * Get group counters.
     *
     * @return mixed
     */
    protected function getGroupCounters()
    {
        $messages = [
            'confirm' => __('Confirm'),
            'cancel'  => __('Cancel'),
            'messg'   => __('Are you sure?'),
        ];

        $group_acl = '';
        $search_agent = '';
        $status_agent = '';
        $inner_agent = '';

        if ((bool) is_metaconsole() === true) {
            if (users_can_manage_group_all('AR') === false) {
                $user_groups_str = implode(',', $this->userGroupsArray);
                $group_acl = sprintf(
                    ' AND tgrupo.id_grupo IN (%s) ',
                    $user_groups_str
                );
            }

            if (isset($this->filter['searchAgent']) === true && empty($this->filter['searchAgent']) === false
                || isset($this->filter['statusAgent']) === true && strlen($this->filter['statusAgent']) > 0
            ) {
                $inner_agent = 'INNER JOIN tmetaconsole_agent ON tgrupo.id_grupo = tmetaconsole_agent.id_grupo';
            }

            if (isset($this->filter['searchAgent']) === true && empty($this->filter['searchAgent']) === false) {
                $search_agent = ' AND tmetaconsole_agent.alias LIKE "%'.$this->filter['searchAgent'].'%" ';
            }

            if (isset($this->filter['statusAgent']) === true && strlen($this->filter['statusAgent']) > 0) {
                switch ($this->filter['statusAgent']) {
                    case AGENT_STATUS_NORMAL:
                        $status_agent = ' AND (
                            tmetaconsole_agent.critical_count = 0
                            AND tmetaconsole_agent.warning_count = 0
                            AND tmetaconsole_agent.unknown_count = 0 
                            AND tmetaconsole_agent.normal_count > 0)';
                    break;

                    case AGENT_STATUS_WARNING:
                        $status_agent = ' AND (
                            tmetaconsole_agent.critical_count = 0 
                            AND tmetaconsole_agent.warning_count > 0
                            AND tmetaconsole_agent.total_count > 0)';
                    break;

                    case AGENT_STATUS_CRITICAL:
                        $status_agent = ' AND tmetaconsole_agent.critical_count > 0';
                    break;

                    case AGENT_STATUS_UNKNOWN:
                        $status_agent = ' AND (
                            tmetaconsole_agent.critical_count = 0 
                            AND tmetaconsole_agent.warning_count = 0 
                            AND tmetaconsole_agent.unknown_count > 0)';
                    break;

                    case AGENT_STATUS_NOT_NORMAL:
                        $status_agent = ' AND (
                            tmetaconsole_agent.normal_count <> total_count
                            OR tmetaconsole_agent.total_count = notinit_count)';
                    break;

                    case AGENT_STATUS_NOT_INIT:
                        $status_agent = ' AND (
                            tmetaconsole_agent.total_count = 0
                            OR tmetaconsole_agent.total_count = notinit_count)';
                    break;

                    default:
                        // Nothing to do.
                    break;
                }
            }

            $sql = sprintf(
                'SELECT tgrupo.id_grupo AS gid,
                tgrupo.nombre as name,
                tgrupo.parent,
                tgrupo.icon
                FROM tgrupo
                %s
                WHERE 1=1
                %s
                %s
                %s ',
                $inner_agent,
                $search_agent,
                $status_agent,
                $group_acl
            );
        } else {
            if (users_can_manage_group_all('AR') === false) {
                $user_groups_str = implode(',', $this->userGroupsArray);
                $group_acl = sprintf(
                    'AND id_grupo IN (%s)',
                    $user_groups_str
                );
            }

            $sql = sprintf(
                'SELECT id_grupo AS gid,
                nombre as name,
                parent,
                icon
                FROM tgrupo
                WHERE 1=1 
                %s ',
                $group_acl
            );
        }

        $stats = db_get_all_rows_sql($sql);
        $group_stats = [];
        foreach ($stats as $group) {
            $group_stats[$group['gid']]['name']   = $group['name'];
            $group_stats[$group['gid']]['parent'] = $group['parent'];
            $group_stats[$group['gid']]['icon']   = $group['icon'];
            $group_stats[$group['gid']]['id']     = $group['gid'];
            $group_stats[$group['gid']]['type']   = 'group';

            $group_stats[$group['gid']] = $this->getProcessedItem(
                $group_stats[$group['gid']]
            );
            if (is_management_allowed() === true) {
                $group_stats[$group['gid']]['delete']['messages'] = $messages;
                $group_stats[$group['gid']]['edit']   = 1;
            }

            $group_stats[$group['gid']]['alerts'] = '';
        }

        return $group_stats;
    }


}
