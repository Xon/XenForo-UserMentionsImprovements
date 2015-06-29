<?php

class SV_UserTaggingImprovements_XenForo_AlertHandler_DiscussionMessage_Post extends XFCP_SV_UserTaggingImprovements_XenForo_AlertHandler_DiscussionMessage_Post
{
    public function getContentUrl(array $content, $canonical = false)
    {
        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'posts', $content);
    }
}