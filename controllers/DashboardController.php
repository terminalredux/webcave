<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;

/**
 * Actions allowed only for logged users
 */
class DashboardController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    AccessControl::onlyForLogged();
    parent::__construct();
  }

  public function actionIndex() {
    AccessControl::onlyForLogged();
    $this->view->render('dashboard/index');
  }
}
