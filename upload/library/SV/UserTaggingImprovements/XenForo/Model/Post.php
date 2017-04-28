<?php

class SV_UserTaggingImprovements_XenForo_Model_Post extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Post
{
    protected $resetEmailed = true;

    public function alertQuotedMembers(array $post, array $thread, array $forum)
    {
        SV_UserTaggingImprovements_Globals::$emailedUsers = array();
        $this->resetEmailed = false;

        $quotedUserIds = parent::alertQuotedMembers($post, $thread, $forum);

        $options = XenForo_Application::getOptions();
        $threadId = intval($post['thread_id']);
        if ($threadId && $quotedUserIds && $options->sv_send_email_on_quote && $this->canPostCanTag($post, $thread, $forum))
        {
            if ($options->sv_limit_quote_emails)
            {
                $db = $this->_getDb();
                $ids = array();
                foreach($quotedUserIds as $id)
                {
                    if ($id = intval($id))
                    {
                        $ids[] = "select $id as id";
                    }
                }
                $idsToAlert = $db->fetchCol("
                    select a.id
                    from ( ".join(' union ', $ids)." ) a
                    left join xf_thread_user_post on (xf_thread_user_post.thread_id = {$threadId} and xf_thread_user_post.user_id = a.id)
                    where xf_thread_user_post.user_id is null
                ");
            }
            else
            {
                $idsToAlert = $quotedUserIds;
            }

            if ($idsToAlert)
            {
                $userTaggingModel = $this->_getUserTaggingModel();
                $userTaggingModel->emailAlertedUsers('post', $post['post_id'], $post, $idsToAlert, $post, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserQuotedEmailTemplate);
            }
        }

        return $quotedUserIds;
    }

    public function alertTaggedMembers(array $post, array $thread, array $forum, array $tagged, array $alreadyAlerted)
    {
        if ($this->resetEmailed)
        {
            SV_UserTaggingImprovements_Globals::$emailedUsers = array();
        }
        $this->resetEmailed = true;

        if (!$this->canPostCanTag($post, $thread, $forum))
        {
            return array();
        }

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        $alertedUsers = parent::alertTaggedMembers($post, $thread, $forum, $tagged, $alreadyAlerted);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('post', $post['post_id'], $post, $alertedUsers, $post, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserTaggedEmailTemplate);
        return $alertedUsers;
    }

    protected $viewerPermCache = array();
    protected function canPostCanTag(array $post, array $thread, array $forum)
    {
        if (isset($viewerPermCache[$post['user_id']]))
        {
            return $viewerPermCache[$post['user_id']];
        }

        $visitor = XenForo_Visitor::getInstance();
        if ($post['user_id'] == $visitor['user_id'])
        {
            $permissions = $visitor->getNodePermissions($forum['node_id']);
        }
        else
        {
            $permissionCacheModel = XenForo_Model::create('XenForo_Model_PermissionCache');
            $userModel = $this->_getUserModel();
            if ($post['user_id'] == 0)
            {
                $PermissionCombinationId = XenForo_Model_User::$guestPermissionCombinationId;
            }
            else
            {
                $user = $userModel->getUserById($post['user_id']);
                $PermissionCombinationId = $user ['permission_combination_id'];
            }
            $permissions = $permissionCacheModel->getContentPermissionsForItem($PermissionCombinationId, 'node', $forum['node_id']);
        }

        $viewerPermCache[$post['user_id']] = XenForo_Permission::hasContentPermission($permissions, 'sv_EnableTagging');

        return $viewerPermCache[$post['user_id']];
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
