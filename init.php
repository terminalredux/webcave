<?php

require_once 'config/paths.php';
require_once 'config/params.php';
require_once 'vendor/autoload.php';

ActiveRecord\Config::initialize(function($config) {
  //$config->set_model_directory('models');
  $config->set_connections([
    'development' => 'mysql://root:@127.0.0.1/webcave2'
  ]);
});
