<?php

class SV_UserTaggingImprovements_XenForo_BbCode_Formatter_Base extends XFCP_SV_UserTaggingImprovements_XenForo_BbCode_Formatter_Base
{
    /** @var bool  */
    protected $canViewPublicGroups;

    public function getTags()
    {
        $visitor = XenForo_Visitor::getInstance();
        $this->canViewPublicGroups = $visitor->hasPermission('general', 'sv_ViewPublicGroups');

        $tags = parent::getTags();

        $tags['usergroup'] = [
            'hasOption'   => true,
            'stopSmilies' => true,
            'callback'    => [$this, '_renderTagUserGroup']
        ];

        return $tags;
    }

    public function _renderTagUserGroup(array $tag, array $rendererStates)
    {
        $content = $this->renderSubTree($tag['children'], $rendererStates);
        if ($content === '')
        {
            return '';
        }

        if (!$this->canViewPublicGroups)
        {
            return $content;
        }

        $userGroupId = intval($tag['option']);
        if (!$userGroupId)
        {
            return $content;
        }

        $userGroupTitle = $this->stringifyTree($tag['children']);
        $linkParts = SV_UserTaggingImprovements_Helper_String::getUserGroupLinkParts($userGroupId, $userGroupTitle);

        return $this->_wrapInHtml($linkParts[0], $linkParts[1], $content);
    }
}
