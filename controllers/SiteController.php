<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\Session\Session;
use App\Models\LoginModel;

class SiteController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    $this->view->render('site/index');
  }

  public function actionSearch() {
    var_dump($_GET['txt']);die;
  }

  public function actionLogin() {
    $model = new LoginModel();

    if ($this->isPost()) {
       if ($model->login()) {
         //TODO ustawić flashe, returny viewsów, sesje
         Session::set('logged', true);
         $this->executeAction('dashboard');
       } else {
         Session::set('logged', false);
         $this->view->render('site/login');
       }
     } else {
       if (Session::get('logged')) {
          $this->executeAction('site/index');
       } else {
         $this->view->render('site/login');
       }
     }
  }

  public function actionLogout() {
    Session::unset();
    $this->executeAction('site/index');
  }
}
