<?php

// This class is used to encapsulate global state between layers without using $GLOBAL[] or
// relying on the consumer being loaded correctly by the dynamic class autoloader
class SV_UserGroupTagging_Globals
{
    public static $CanGroupTag = false;

    private function __construct() {}
}
