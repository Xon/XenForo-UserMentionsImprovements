<?php

class SV_UserTaggingImprovements_XenForo_BbCode_Formatter_Text extends XFCP_SV_UserTaggingImprovements_XenForo_BbCode_Formatter_Text
{
    public function getTags()
    {
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
        return $this->renderSubTree($tag['children'], $rendererStates);
    }
}
