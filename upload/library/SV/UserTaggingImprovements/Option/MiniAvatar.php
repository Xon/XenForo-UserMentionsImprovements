<?php

class SV_UserTaggingImprovements_Option_MiniAvatar
{
    public static function verifyDisplayGroupAvatar(&$option, XenForo_DataWriter $dw, $fieldName)
    {
        if ($option)
        {
            XenForo_Model::create('XenForo_Model_Style')->updateAllStylesLastModifiedDate();
        }
        return true;
    }

}