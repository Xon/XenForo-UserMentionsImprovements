<?php

class SV_UserTaggingImprovements_NixFifty_Tickets_Model_TicketMessage extends XFCP_SV_UserTaggingImprovements_NixFifty_Tickets_Model_TicketMessage
{
    /** @var bool */
    protected $resetEmailed = true;

    /**
     * @param array $message
     * @param array $ticket
     * @param array $category
     * @param array $tagged
     * @param array $alreadyAlerted
     * @return array
     */
    public function alertMentionedUsers(array $message, array $ticket, array $category, array $tagged, array $alreadyAlerted)
    {
        if ($this->resetEmailed)
        {
            SV_UserTaggingImprovements_Globals::$emailedUsers = [];
        }
        $this->resetEmailed = true;

        $userTaggingModel = $this->_getUserTaggingModel();
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = $tagged;
        $alertedUsers = parent::alertMentionedUsers($message, $ticket, $category, $tagged, $alreadyAlerted);
        SV_UserTaggingImprovements_Globals::$AlertedUsersExtraInfo = null;
        $userTaggingModel->emailAlertedUsers('ticket_message', $message['message_id'], $message, $alertedUsers, $message, SV_UserTaggingImprovements_XenForo_Model_UserTagging::UserTaggedEmailTemplate);

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