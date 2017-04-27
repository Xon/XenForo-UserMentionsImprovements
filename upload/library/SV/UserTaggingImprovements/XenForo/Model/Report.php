<?php

class SV_UserTaggingImprovements_XenForo_Model_Report extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Report
{
    public function alertTaggedMembers(array $report, array $reportComment, array $tagged, array $alreadyAlerted, array $taggingUser)
    {
        SV_UserTaggingImprovements_Globals::$emailedUsers = array();

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        $alertedUsers = parent::alertTaggedMembers($report, $reportComment, $tagged, $alreadyAlerted, $taggingUser);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('report', $reportComment['report_id'], $reportComment, $alertedUsers, $taggingUser, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserTaggedEmailTemplate);
        return $alertedUsers;
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
