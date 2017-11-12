<?php
namespace Libs\Base;
use Libs\Base\View;
use Libs\FlashMessage\{
  Flash,
  FlashConf
};
use Libs\Exceptions\MethodNotFoundException;

class Controller
{
  public function __construct() {
    $this->view = new View();
  }

  /**
   * If action doesn't exists, throws exception
   */
  public function __call($method, $arguments) {
    if (!method_exists($this, $method)) {
      throw new MethodNotFoundException($method);
    }
  }

  /**
   * Check if there is a post request
   */
  public function isPost() : bool {
    return !empty($_POST);
  }

  /**
   * Check if there is a post request
   */
  public function isGet() : bool {
    return !empty($_GET);
  }

  /**
   * Check if there is a file
   */
  public function isFile() : bool {
    return !empty($_FILES);
  }

  /**
   * Routes to action in different controller
   * For example 'site/index'
   */
  public function executeAction($route) {
    header('Location: ' . URL . $route);
  }

  /**
   * @inheritdoc libs/base/View.php
   */
  public function render(string $path, array $params = null) {
    return $this->view->render($path, $params);
  }

  /**
   * Success flash message
   * @param string $message
   */
  protected function success(string $message) {
    Flash::say('success', $message);
  }

  /**
   * Error flash message
   * @param string $message
   */
  protected function error(string $message) {
    Flash::say('error', $message);
  }

  /**
   * Shows flash message depends on FlashConf object
   */
  protected function showFlash(FlashConf $result) {
    if ($result->isSuccess()) {
      $type = 'success';
    } elseif ($result->isError()) {
      $type = 'error';
    }
    Flash::say($type, $result->getContent());
  }

  /**
   * Automaticlly gets Controller and Action name
   * and use executeAction to this route.
   * Works like a simply refresh.
   */
  public function refresh() {
    //Prepare controller alias
    $className = get_class($this);
    $className = array_reverse(explode('\\', $className))[0];
    $className = strtolower($className);
    $className = str_replace('controller', '', $className);

    //Prepare action alias
    $methodName = strtolower(debug_backtrace()[1]['function']);
    $methodName = str_replace('action', '', $methodName);

    $this->executeAction($className . '/' . $methodName);
  }

  /**
   * If one of the parameters are null execute t action
   * @param array $params
   * @param string $route
   */
  protected function checkParams(array $params, string $route) {
    $success= true;
    $message = "";
    foreach ($params as $key => $value) {
      if (!$value) {
        $success = false;
        $message .= "$key: brak wartość<br> ";
      }
    }
    if (!$success) {
      $this->error($message);
      return $this->executeAction($route);
    }
  }


}
