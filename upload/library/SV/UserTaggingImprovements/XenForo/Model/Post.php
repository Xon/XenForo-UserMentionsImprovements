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
        else if ($post['user_id'] == 0)
        {
            $permissionCacheModel = XenForo_Model::create('XenForo_Model_PermissionCache');
            $userModel = $this->_getUserModel();
            $permissions = $permissionCacheModel->getContentPermissionsForItem(XenForo_Model_User::$guestPermissionCombinationId, 'node', $forum['node_id']);
        }
        else
        {
            $userModel = $this->_getUserModel();
            $user = $userModel->getUserById($post['user_id'], array(
                'join' => XenForo_Model_User::FETCH_USER_PERMISSIONS,
            ));

            $permissions = (!empty($user['global_permission_cache'])
                            ? XenForo_Permission::unserializePermissions($user['global_permission_cache'])
                            : array());
        }

        if (XenForo_Permission::hasContentPermission($permissions, 'sv_DisableTagging'))
        {
            return array();
        }

        $userTaggingModel = $this->_getUserTaggingModel();
        $tagged = $userTaggingModel->expandTaggedGroups($tagged, $post);
        $alertedUsers = parent::alertTaggedMembers($post, $thread, $forum, $tagged, $alreadyAlerted);
        $userTaggingModel->emailAlertedUsers('post', $post['post_id'], $post, $alertedUsers, $post);
        return $alertedUsers;
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
