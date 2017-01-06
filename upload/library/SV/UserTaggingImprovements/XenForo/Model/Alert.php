<?php

class SV_UserTaggingImprovements_XenForo_Model_Alert extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Alert
{
    public function getAlertHandler($contentType)
    {
        return $this->_getAlertHandlerFromCache($contentType);
    }

    public function alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, array $extraData = null)
    {
        if (!empty(SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertUserId]['taggedGroupId']))
        {
            if ($extraData === null)
            {
                $extraData = array();
            }
            $extraData['taggedGroupId'] = SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertUserId]['taggedGroupId'];
            $extraData['taggedGroup'] = SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertUserId]['taggedGroup'];
            unset(SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo[$alertUserId]);

        }

        return parent::alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, $extraData);
    }
}