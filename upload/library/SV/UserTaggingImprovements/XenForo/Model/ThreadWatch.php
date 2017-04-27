<?php

class SV_UserTaggingImprovements_XenForo_Model_ThreadWatch extends XFCP_SV_UserTaggingImprovements_XenForo_Model_ThreadWatch
{
    public function sendNotificationToWatchUsersOnReply(array $reply, array $thread = null, array $noAlerts = array())
    {
        $emailedUsers = null;
        if (!empty(SV_UserTaggingImprovements_Globals::$emailedUsers))
        {
            $emailedUsers = array_keys(SV_UserTaggingImprovements_Globals::$emailedUsers);
            SV_UserTaggingImprovements_Globals::$emailedUsers = array();
            foreach($emailedUsers as $userId)
            {
                XenForo_Model_ThreadWatch::$_preventDoubleNotify[$thread['thread_id']][$userId] = true;
            }
        }

        $ret = parent::sendNotificationToWatchUsersOnReply($reply, $thread , $noAlerts);
        if ($emailedUsers !== null)
        {
            $ret['emailed'] = empty($ret['emailed']) ? $emailedUsers : array_merge($ret['emailed'], $emailedUsers);
        }
        return $ret;
    }
}