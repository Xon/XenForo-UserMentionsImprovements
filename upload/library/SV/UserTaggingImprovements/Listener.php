<?php

class SV_UserTaggingImprovements_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_UserTaggingImprovements_' . $class;
    }

    public static function init_dependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        SV_UserTaggingImprovements_Helper_String::setupCallbacks();
    }
}
