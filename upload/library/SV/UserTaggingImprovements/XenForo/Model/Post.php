<?php

class SV_UserTaggingImprovements_XenForo_Model_Post extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Post
{
    public function alertTaggedMembers(array $post, array $thread, array $forum, array $tagged, array $alreadyAlerted)
    {
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

        if (!XenForo_Permission::hasContentPermission($permissions, 'sv_EnableTagging'))
        {
            return array();
        }

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        $alertedUsers = parent::alertTaggedMembers($post, $thread, $forum, $tagged, $alreadyAlerted);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('post', $post['post_id'], $post, $alertedUsers, $post);
        return $alertedUsers;
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
