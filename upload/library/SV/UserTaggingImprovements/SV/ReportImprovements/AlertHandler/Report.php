<?php

class SV_UserTaggingImprovements_SV_ReportImprovements_AlertHandler_Report extends XFCP_SV_UserTaggingImprovements_SV_ReportImprovements_AlertHandler_Report
{
    public function getContentUrl(array $content, $canonical = false)
    {
        $extraParams = array();
        if (!empty($content['report_comment_id']))
        {
            $extraParams['report_comment_id'] = $content['report_comment_id'];
        }
        return XenForo_Link::buildPublicLink(($canonical ? 'canonical:' : '') . 'posts', $content, $extraParams);
    }
}