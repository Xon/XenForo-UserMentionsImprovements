<?php

class SV_UserTaggingImprovements_XenForo_ControllerPublic_Account extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerPublic_Account
{
    public function actionContactDetailsSave()
    {
        SV_UserTaggingImprovements_Globals::$emailOnTag = $this->_input->filterSingle('sv_email_on_tag', XenForo_Input::UINT);
        SV_UserTaggingImprovements_Globals::$emailOnQuote = $this->_input->filterSingle('sv_email_on_quote', XenForo_Input::UINT);
        SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag = XenForo_Visitor::getInstance()->hasPermission('general', 'sv_ReceiveTagAlertEmails');

        return parent::actionContactDetailsSave();
    }

    public function actionContactDetails()
    {
        $response = parent::actionContactDetails();
        if ($response instanceof XenForo_ControllerResponse_View)
        {
            $options = XenForo_Application::getOptions();
            $visitor = XenForo_Visitor::getInstance();

            $response->subView->params['CanEnableEmailOnTag'] = $options->sv_send_email_on_tagging && $visitor->hasPermission('general', 'sv_ReceiveTagAlertEmails');
            $response->subView->params['CanEnableEmailOnQuote'] = $options->sv_send_email_on_quote && $visitor->hasPermission('general', 'sv_ReceiveTagAlertEmails');
        }
        return $response;
    }
}
