<?php

class SV_UserTaggingImprovements_XenForo_Model_ProfilePost extends XFCP_SV_UserTaggingImprovements_XenForo_Model_ProfilePost
{
    public function alertTaggedMembers(
        array $profilePost, array $profileUser, array $tagged, array $alreadyAlerted = [],
        $isComment = false, array $taggingUser = null
    )
    {
        SV_UserTaggingImprovements_Globals::$emailedUsers = [];

        if (!$taggingUser)
        {
            $taggingUser = $profilePost;
        }

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        $alertedUsers = parent::alertTaggedMembers($profilePost, $profileUser, $tagged, $alreadyAlerted, $isComment, $taggingUser);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('profile_post', $profilePost['profile_post_id'], $profilePost, $alertedUsers, $taggingUser, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserTaggedEmailTemplate);

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
