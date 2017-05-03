<?php

class SV_UserTaggingImprovements_XenForo_ControllerAdmin_User extends XFCP_SV_UserTaggingImprovements_XenForo_ControllerAdmin_User
{
    public function actionSave()
    {
        SV_UserTaggingImprovements_Globals::$emailOnTag = $this->_input->filterSingle('sv_email_on_tag', XenForo_Input::UINT);
        SV_UserTaggingImprovements_Globals::$emailOnQuote = $this->_input->filterSingle('sv_email_on_quote', XenForo_Input::UINT);
        SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag = true;

        return parent::actionSave();
    }
}
