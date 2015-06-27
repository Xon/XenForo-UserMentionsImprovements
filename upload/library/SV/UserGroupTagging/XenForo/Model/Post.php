<?php

class SV_UserGroupTagging_XenForo_Model_Post extends XFCP_SV_UserGroupTagging_XenForo_Model_Post
{
    public function alertTaggedMembers(array $post, array $thread, array $forum, array $tagged, array $alreadyAlerted)
    {
        $userTaggingModel = $this->_getUserTaggingModel();
        $tagged = $userTaggingModel->expandTaggedGroups($tagged);
        $alertedUsers = parent::alertTaggedMembers($post, $thread, $forum, $tagged, $alreadyAlerted);
        $userTaggingModel->emailAlertedUsers($alertedUsers, $post);
        return $alertedUsers;
    }

    protected function _getUserTaggingModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserTagging');
    }
}
