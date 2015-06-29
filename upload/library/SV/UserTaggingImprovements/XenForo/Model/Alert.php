<?php

class SV_UserGroupTagging_XenForo_Model_Alert extends XFCP_SV_UserGroupTagging_XenForo_Model_Alert
{
	public function getAlertHandler($contentType)
	{
        return $this->_getAlertHandlerFromCache($contentType);
    }
}