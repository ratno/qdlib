<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/web/bootstrap.php');

// garbage collect for qformstate files
QFileFormStateHandler::GarbageCollect();
