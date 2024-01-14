<?php

require 'vendor/autoload.php';
require 'CombinedSessionHandler.php';

session_set_save_handler(new \CombinedSessionHandler());
session_start();