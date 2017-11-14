<?php
namespace Libs\Base;

use Libs\Exceptions\MethodNotFoundException;
use Libs\Session\Session;
use Libs\FlashMessage\Flash;
use Libs\AccessControl\AccessControl;
use App\Controllers\ErrorController;

/**
 * Singleton class
 */
class Bootstrap
{
  /**
   * singleton design pattern
   */
  private static $instance = false;

  /**
   * Singleton design pattern
   * @return Bootstrap object
   */
  public static function getInstance() : self {
    if (self::$instance == false) {
      self::$instance = new Bootstrap();
    }
    return self::$instance;
  }

  private function __construct() {}

  public function initTemplate() {
    if (AccessControl::logged()) {
      require_once "views/template/admin.php";
    } else {
      require_once "views/template/main.php";
    }

  }

  public function sessionInit() {
    Session::init();
  }

  public function getContent() {
    $url = $this->getUrl();
    $file = 'controllers/' . $url[0] . 'Controller.php';

    if (file_exists($file) && $file != 'controllers/errorController.php') {
      $controllerNamespace = '\App\Controllers\\' . ucfirst($url[0]) . 'Controller';
      $controller = new $controllerNamespace;
      Flash::disableFlash();
      $this->executeRoute($url, $controller);
    } else {
      (new ErrorController)->pageNotFound("<strong style='font-size: 1.2em'>" . $url[0] . "Controller</strong> doesn't exists!");
      return false;
    }
  }

  /**
   * Check if its searched controller
   * @param string alias
   * @return bool
   */
  public function checkController(string $alias) : bool {
    $url = $this->getUrl();
    return $url[0] == $alias;
  }

  /**
   * Check if its searched controller/action
   * @param string alias
   * @return bool
   */
  public function checkAction(string $alias) : bool {
    $url = $this->getUrl();
    return $url[0] . '/' . $url[1] === $alias;
  }

  /**
   * Check if its searched action's param
   * @param string param
   * @return bool
   */
  public function checkParam(string $param) : bool {
    $url = $this->getUrl();
    if (isset($url[2])) {
      return $url[2] === $param;
    }
    return false;
  }

  /**
   * Returns param from the current url
   */
  public function getParam() : ? string {
    $url = $this->getUrl();
    if (isset($url[2])) {
      return $url[2];
    }
    return null;
  }

  /**
   * @return array $url
   */
  private function getUrl() : array {
    if(!isset($_GET['url'])) {
      $url[0] = DEFAULT_CONTROLLER;
      $url[1] = DEFAULT_ACTION;
    } else {
      $url = $_GET['url'];
      $url = rtrim($url, '/');
    	$url = explode('/',$_GET['url']);
    }
    return $url;
  }

  /**
   * @param array $url
   * @param Controller $controller
   * @return void
   */
  private function executeRoute($url, $controller) {
    try {
      if (!isset($url[1])) {
        $controller->{DEFAULT_ACTION_INDEX}();  // sets default action to index
      } else if (isset($url[2])) {              // if user provides arguments for action
        if (empty($url[2])) {                   // if user provides e.g. site/index/
          $controller->{'action' . $url[1]}();
        } else {
          if (empty($url[3])) {
            $controller->{'action' . $url[1]}($url[2]);
          } else {
            $controller->{'action' . $url[1]}($url[2], $url[3]);
          }
        }
      } else if (isset($url[1])) {
        if (empty($url[1])) {
          $url[1] = DEFAULT_CONTROLLERS_ACTION; // if user provide only "domain/controller/"
        }
        $controller->{'action' . $url[1]}();
      }
    } catch (MethodNotFoundException $e) {
        (new ErrorController)->pageNotFound("<strong style='font-size: 1.2em'>action" . $url[1] . "</strong> doesn't exists!");
        //alternative: $e->getMessage();
    }
  }

}
