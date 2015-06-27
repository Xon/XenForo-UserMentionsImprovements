<?php

class SV_UserGroupTagging_XenForo_Model_UserTagging extends XFCP_SV_UserGroupTagging_XenForo_Model_UserTagging
{
    public function emailAlertedUsers(array $users, array $taggingUser)
    {
		if (empty($userIds))
		{
            return;
        }

        $options = XenForo_Application::getOptions();
        if (empty($option->sv_ugt_email))
        {
            return;
        }

        $userModel = $this->_getUserModel();
        $users = $userModel->getUsersByIds($userIds, array(
            'join' => XenForo_Model_User::FETCH_USER_OPTION | 
                      XenForo_Model_User::FETCH_USER_PERMISSIONS,
            'sv_emailOnTag' => true
        ));
        $users = $this->unserializePermissionsInList($users, 'permission_cache');
        foreach($users as $user)
        {
            $this->emailAlertedUser($user, $taggingUser);
        }
    }

    public function emailAlertedUser(array $user, array $taggingUser)
    {
        if (!XenForo_Permission::hasPermission($user, 'general', 'sv_ReceiveTagAlertEmails'))
        {
            return;
        }
    }

    public function getTaggedUsersInMessage($message, &$newMessage, $replaceStyle = 'bb')
    {
        $filteredMessage = $message;
        if (!empty(SV_UserGroupTagging_Globals::$CanGroupTag))
        {
            if ($replaceStyle == 'bb')
            {
                $this->_plainReplacements = array();
                $filteredMessage = preg_replace_callback(
                    '#\[(usergroup)(=[^\]]*)?](.*)\[/\\1]#siU',
                    array($this, '_plainReplaceHandler'),
                    $filteredMessage
                );
            }
        }
        $matches = parent::getTaggedUsersInMessage($filteredMessage, $newMessage, $replaceStyle);
        // restore the message if there are no matches
        if (empty($matches))
        {
            $newMessage = $message;
        }

        return $matches;
    }

    protected function _getTagMatchUsers(array $matches)
    {
        $usersByMatch = parent::_getTagMatchUsers($matches);

        if (empty(SV_UserGroupTagging_Globals::$CanGroupTag))
        {
            return $usersByMatch;
        }

        $db = $this->_getDb();
        $matchKeys = array_keys($matches);
        $whereParts = array();
        $matchParts = array();

        foreach ($matches AS $key => $match)
        {
            if (utf8_strlen($match[1][0]) > 50)
            {
                // longer than max username length
                continue;
            }

            $sql = 'usergroup.title LIKE ' . XenForo_Db::quoteLike($match[1][0], 'r', $db);
            $whereParts[] = $sql;
            $matchParts[] = 'IF(' . $sql . ', 1, 0) AS match_' . $key;
        }

        if (!$whereParts)
        {
            return $usersByMatch;
        }

        $userResults = $db->query("
            SELECT usergroup.user_group_id, usergroup.title,
                " . implode(', ', $matchParts) . "
            FROM xf_user_group AS usergroup
            WHERE usergroup.sv_tagable = 1 and (" . implode(' OR ', $whereParts) . ")
            ORDER BY LENGTH(usergroup.title) DESC
        ");

        $require_sort = array();

        while ($group = $userResults->fetch())
        {
            $userInfo = array(
                'user_id' => 'ug_' . $group['user_group_id'],
                'is_group' => 1,
                'username' => $group['title'],
                'lower' => strtolower($group['title'])
            );

            foreach ($matchKeys AS $key)
            {
                if ($group["match_$key"])
                {
                    $usersByMatch[$key][$group['user_group_id']] = $userInfo;
                    $require_sort[$key] = true;
                }
            }
        }
        // sort in the groups
        foreach ($require_sort AS $key => $x)
        {
            usort($usersByMatch[$key],  array(__CLASS__, 'sv_length_sort'));
        }

        return $usersByMatch;
    }

    protected static function sv_length_sort($a, $b)
    {
        return utf8_strlen($b['lower']) - utf8_strlen($a['lower']);
    }

    protected function _replaceTagUserMatch(array $user, $replaceStyle)
    {
        if (empty($user['is_group']))
        {
            return parent::_replaceTagUserMatch($user, $replaceStyle);
        }
        $group_id = intval(str_replace('ug_', '', $user['user_id']));

        $prefix = XenForo_Application::getOptions()->userTagKeepAt ? '@' : '';

        if ($replaceStyle == 'bb')
        {
            return '[USERGROUP=' . $group_id . ']' . $prefix . $user['username'] . '[/USERGROUP]';
        }
        else if ($replaceStyle == 'text')
        {
            if (strpos($user['username'], ']') !== false)
            {
                if (strpos($user['username'], "'") !== false)
                {
                    $username = '"' . $prefix . $user['username'] . '"';
                }
                else
                {
                    $username = "'" . $prefix . $user['username'] . "'";
                }
            }
            else
            {
                $username = $prefix . $user['username'];
            }
            return '@[' . $username . ']';
        }
        else
        {
            return $prefix . $user['username'];
        }
    }

    public function getTaggableGroups($q = null)
    {
        $db = $this->_getDb();
        $sql = '';
        if (!empty($q))
        {
            $sql = ' and usergroup.title LIKE ' . XenForo_Db::quoteLike($q, 'r', $db);
        }
        return $this->fetchAllKeyed("
            SELECT usergroup.user_group_id, usergroup.title
            FROM xf_user_group AS usergroup
            WHERE usergroup.sv_tagable = 1 ". $sql."
            ORDER BY LENGTH(usergroup.title) DESC
        ", 'user_group_id');
    }

    public function expandTaggedGroups(array $tagged)
    {
        $alreadyTagged = array_keys(XenForo_Application::arrayColumn($tagged, 'user_id'));
        $db = $this->_getDb();
        $users = array();
        foreach($tagged as $candinate)
        {
            if (!empty($alreadyTagged[$candinate['user_id']]))
            {
                continue;
            }
            $alreadyTagged[$candinate['user_id']] = true;

            if (empty($candinate['is_group']))
            {
                $users[$candinate['user_id']] = $candinate;
                continue;
            }

            $group_id = intval(str_replace('ug_', '', $candinate['user_id']));
            if (empty($group_id))
            {
                continue;
            }

            $userResults = $db->query("
                SELECT distinct user.user_id, user.username
                FROM xf_user AS user
                join xf_user_group_relation as relation on relation.user_id = user.user_id
                WHERE relation.user_group_id = ?
            ", $group_id);

            while ($user = $userResults->fetch())
            {
                if (!empty($alreadyTagged[$user['user_id']]))
                {
                    continue;
                }
                $alreadyTagged[$user['user_id']] = true;

                $users[$candinate['user_id']] = array
                (
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'lower' => strtolower($user['username'])
                );
            }
        }
        return $users;
    }
}
