<?php

class SV_UserTaggingImprovements_XenForo_ControllerAdmin_UserGroup extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerAdmin_UserGroup
{
    public function actionSave()
    {
        SV_UserTaggingImprovements_Globals::$UserGroupAdminController = $this;

        return parent::actionSave();
    }
}
