<?php

class SV_UserTaggingImprovements_XenForo_ControllerAdmin_User extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerAdmin_User
{
    public function actionSave()
    {
        SV_UserTaggingImprovements_Globals::$PublicAccountController = $this;
        SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag = true;

        return parent::actionSave();
    }
}
