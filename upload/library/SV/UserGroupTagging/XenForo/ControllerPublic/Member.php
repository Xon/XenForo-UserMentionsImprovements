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

    public function actionFind()
    {
        $response = parent::actionFind();

        if ($response instanceof XenForo_ControllerResponse_View)
        {
            $users = &$response->params['users'];


        }
        return $response;
    }
}