<?php

class SV_UserGroupTagging_XenForo_DataWriter_UserGroup extends XFCP_SV_UserGroupTagging_XenForo_DataWriter_UserGroup
{
    protected function _getFields()
    {
        $fields = parent::_getFields();
        $fields['xf_user_group']['sv_tagable'] = array('type' => self::TYPE_UINT,   'default' => 0);
        $fields['xf_user_group']['sv_avatar']  = array('type' => self::TYPE_STRING, 'default' => '');
        return $fields;
    }
}