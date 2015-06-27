<?php

class SV_UserGroupTagging_Listener
{
    const AddonNameSpace = 'SV_UserGroupTagging';

    public static function install($installedAddon, array $addonData, SimpleXMLElement $xml)
    {
        SV_UserGroupTagging_Install::addColumn('xf_user_group', 'sv_tagable', 'tinyint(1) NOT NULL default 0');
        SV_UserGroupTagging_Install::addColumn('xf_user_group', 'sv_private', 'tinyint(1) NOT NULL default 0');
        SV_UserGroupTagging_Install::addColumn('xf_user_group', 'sv_avatar_s', 'text');
        SV_UserGroupTagging_Install::addColumn('xf_user_group', 'sv_avatar_l', 'text');
        SV_UserGroupTagging_Install::addColumn('xf_user_option', 'sv_emailOnTag', 'tinyint(1) NOT NULL default 0');

        //"update xf_user_option
        //set  sv_emailOnTag = bdtagme_email ;"
    }

    public static function uninstall()
    {
        SV_UserGroupTagging_Install::dropColumn('xf_user_group', 'sv_tagable');
        SV_UserGroupTagging_Install::dropColumn('xf_user_group', 'sv_avatar_s');
        SV_UserGroupTagging_Install::dropColumn('xf_user_group', 'sv_avatar_l');
        SV_UserGroupTagging_Install::dropColumn('xf_user_group', 'sv_private');
        SV_UserGroupTagging_Install::dropColumn('xf_user_option', 'sv_emailOnTag');
    }

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.'_'.$class;
    }

    public static function init_dependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        SV_UserGroupTagging_Helper_String::setupCallbacks();
    }
}