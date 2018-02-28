<?php

class SV_UserTaggingImprovements_Template
{
    /** @var null|XenForo_Model_UserGroup */
    protected static $userGroupModel = null;

    public static function template_hook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        if ($hookName != 'sv_usergroup_list')
        {
            return;
        }

        if (empty($hookParams['template']))
        {
            $hookParams['template'] = 'UserGroupTagging_MiniAvatar.css';
        }

        if (empty($hookParams))
        {
            return;
        }

        $options = XenForo_Application::getOptions();
        if (!$options->sv_displayGroupAvatar)
        {
            return;
        }

        if (self::$userGroupModel === null)
        {
            self::$userGroupModel = XenForo_Model::create('XenForo_Model_UserGroup');
        }

        $groups = self::$userGroupModel->getAllUserGroups();
        if (empty($groups))
        {
            return;
        }

        $template->preloadTemplate($hookParams['template']);
        $contents .= $template->create($hookParams['template'], ['groups' => $groups, 'xenOptions' => $options->getOptions()])->render();

        return;
    }
}
