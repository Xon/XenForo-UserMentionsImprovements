<?php

// This class is used to encapsulate global state between layers without using $GLOBAL[] or
// relying on the consumer being loaded correctly by the dynamic class autoloader
class SV_UserTaggingImprovements_Globals
{
    /** @var null|SV_UserTaggingImprovements_XenForo_ControllerAdmin_UserGroup|XenForo_ControllerAdmin_UserGroup */
    public static $UserGroupAdminController = null;
    /** @var bool|null */
    public static $emailOnTag               = null;
    /** @var bool|null */
    public static $emailOnQuote             = null;
    /** @var bool */
    public static $CanEnableEmailOnTag      = false;
    /** @var array|null */
    public static $AlertedUsersExtraInfo    = null;

    public static $emailedUsers = [];

    private function __construct() { }
}
