<?php

class SV_UserGroupTagging_XenForo_ControllerAdmin_UserGroup extends XFCP_SV_UserGroupTagging_XenForo_ControllerAdmin_UserGroup
{
    public function actionSave()
    {
        SV_UserGroupTagging_Globals::$UserGroupAdminController = $this;

        return parent::actionSave();
    }
}
