<?php

class SV_UserTaggingImprovements_XenForo_DataWriter_User extends XFCP_SV_UserTaggingImprovements_XenForo_DataWriter_User
{
    protected function _getFields()
    {
        $fields = parent::_getFields();
        $fields['xf_user_option']['sv_email_on_tag'] = array('type' => self::TYPE_BOOLEAN, 'default' => 0);
        $fields['xf_user_option']['sv_email_on_quote'] = array('type' => self::TYPE_BOOLEAN, 'default' => 0);
        return $fields;
    }

    protected function _preSave()
    {
        if (isset(SV_UserTaggingImprovements_Globals::$emailOnTag))
        {
            if (SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag || !SV_UserTaggingImprovements_Globals::$emailOnTag)
            {
                $this->set('sv_email_on_tag', SV_UserTaggingImprovements_Globals::$emailOnTag);
            }
        }
        if (isset(SV_UserTaggingImprovements_Globals::$emailOnQuote))
        {
            if (SV_UserTaggingImprovements_Globals::$CanEnableEmailOnTag || !SV_UserTaggingImprovements_Globals::$emailOnQuote)
            {
                $this->set('sv_email_on_quote', SV_UserTaggingImprovements_Globals::$emailOnQuote);
            }
        }

        parent::_preSave();
    }
}
