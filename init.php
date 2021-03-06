<?php

require_once 'config/paths.php';
require_once 'config/params.php';
require_once 'config/db_connection.php';
require_once 'vendor/autoload.php';

ActiveRecord\Config::initialize(function($config) {
  $config->set_connections([
    'development' => 'mysql://' . USER . ':' . PASSWORD . '@' . HOST . '/' . DBNAME
  ]);
});
