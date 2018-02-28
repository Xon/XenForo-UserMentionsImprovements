<?php

class SV_UserTaggingImprovements_SV_ReportImprovements_AlertHandler_ReportComment extends XFCP_SV_UserTaggingImprovements_SV_ReportImprovements_AlertHandler_ReportComment
{
    public function getContentUrl(array $content, $canonical = false)
    {
        $extraParams = [];
        if (!empty($content['report_comment_id']))
        {
            $extraParams['report_comment_id'] = $content['report_comment_id'];
        }

        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'posts', $content, $extraParams);
    }

    public function getContentMessage(array $content)
    {
        return isset($content['message']) ? $content['message'] : null;
    }
}
