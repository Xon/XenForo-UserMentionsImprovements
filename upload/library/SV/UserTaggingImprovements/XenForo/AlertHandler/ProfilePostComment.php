<?php

class SV_UserTaggingImprovements_XenForo_AlertHandler_ProfilePostComment extends XFCP_SV_UserTaggingImprovements_XenForo_AlertHandler_ProfilePostComment
{
    public function getContentUrl(array $content, $canonical = false)
    {
        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'profile-posts/comments', $content);
    }
}
