<?php

// This class is used to encapsulate global state between layers without using $GLOBAL[] or
// relying on the consumer being loaded correctly by the dynamic class autoloader
class SV_UserTaggingImprovements_Globals
{
    public static $UserGroupAdminController = null;
    public static $emailOnTag = null;
    public static $CanEnableEmailOnTag = false;
    public static $AlertedUsersExtraInfo = null;

    public static $emailedUsers = array();

    private function __construct() {}
}
