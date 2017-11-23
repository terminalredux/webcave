<?php

require_once 'config/paths.php';
require_once 'config/params.php';
require_once 'config/db_connection.php';
require_once 'vendor/autoload.php';
//require 'vendor/wysiwyg-editor-php-sdk-master/lib/FroalaEditor.php';

ActiveRecord\Config::initialize(function($config) {
  $config->set_connections([
    'development' => 'mysql://' . USER . ':' . PASSWORD . '@' . HOST . '/' . DBNAME
  ]);
});
