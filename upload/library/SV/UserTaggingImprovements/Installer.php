<?php

class SV_UserTaggingImprovements_Installer
{
    public static function install($installedAddon, array $addonData, SimpleXMLElement $xml)
    {
        $db = XenForo_Application::getDb();

        SV_Utils_Install::addColumn('xf_user_group', 'sv_taggable', 'tinyint(3) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_private', 'tinyint(3) NOT NULL default 0');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_avatar_s', 'text');
        SV_Utils_Install::addColumn('xf_user_group', 'sv_avatar_l', 'text');
        SV_Utils_Install::addColumn('xf_user_option', 'sv_email_on_tag', 'tinyint(3) NOT NULL default 0');

        //"update xf_user_option
        //set  sv_email_on_tag = bdtagme_email ;"
    }

    public static function uninstall()
    {
        $db = XenForo_Application::getDb();

        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_group_id = 'forum' and permission_id = 'sv_DisableTagging'
        ");
        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_group_id = 'general' and permission_id = 'sv_ReceiveTagAlertEmails'
        ");
        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_group_id = 'general' and permission_id = 'sv_TagUserGroup'
        ");
        $db->query("
            DELETE FROM xf_permission_entry
            WHERE permission_group_id = 'general' and permission_id = 'sv_ViewPrivateGroups'
        ");

        SV_Utils_Install::dropColumn('xf_user_group', 'sv_taggable');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_avatar_s');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_avatar_l');
        SV_Utils_Install::dropColumn('xf_user_group', 'sv_private');
        SV_Utils_Install::dropColumn('xf_user_option', 'sv_email_on_tag');
    }
}