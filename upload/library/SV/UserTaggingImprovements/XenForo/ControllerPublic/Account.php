<?php

class SV_UserTaggingImprovements_XenForo_ControllerPublic_Account extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerPublic_Account
{
    public function actionContactDetailsSave()
    {
        SV_UserTaggingImprovements_Globals::$PublicAccountController = $this;
        SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag = XenForo_Visitor::getInstance()->hasPermission('general', 'sv_ReceiveTagAlertEmails');

        return parent::actionContactDetailsSave();
    }

    public function actionContactDetails()
    {
        $response = parent::actionContactDetails();
        if ($response instanceof XenForo_ControllerResponse_View)
        {
            $response->subView->params['CanEnableEmailOnTag'] = XenForo_Visitor::getInstance()->hasPermission('general', 'sv_ReceiveTagAlertEmails');
        }
        return $response;
    }
}
