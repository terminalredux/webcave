<?php
namespace Libs\Base;

use Libs\Base\Controller;
use App\Controllers\{
  SiteController,
  CategoryController,
  ArticleController,
  DashboardController,
  FileController
};

class ControllerFactory
{
  /**
   * TODO zrobić tak żebym nie musiał za każdym nowym kontrolerem dodawać
   * go tutaj. Można wykorzystać automatycznego use: \App\Controllers\TuWygenerowanaNazwaKontrolera
   * przy pomocy file_exists ??? coś takiego. Nazwa kontrolera będzie generowana na podstawie
   * przekazanago do metody controllerAliasu. 
   * @param string $controllerAlias
   * @return Controller Object
   */
  public static function create($controllerAlias) : Controller {
    $controllerAlias = strtolower($controllerAlias);
    if ($controllerAlias == "site") {
      $controller = new SiteController;
    } else if ($controllerAlias == "category") {
      $controller = new CategoryController;
    } else if ($controllerAlias == "article") {
      $controller = new ArticleController;
    } else if ($controllerAlias == "dashboard") {
      $controller = new DashboardController;
    } else if ($controllerAlias == "file") {
      $controller = new FileController;
    }
    return $controller;
  }
}
