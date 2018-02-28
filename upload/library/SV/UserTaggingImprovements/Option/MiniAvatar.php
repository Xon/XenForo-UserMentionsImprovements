<?php

class SV_UserTaggingImprovements_Option_MiniAvatar
{
    public static function verifyDisplayGroupAvatar(&$option, XenForo_DataWriter $dw, $fieldName)
    {
        if ($option)
        {
            /** @var XenForo_Model_Style $model */
            $model = $dw->getModelFromCache('XenForo_Model_Style');
            $model->updateAllStylesLastModifiedDate();
        }
        return true;
    }

}
