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

        return $this->responseReroute('XenForo_ControllerPublic_Member', 'usergroup-tagged');
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

            foreach ($userGroups as $userGroupId => $userGroup)
            {
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