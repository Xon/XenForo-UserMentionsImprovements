<?php

class SV_UserGroupTagging_XenForo_ControllerPublic_Member extends XFCP_SV_UserGroupTagging_XenForo_ControllerPublic_Member
{
    public function actionIndex()
    {
        $ug = $this->_input->filterSingle('ug', XenForo_Input::STRING);

        if (empty($ug))
        {
            return parent::actionIndex();
        }

        return $this->responseReroute('XenForo_ControllerPublic_Member', 'ViewUsergroup');
    }

    public function actionViewUsergroup()
    {
        $userGroupId = $this->_input->filterSingle('ug', XenForo_Input::STRING);

        $userTaggingModel = $this->_getUserTaggingModel();
        $userGroup = $userTaggingModel->getTaggableGroup($userGroupId);

        if (empty($userGroup))
        {
            return $this->responseNoPermission();
        }

        $userIds = $userTaggingModel->getTaggedGroupUserIds($userGroup['user_group_id']);

        if (!empty($userIds))
        {
            $userModel = $this->getModelFromCache('XenForo_Model_User');
            $users = $userModel->getUsersByIds($userIds);
        }
        else
        {
            $users = array();
        }

        $viewParams = array(
            'users' => $users,
            'userGroup' => $userGroup,
        );

        return $this->responseView('SV_UserGroupTagging_ViewPublic_Member_UserGroup', 'sv_members_usergroup', $viewParams);
    }

    public function actionGroupFind()
    {
        $response = null;
        $users = array();
        $groups = array();
        $q = utf8_strtolower($this->_input->filterSingle('q', XenForo_Input::STRING));
        if ($q !== '' && utf8_strlen($q) >= 2)
        {
            $response = $this->actionFind();
        }
        if ($response instanceof XenForo_ControllerResponse_View)
        {
            $users = $response->params['users'];
            $userGroups = $this->_getUserTaggingModel()->getTaggableGroups($q, 10);
            $options = XenForo_Application::getOptions();
            foreach ($userGroups as $userGroupId => $userGroup)
            {
                if (empty($userGroup['avatar_s']))
                {
                   $userGroup['avatar_s'] = $options->sv_default_group_avatar_s;
                }
                if (empty($userGroup['avatar_l']))
                {
                   $userGroup['avatar_l'] = $options->sv_default_group_avatar_l;
                }
                $groups[] = $userGroup;
            }
        }
        $viewParams = array
        (
            'users' => array_merge($groups, $users)
        );
        return $this->responseView('SV_UserGroupTagging_ViewPublic_Member_Find', 'group_autocomplete', $viewParams);
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}