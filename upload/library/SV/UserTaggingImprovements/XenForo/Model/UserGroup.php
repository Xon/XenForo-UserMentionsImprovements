<?php

class SV_UserGroupTagging_XenForo_Model_UserGroup extends XFCP_SV_UserGroupTagging_XenForo_Model_UserGroup
{
    public function updateUserGroupAndPermissions($userGroupId, array $userGroupInfo, array $permissions)
    {
        if (!empty(SV_UserGroupTagging_Globals::$UserGroupAdminController))
        {
            $input = SV_UserGroupTagging_Globals::$UserGroupAdminController->getInput();
            $userGroupInfo['sv_private']  = $input->filterSingle('sv_private', XenForo_Input::UINT);
            $userGroupInfo['sv_taggable'] = $input->filterSingle('sv_taggable', XenForo_Input::UINT);
            $userGroupInfo['sv_avatar_s'] = $input->filterSingle('sv_avatar_s', XenForo_Input::STRING);
            $userGroupInfo['sv_avatar_l'] = $input->filterSingle('sv_avatar_l', XenForo_Input::STRING);
        }
        return parent::updateUserGroupAndPermissions($userGroupId, $userGroupInfo, $permissions);
    }
}