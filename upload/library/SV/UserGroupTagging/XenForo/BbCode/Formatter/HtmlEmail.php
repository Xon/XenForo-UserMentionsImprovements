<?php

class SV_UserGroupTagging_XenForo_BbCode_Formatter_HtmlEmail extends XFCP_SV_UserGroupTagging_XenForo_BbCode_Formatter_HtmlEmail
{
    public function getTags()
    {
        $tags = parent::getTags();

        $tags['usergroup'] = array
        (
            'hasOption' => true,
            'stopSmilies' => true,
            'callback' => array($this, '_renderTagUserGroup')
        );

        return $tags;
    }

    public function _renderTagUserGroup(array $tag, array $rendererStates)
    {
        $content = $this->renderSubTree($tag['children'], $rendererStates);
        if ($content === '') 
        {
            return '';
        }

        $userGroupId = intval($tag['option']);
        if (!$userGroupId)
        {
            return $content;
        }

        $link = XenForo_Link::buildPublicLink('full:members', '', array('ug' => $userGroupId));
        $usergroupname = $this->stringifyTree($tag['children']);

        return $this->_wrapInHtml('<a href="' . htmlspecialchars($link) . '" class="username ug" data-usergroup="' . $userGroupId . ', ' . htmlspecialchars($usergroupname) . '">', '</a>', $content);
    }
}
