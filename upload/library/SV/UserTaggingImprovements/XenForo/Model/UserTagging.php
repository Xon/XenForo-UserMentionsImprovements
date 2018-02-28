<?php

class SV_UserTaggingImprovements_XenForo_Model_UserTagging extends XFCP_SV_UserTaggingImprovements_XenForo_Model_UserTagging
{
    const UserTaggedEmailTemplate = 'sv_user_tagged';
    const UserQuotedEmailTemplate = 'sv_user_quoted';

    const UserTaggedCheckField = 'sv_email_on_tag';
    const UserQuotedCheckField = 'sv_email_on_quote';

    /**
     * @param string $contentType
     * @param int    $contentId
     * @param array  $content
     * @param int[]  $userIds
     * @param array  $taggingUser
     * @param string $template
     * @param string $userCheckField
     */
    public function emailAlertedUsers($contentType, $contentId, $content, array $userIds, array $taggingUser, $template = 'sv_user_tagged', $userCheckField = 'sv_email_on_tag')
    {
        if (empty($userIds))
        {
            return;
        }

        $options = XenForo_Application::getOptions();
        /** @noinspection PhpUndefinedFieldInspection */
        if (!$options->sv_send_email_on_tagging)
        {
            return;
        }

        // use the alert handler to provide a content link.
        // This add-on extends the relevant alert handlers to inject the required method
        /** @var XenForo_Model_Alert|SV_UserTaggingImprovements_XenForo_Model_Alert $alertModel */
        $alertModel = $this->getModelFromCache('XenForo_Model_Alert');
        $alertHandler = $alertModel->getAlertHandler($contentType);
        if (empty($alertHandler) || !method_exists($alertHandler, 'getContentUrl'))
        {
            return;
        }

        /** @noinspection PhpUndefinedFieldInspection */
        $snippetLength = intval($options->sv_mention_snippet_length);
        $canGetSnippet = (!$snippetLength) && method_exists($alertHandler, 'getContentMessage');

        /** @noinspection PhpUndefinedMethodInspection */
        $viewLink = $alertHandler->getContentUrl($content, true);
        if (empty($viewLink))
        {
            return;
        }

        $snippetHtml = null;
        $snippetText = null;
        if ($canGetSnippet)
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $snippet = $alertHandler->getContentMessage($content);

            if ($snippet)
            {
                $snippet = trim($snippet);
                if ($snippetLength)
                {
                    if ($snippetLength > 0)
                    {
                        $snippet = XenForo_Helper_String::wholeWordTrim($snippet, $snippetLength);
                    }
                    if ($snippet)
                    {
                        $template .= '_message';

                        $bbCodeParserText = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Text'));
                        $content['sv_snippet_text'] = $bbCodeParserText->render($snippet);

                        $bbCodeParserHtml = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('HtmlEmail'));
                        $content['sv_snippet_html'] = $bbCodeParserText->render($bbCodeParserHtml);
                    }
                }
            }
        }

        // don't alert accounts which are too old, or bounced
        $lastActivity = 0;
        /** @noinspection PhpUndefinedFieldInspection */
        $activeLimitOption = $options->watchAlertActiveOnly;
        if (!empty($activeLimitOption['enabled']) && !empty($activeLimitOption['days']))
        {
            $lastActivity = XenForo_Application::$time - 86400 * $activeLimitOption['days'];
        }

        $userModel = $this->_getUserModel();
        $users = $userModel->getUsersByIds($userIds, [
            'join' => XenForo_Model_User::FETCH_USER_OPTION |
                      XenForo_Model_User::FETCH_USER_PERMISSIONS,
        ]);
        foreach ($users as $user)
        {
            if (isset(SV_UserTaggingImprovements_Globals::$emailedUsers[$user['user_id']]))
            {
                continue;
            }
            SV_UserTaggingImprovements_Globals::$emailedUsers[$user['user_id']] = true;

            if (empty($user[$userCheckField]))
            {
                continue;
            }
            if ($user['is_banned'] || $user['user_state'] != 'valid')
            {
                continue;
            }
            if ($lastActivity && isset($user['last_activity']) && $user['last_activity'] < $lastActivity)
            {
                continue;
            }

            $permissions = (!empty($user['global_permission_cache'])
                ? XenForo_Permission::unserializePermissions($user['global_permission_cache'])
                : []);
            if (!XenForo_Permission::hasPermission($permissions, 'general', 'sv_ReceiveTagAlertEmails'))
            {
                continue;
            }

            $this->emailAlertedUser($viewLink, $contentType, $contentId, $content, $user, $taggingUser, $template);
        }
    }

    /**
     * @param string      $viewLink
     * @param string      $contentType
     * @param int         $contentId
     * @param array       $content
     * @param array       $user
     * @param array       $taggingUser
     * @param string      $template
     */
    protected function emailAlertedUser(/** @noinspection PhpUnusedParameterInspection */
        $viewLink, $contentType, $contentId, $content, array $user, array $taggingUser, $template)
    {
        $mail = XenForo_Mail::create($template, [
            'sender'      => $taggingUser,
            'receiver'    => $user,
            'contentType' => $contentType,
            'contentId'   => $contentId,
            'viewLink'    => $viewLink,
            'content'     => $content,
        ], $user['language_id']);

        $mail->enableAllLanguagePreCache();
        $mail->queue($user['email'], $user['username']);
    }

    protected $_plainReplacementsUserGroupMentions = null;

    public function getTaggedUsersInMessage($message, &$newMessage, $replaceStyle = 'bb')
    {
        $filteredMessage = $message;
        $this->_plainReplacementsUserGroupMentions = null;
        $this->_plainReplacements = null;
        if ($replaceStyle == 'bb')
        {
            $this->_plainReplacements = [];
            $filteredMessage = preg_replace_callback(
                '#\[(usergroup)(=[^\]]*)?](.*)\[/\\1]#siU',
                [$this, '_plainReplaceHandlerGroup'],
                $filteredMessage
            );
        }
        else if ($replaceStyle == 'text')
        {
            $this->_plainReplacements = [];
            $filteredMessage = preg_replace_callback(
                '#(?<=^|\s|[\](,]|--|@)@\[ug_(\d+):(\'|"|&quot;|)(.*)\\2\]#iU',
                [$this, '_plainReplaceHandlerGroup'],
                $filteredMessage
            );
        }
        // _plainReplacements gets set to null in getTaggedUsersInMessage
        if (!empty($this->_plainReplacements))
        {
            $this->_plainReplacementsUserGroupMentions = $this->_plainReplacements;
            $this->_plainReplacements = null;
        }

        $matches = parent::getTaggedUsersInMessage($filteredMessage, $newMessage, $replaceStyle);
        // restore the message if there are no matches
        if (empty($matches))
        {
            $newMessage = $message;
        }

        // expand matches early to support tapatalk
        $matches = $this->expandTaggedGroups($matches);

        return $matches;
    }

    protected function _plainReplaceHandlerGroup(array $match)
    {
        if (!is_array($this->_plainReplacements))
        {
            $this->_plainReplacements = [];
        }

        $placeholder = "\x1Ag\x1A" . count($this->_plainReplacements) . "\x1Ag\x1A";

        $this->_plainReplacements[$placeholder] = $match[0];

        return $placeholder;
    }

    protected function _getPossibleTagMatches($message)
    {
        if (!empty($this->_plainReplacementsUserGroupMentions))
        {
            if (empty($this->_plainReplacements))
            {
                $this->_plainReplacements = $this->_plainReplacementsUserGroupMentions;
            }
            else
            {
                $this->_plainReplacements = array_merge($this->_plainReplacements, $this->_plainReplacementsUserGroupMentions);
            }
        }

        return parent::_getPossibleTagMatches($message);
    }

    protected function _getGroupMembership($user)
    {
        $groups = explode(',', $user['secondary_group_ids']);
        $groups[] = $user['user_group_id'];
        $groupKeys = [];
        foreach ($groups as $group)
        {
            $groupKeys[$group] = true;
        }

        return $groupKeys;
    }

    protected function _getTagMatchUsers(array $matches)
    {
        $usersByMatch = parent::_getTagMatchUsers($matches);

        $db = $this->_getDb();
        $matchKeys = array_keys($matches);
        $whereParts = [];
        $matchParts = [];

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
            SELECT usergroup.user_group_id, usergroup.title, usergroup.sv_private,
                " . implode(', ', $matchParts) . "
            FROM xf_user_group AS usergroup
            WHERE usergroup.sv_taggable = 1 and (" . implode(' OR ', $whereParts) . ")
            ORDER BY LENGTH(usergroup.title) DESC
        ");

        $require_sort = [];

        $visitor = XenForo_Visitor::getInstance();
        $viewAllGroups = $visitor->hasPermission('general', 'sv_ViewPrivateGroups');
        $groupMembership = $this->_getGroupMembership($visitor->toArray());

        while ($group = $userResults->fetch())
        {
            // private groups are only view able by members and administrators.
            if (!$viewAllGroups && $group['sv_private'] && empty($groupMembership[$group['user_group_id']]))
            {
                continue;
            }

            $userInfo = [
                'user_id'  => 'ug_' . $group['user_group_id'],
                'is_group' => 1,
                'username' => $group['title'],
                'lower'    => utf8_strtolower($group['title'])
            ];

            foreach ($matchKeys AS $key)
            {
                if (!empty($group["match_$key"]))
                {
                    if (!isset($require_sort[$key]))
                    {
                        $require_sort[$key] = !empty($usersByMatch[$key]);
                    }
                    $usersByMatch[$key][$userInfo['user_id']] = $userInfo;
                }
            }
        }
        // sort in the groups
        foreach ($require_sort AS $key => $x)
        {
            if ($require_sort[$key])
            {
                uasort($usersByMatch[$key], [$this, 'usergroup_sorting']);
            }
        }

        return $usersByMatch;
    }

    public function usergroup_sorting($a, $b)
    {
        if (!empty($b['is_group']) && empty($a['is_group']))
        {
            return 1;
        }
        else if (empty($b['is_group']) && !empty($a['is_group']))
        {
            return -1;
        }

        return (utf8_strlen($b['lower']) - utf8_strlen($a['lower']));
    }

    protected function _replaceTagUserMatch(array $user, $replaceStyle)
    {
        if (!empty($user['is_group']) && $replaceStyle == 'bb')
        {
            $group_id = intval(str_replace('ug_', '', $user['user_id']));
            /** @noinspection PhpUndefinedFieldInspection */
            $prefix = XenForo_Application::getOptions()->userTagKeepAt ? '@' : '';

            return '[USERGROUP=' . $group_id . ']' . $prefix . $user['username'] . '[/USERGROUP]';
        }

        return parent::_replaceTagUserMatch($user, $replaceStyle);
    }

    public function getTaggableGroup($UserGroupId)
    {
        $db = $this->_getDb();
        $sql = '';

        $visitor = XenForo_Visitor::getInstance();
        $viewAllGroups = $visitor->hasPermission('general', 'sv_ViewPrivateGroups');

        if (!$viewAllGroups)
        {
            $groupMembership = array_keys($this->_getGroupMembership($visitor->toArray()));
            $sql .= ' and ( usergroup.sv_private = 0 or usergroup.user_group_id in ( ' . $db->quote($groupMembership) . ' ) )';
        }

        /** @noinspection SpellCheckingInspection */
        $userGroup = $db->fetchRow("
            SELECT usergroup.user_group_id, usergroup.title as username, usergroup.sv_avatar_s as avatar_s, usergroup.sv_avatar_l as avatar_l, usergroup.sv_private as private, usergroup.last_edit_date
            FROM xf_user_group AS usergroup
            WHERE usergroup.sv_taggable = 1 and usergroup.user_group_id = ? {$sql}
        ", $UserGroupId);

        if (!empty($userGroup))
        {
            $options = XenForo_Application::getOptions();
            if (empty($userGroup['avatar_s']))
            {
                /** @noinspection PhpUndefinedFieldInspection */
                $userGroup['avatar_s'] = $options->sv_default_group_avatar_s;
            }
            if (empty($userGroup['avatar_l']))
            {
                /** @noinspection PhpUndefinedFieldInspection */
                $userGroup['avatar_l'] = $options->sv_default_group_avatar_l;
            }
            if (isset($userGroup['last_edit_date']))
            {
                // cache buster strings
                if ($userGroup['avatar_s'])
                {
                    $userGroup['avatar_s'] .= "?q=" . $userGroup['last_edit_date'];
                }
                if ($userGroup['avatar_l'])
                {
                    $userGroup['avatar_l'] .= "?q=" . $userGroup['last_edit_date'];
                }
            }
        }

        return $userGroup;
    }

    public function getTaggableGroups($q = null, $limit = 0)
    {
        $db = $this->_getDb();
        $sql = '';
        if (!empty($q))
        {
            $sql = ' and usergroup.title LIKE ' . XenForo_Db::quoteLike($q, 'r', $db);
        }

        $visitor = XenForo_Visitor::getInstance();
        $viewAllGroups = $visitor->hasPermission('general', 'sv_ViewPrivateGroups');

        if (!$viewAllGroups)
        {
            $groupMembership = array_keys($this->_getGroupMembership($visitor->toArray()));
            $sql .= ' and ( usergroup.sv_private = 0 or usergroup.user_group_id in ( ' . $db->quote($groupMembership) . ' ) )';
        }

        return $this->fetchAllKeyed("
            SELECT usergroup.user_group_id, usergroup.title as username, usergroup.sv_avatar_s as avatar_s, usergroup.sv_avatar_l as avatar_l, usergroup.sv_private as private
            FROM xf_user_group AS usergroup
            WHERE usergroup.sv_taggable = 1 {$sql}
            ORDER BY LENGTH(usergroup.title) DESC
            " . ($limit ? " limit $limit " : '') . "
        ", 'user_group_id');
    }

    public function getTaggedGroupUserIds($UserGroupId)
    {
        $db = $this->_getDb();

        return $db->fetchCol("
            SELECT DISTINCT user.user_id
            FROM xf_user AS user
            JOIN xf_user_group_relation AS relation ON relation.user_id = user.user_id
            WHERE relation.user_group_id = ?
        ", $UserGroupId);
    }

    public function expandTaggedGroups(array $tagged, array $taggingUser = null)
    {
        if ($taggingUser == null)
        {
            $taggingUser = XenForo_Visitor::getInstance()->toArray();
        }
        $permissions = [];
        if (!empty($taggingUser['permissions']))
        {
            $permissions = $taggingUser['permissions'];
        }
        else if (!empty($taggingUser['global_permission_cache']))
        {
            $permissions = XenForo_Permission::unserializePermissions($taggingUser['global_permission_cache']);
        }
        $visitor = XenForo_Visitor::getInstance()->toArray();
        if (empty($permissions) && $visitor['user_id'] == $taggingUser['user_id'])
        {
            $permissions = $visitor['permissions'];
        }
        if (empty($permissions))
        {
            $permUser = $this->_getDb()->fetchRow('
                SELECT permission_combination.cache_value AS global_permission_cache
                FROM xf_user user
                LEFT JOIN xf_permission_combination AS permission_combination ON
                            (permission_combination.permission_combination_id = user.permission_combination_id)
                WHERE user.user_id = ?
            ', $taggingUser['user_id']);

            if (!empty($permUser['global_permission_cache']))
            {
                $permissions = XenForo_Permission::unserializePermissions($permUser['global_permission_cache']);
            }
        }

        $CannotGroupTag = !XenForo_Permission::hasPermission($permissions, 'general', 'sv_TagUserGroup');

        $alreadyTagged = [];
        $db = $this->_getDb();
        $users = [];
        foreach ($tagged as $candinate)
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

            if ($CannotGroupTag)
            {
                continue;
            }

            $group_id = intval(str_replace('ug_', '', $candinate['user_id']));
            if (empty($group_id))
            {
                continue;
            }

            $userResults = $db->query("
                SELECT DISTINCT user.user_id, user.username
                FROM xf_user AS user
                JOIN xf_user_group_relation AS relation ON relation.user_id = user.user_id
                WHERE relation.user_group_id = ?
            ", $group_id);

            while ($user = $userResults->fetch())
            {
                if (!empty($alreadyTagged[$user['user_id']]))
                {
                    continue;
                }
                $alreadyTagged[$user['user_id']] = true;

                $users[$user['user_id']] = [
                    'user_id'       => $user['user_id'],
                    'username'      => $user['username'],
                    'lower'         => utf8_strtolower($user['username']),
                    'taggedGroupId' => $group_id,
                    'taggedGroup'   => $candinate['username'],
                ];
            }
        }

        return $users;
    }

    /**
     * @return XenForo_Model|XenForo_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('XenForo_Model_User');
    }
}
