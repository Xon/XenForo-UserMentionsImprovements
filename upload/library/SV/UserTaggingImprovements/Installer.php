<?php

class SV_UserTaggingImprovements_Installer
{
    public static function install($existingAddOn, $addOnData, SimpleXMLElement $xml)
    {
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $db = XenForo_Application::getDb();

        SV_Utils_Install::addColumn('xf_user_group', 'sv_taggable', 'tinyint(3) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_private', 'tinyint(3) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_avatar_s', 'text');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_avatar_l', 'text');
        SV_Utils_Install::addColumn('xf_user_group', 'last_edit_date', 'int(11) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_option', 'sv_email_on_tag', 'tinyint(3) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_option', 'sv_email_on_quote', 'tinyint(3) NOT NULL default 0');

        //"update xf_user_option
        //set  sv_email_on_tag = bdtagme_email, sv_email_on_quote = sv_email_on_tag;"
        if ($version <= 1000900)
        {
            $db->query("
                UPDATE xf_permission_entry
                set permission_id = 'sv_EnableTagging', permission_value = 'deny'
                WHERE permission_group_id = 'forum' and permission_id = 'sv_DisableTagging'
            ");
            $db->query("
                UPDATE xf_permission_entry_content
                set permission_id = 'sv_EnableTagging', permission_value = 'deny'
                WHERE permission_group_id = 'forum' and permission_id = 'sv_DisableTagging'
            ");

            $db->query("insert ignore into xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select distinct user_group_id, user_id, 'forum', 'sv_EnableTagging', 'allow', 0
                from xf_permission_entry
                where permission_group_id = 'general' and permission_id in ('maxTaggedUsers') and permission_value_int <> 0
            ");
            $db->query("insert ignore into xf_permission_entry_content (content_type, content_id, user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                select distinct content_type, content_id, user_group_id, user_id, 'forum', 'sv_EnableTagging', 'content_allow', 0
                from xf_permission_entry_content
                where permission_group_id = 'general' and permission_id in ('maxTaggedUsers') and permission_value_int <> 0
            ");
            XenForo_Application::defer('Permission', array(), 'Permission', true);
        }

        if ($version < 10100000)
        {
            $db->query("
                UPDATE xf_user_group
                set last_edit_date = ?
                WHERE last_edit_date = 0
            ", array(XenForo_Application::$time));
        }

        if ($version < 1040100)
        {
            $db->query("
                update xf_user_option
                set sv_email_on_quote = sv_email_on_tag
            ");
        }
    }

    public static function uninstall()
    {
        $db = XenForo_Application::getDb();

        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_id in (
                'sv_EnableTagging',
                 'sv_DisableTagging',
                 'sv_ReceiveTagAlertEmails',
                 'sv_TagUserGroup',
                 'sv_ViewPrivateGroups'
            )
        ");

        $db->query("
            DELETE FROM xf_permission_entry_content
            WHERE permission_id in (
                'sv_EnableTagging',
                 'sv_DisableTagging',
                 'sv_ReceiveTagAlertEmails',
                 'sv_TagUserGroup',
                 'sv_ViewPrivateGroups'
            )
        ");

        SV_Utils_Install::dropColumn('xf_user_group', 'last_edit_date');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_taggable');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_avatar_s');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_avatar_l');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_private');
        SV_Utils_Install::dropColumn('xf_user_option', 'sv_email_on_tag');
        SV_Utils_Install::dropColumn('xf_user_option', 'sv_email_on_quote');

        XenForo_Application::defer('Permission', array(), 'Permission', true);
    }
}