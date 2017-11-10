<?php

/**
 * Sets default controller/action
 * if user provides only domain
 */
define('DEFAULT_CONTROLLER', 'site');
define('DEFAULT_ACTION', 'index');

/**
 * if user provides only domain/controller/
 * long story short: sets default action to index
 * for ALL controllers!
 */
define('DEFAULT_CONTROLLERS_ACTION', 'index');
define('DEFAULT_ACTION_INDEX', 'actionIndex');
