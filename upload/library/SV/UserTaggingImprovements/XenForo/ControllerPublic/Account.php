<?php

class SV_UserTaggingImprovements_XenForo_ControllerPublic_Account extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerPublic_Account
{
    public function actionContactDetailsSave()
    {
        SV_UserTaggingImprovements_Globals::$PublicAccountController = $this;

        return parent::actionContactDetailsSave();
    }
}
