<?php

class SV_UserTaggingImprovements_XenForo_Html_Renderer_BbCode extends XFCP_SV_UserTaggingImprovements_XenForo_Html_Renderer_BbCode
{
    public function handleTagA($text, XenForo_Html_Tag $tag)
    {
        $group_id = intval($tag->attribute('data-usergroup'));
        if ($group_id)
        {
            return "[USERGROUP={$group_id}]{$text}[/USERGROUP]";
        }
        return parent::handleTagA($text, $tag);
    }
}