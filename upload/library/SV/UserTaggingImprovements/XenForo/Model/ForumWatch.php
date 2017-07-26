<?php

class SV_UserTaggingImprovements_XenForo_Model_ForumWatch extends XFCP_SV_UserTaggingImprovements_XenForo_Model_ForumWatch
{
    public function sendNotificationToWatchUsersOnMessage(array $post, array $thread = null, array $noAlerts = array(), array $noEmail = array())
    {
        if (!empty(SV_UserTaggingImprovements_Globals::$emailedUsers))
        {
            $emailedUsers = array_keys(SV_UserTaggingImprovements_Globals::$emailedUsers);
            SV_UserTaggingImprovements_Globals::$emailedUsers = array();

            foreach($emailedUsers as $userId)
            {
                XenForo_Model_ForumWatch::$_preventDoubleNotify[$thread['thread_id']][$userId] = true;
            }
            $noEmail = array_merge($noEmail, $emailedUsers);
            $noAlerts = array_merge($noAlerts, $emailedUsers);
        }

        return parent::sendNotificationToWatchUsersOnMessage($post, $thread, $noAlerts, $noEmail);
    }
}