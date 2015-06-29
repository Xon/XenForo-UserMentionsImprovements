<?php

class SV_UserGroupTagging_XenForo_ControllerPublic_Account extends XFCP_SV_UserGroupTagging_XenForo_ControllerPublic_Account
{
    public function actionContactDetailsSave()
    {
        SV_UserGroupTagging_Globals::$PublicAccountController = $this;

        return parent::actionContactDetailsSave();
    }
}
