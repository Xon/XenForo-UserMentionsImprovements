<?php

class SV_UserTaggingImprovements_Listener
{
    const AddonNameSpace = 'SV_UserTaggingImprovements_';

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.$class;
    }

    public static function init_dependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        SV_UserTaggingImprovements_Helper_String::setupCallbacks();
    }
}