<?php

class SV_UserTaggingImprovements_Installer
{
    public static function install($existingAddOn, $addOnData, SimpleXMLElement $xml)
    {
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $required = '5.4.0';
        $phpversion = phpversion();
        if (version_compare($phpversion, $required, '<'))
        {
            throw new XenForo_Exception(
                "PHP {$required} or newer is required. {$phpversion} does not meet this requirement. Please ask your host to upgrade PHP",
                true
            );
        }
        if (XenForo_Application::$versionId < 1030070)
        {
            throw new XenForo_Exception('XenForo 1.3.0+ is Required!', true);
        }

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
                SET permission_id = 'sv_EnableTagging', permission_value = 'deny'
                WHERE permission_group_id = 'forum' AND permission_id = 'sv_DisableTagging'
            ");
            $db->query("
                UPDATE xf_permission_entry_content
                SET permission_id = 'sv_EnableTagging', permission_value = 'deny'
                WHERE permission_group_id = 'forum' AND permission_id = 'sv_DisableTagging'
            ");

            $db->query("INSERT IGNORE INTO xf_permission_entry (user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                SELECT DISTINCT user_group_id, user_id, 'forum', 'sv_EnableTagging', 'allow', 0
                FROM xf_permission_entry
                WHERE permission_group_id = 'general' AND permission_id IN ('maxTaggedUsers') AND permission_value_int <> 0
            ");
            $db->query("INSERT IGNORE INTO xf_permission_entry_content (content_type, content_id, user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
                SELECT DISTINCT content_type, content_id, user_group_id, user_id, 'forum', 'sv_EnableTagging', 'content_allow', 0
                FROM xf_permission_entry_content
                WHERE permission_group_id = 'general' AND permission_id IN ('maxTaggedUsers') AND permission_value_int <> 0
            ");
        }

        if ($version < 10100000)
        {
            $db->query("
                UPDATE xf_user_group
                SET last_edit_date = ?
                WHERE last_edit_date = 0
            ", [XenForo_Application::$time]);
        }

        if ($version < 1040100)
        {
            $db->query("
                UPDATE xf_user_option
                SET sv_email_on_quote = sv_email_on_tag
            ");
        }
    }

    public static function uninstall()
    {
        $db = XenForo_Application::getDb();

        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_id IN (
                'sv_EnableTagging',
                 'sv_DisableTagging',
                 'sv_ReceiveTagAlertEmails',
                 'sv_TagUserGroup',
                 'sv_ViewPrivateGroups'
            )
        ");

        $db->query("
            DELETE FROM xf_permission_entry_content
            WHERE permission_id IN (
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
    }
}
