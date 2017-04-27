<?php

class SV_UserTaggingImprovements_XenForo_Model_ForumWatch extends XFCP_SV_UserTaggingImprovements_XenForo_Model_ForumWatch
{
    public function sendNotificationToWatchUsersOnMessage(array $post, array $thread = null, array $noAlerts = array(), array $noEmail = array())
    {
        if (!empty(SV_UserTaggingImprovements_Globals::$emailedUsers))
        {
            $noEmail = array_merge($noEmail, SV_UserTaggingImprovements_Globals::$emailedUsers);
            SV_UserTaggingImprovements_Globals::$emailedUsers = array();
        }

        return parent::sendNotificationToWatchUsersOnMessage($post, $thread, $noAlerts, $noEmail);
    }
}