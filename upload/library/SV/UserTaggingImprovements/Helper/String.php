<?php

class SV_UserTaggingImprovements_Helper_String
{
    public static $originalBodyText = null;

    public static function setupCallbacks()
    {
        $original = XenForo_Template_Helper_Core::$helperCallbacks['bodytext'];
        if (!empty($original) && $original[0] === 'self')
        {
            $original[0] = 'XenForo_Template_Helper_Core';
        }
        self::$originalBodyText = $original;
        XenForo_Template_Helper_Core::$helperCallbacks['bodytext'] = [__CLASS__, 'helperBodyText'];
    }

    public static function helperBodyText()
    {
        $args = func_get_args();

        if (self::$originalBodyText !== null)
        {
            $string = call_user_func_array(self::$originalBodyText, $args);
        }
        else
        {
            $string = array_shift($args);
        }

        $string = self::linkTaggedUserGroup($string);

        return $string;
    }

    public static function linkTaggedUserGroup($string)
    {
        $string = preg_replace_callback('#(?<=^|\s|[\](,]|--|@)@\[ug_(\d+):(\'|"|&quot;|)(.*)\\2\]#iU', [
            'self',
            '_linkTaggedUserGroupCallback'
        ], $string);

        return $string;
    }

    protected static function _linkTaggedUserGroupCallback(array $matches)
    {
        $userGroupId = intval($matches[1]);
        $userGroupTitle = htmlspecialchars($matches[3], null, 'utf-8', false);

        $linkParts = SV_UserTaggingImprovements_Helper_String::getUserGroupLinkParts($userGroupId, $userGroupTitle);

        return $linkParts[0] . htmlspecialchars($userGroupTitle) . $linkParts[1];
    }

    static $groupAvatar = null;
    static $groupUsername = null;

    public static function getUserGroupLinkParts($userGroupId, $userGroupTitle)
    {
        $userGroupId = intval($userGroupId);
        if ($userGroupId <= 0)
        {
            return ['', ''];
        }
        $link = XenForo_Link::buildPublicLink('full:members', [], ['ug' => $userGroupId]);
        if (self::$groupAvatar === null)
        {
            $options = XenForo_Application::getOptions();
            /** @noinspection PhpUndefinedFieldInspection */
            self::$groupUsername = $options->sv_styleGroupUsername
                ? 'username'
                : '';
            /** @noinspection PhpUndefinedFieldInspection */
            self::$groupAvatar = $options->sv_displayGroupAvatar
                ? '<span class="groupImg"></span>'
                : '';
        }

        return [
            '<a href="' . htmlspecialchars($link) . '" class="' . self::$groupUsername . ' ug" data-usergroup="' . $userGroupId . ', ' . htmlspecialchars($userGroupTitle) . '"><span class="style' . $userGroupId . '">' . self::$groupAvatar,
            '</span></a>'
        ];
    }
}
