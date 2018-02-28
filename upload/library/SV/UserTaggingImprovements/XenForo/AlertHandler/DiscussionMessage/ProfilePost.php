<?php

class SV_UserTaggingImprovements_XenForo_AlertHandler_DiscussionMessage_ProfilePost extends XFCP_SV_UserTaggingImprovements_XenForo_AlertHandler_DiscussionMessage_ProfilePost
{
    public function getContentUrl(array $content, $canonical = false)
    {
        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'profile-posts', $content);
    }

    public function getContentMessage(array $content)
    {
        return isset($content['message']) ? $content['message'] : null;
    }
}
