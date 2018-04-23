<?php

class SV_UserTaggingImprovements_NixFifty_Tickets_AlertHandler_TicketMessage extends XFCP_SV_UserTaggingImprovements_NixFifty_Tickets_AlertHandler_TicketMessage
{
    public function getContentUrl(array $content, $canonical = false)
    {
        $extraParams = [];
        if (!empty($content['message_id']))
        {
            $extraParams['message_id'] = $content['message_id'];
        }

        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'ticket-message', $content, $extraParams);
    }

    public function getContentMessage(array $content)
    {
        return isset($content['message']) ? $content['message'] : null;
    }
}
