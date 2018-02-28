<?php

class SV_UserTaggingImprovements_XenForo_Model_Report extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Report
{
    public function alertTaggedMembers(array $report, array $reportComment, array $tagged, array $alreadyAlerted, array $taggingUser)
    {
        SV_UserTaggingImprovements_Globals::$emailedUsers = [];

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        /** @noinspection PhpUndefinedMethodInspection */
        $alertedUsers = parent::alertTaggedMembers($report, $reportComment, $tagged, $alreadyAlerted, $taggingUser);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('report_comment', $reportComment['report_comment_id'], $reportComment, $alertedUsers, $taggingUser, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserTaggedEmailTemplate);

        return $alertedUsers;
    }

    /**
     * @return XenForo_Model|XenForo_Model_UserTagging|SV_UserTaggingImprovements_XenForo_Model_UserTagging
     */
    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
