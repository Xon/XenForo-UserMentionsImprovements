<?php

class SV_UserTaggingImprovements_XenForo_DataWriter_UserGroup extends XFCP_SV_UserTaggingImprovements_XenForo_DataWriter_UserGroup
{
    protected function _getFields()
    {
        $fields = parent::_getFields();
        $fields['xf_user_group']['sv_taggable']  = array('type' => self::TYPE_UINT,   'default' => 0);
        $fields['xf_user_group']['sv_private']   = array('type' => self::TYPE_UINT,   'default' => 0);
        $fields['xf_user_group']['sv_avatar_s']  = array('type' => self::TYPE_STRING, 'default' => '');
        $fields['xf_user_group']['sv_avatar_l']  = array('type' => self::TYPE_STRING, 'default' => '');
        $fields['xf_user_group']['last_edit_date']  = array('type' => self::TYPE_UINT, 'default' => 0);
        return $fields;
    }

    protected function _preSave()
    {
        parent::_preSave();
        if ($this->isUpdate() && ($this->isChanged('sv_taggable') || $this->isChanged('sv_private') || $this->isChanged('sv_avatar_s')) ||
            $this->isInsert() && ($this->get('sv_taggable') && !$this->get('sv_private') && $this->get('sv_avatar_s')))
        {
            $this->set('last_edit_date', XenForo_Application::$time);
        }
    }

	protected function _postDelete()
	{
        parent::_postDelete();
        $this->_cacheInvalidation();
    }

	protected function _postSaveAfterTransaction()
	{
        parent::_postSaveAfterTransaction();
        if (($this->isChanged('last_edit_date'))
        {
            $this->_cacheInvalidation();
        }
    }

    protected function _cacheInvalidation();
    {
        $this->getModelFromCache('XenForo_Model_Style')->updateAllStylesLastModifiedDate();
    }
}