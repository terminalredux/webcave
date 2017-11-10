<?php
namespace Libs\Exceptions;
use Exception;

class MethodNotFoundException extends Exception
{
  public function __construct($method) {
        $className = array_reverse(explode("\\", __CLASS__))[0];
        parent::__construct("<span style='color: red'>" . $className . ":</span> " . $method . " doesn't exists!");
    }
}
