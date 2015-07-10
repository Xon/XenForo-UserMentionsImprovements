<?php

class SV_UserTaggingImprovements_XenForo_Model_Alert extends XFCP_SV_UserTaggingImprovements_XenForo_Model_Alert
{
    public function getAlertHandler($contentType)
    {
        return $this->_getAlertHandlerFromCache($contentType);
    }
}