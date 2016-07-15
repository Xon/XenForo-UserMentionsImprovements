<?php

class SV_UserTaggingImprovements_XenForo_DataWriter_Alert extends XFCP_SV_UserTaggingImprovements_XenForo_DataWriter_Alert
{
    protected function _preSave()
    {
        if ($this->isInsert())
        {
            $alertedUserId = $this->get('alerted_user_id');
            if (!empty(SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertedUserId]['taggedGroupId']))
            {
                $extraData = $this->get('extra_data');
                if (empty($extraData))
                {
                    $extraData = array();
                }
                $extraData['taggedGroupId'] = SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertedUserId]['taggedGroupId'];
                $extraData['taggedGroup'] = SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertedUserId]['taggedGroup'];
                $this->set('extra_data', $extraData);
            }
        }

        parent::_preSave();
    }
}
