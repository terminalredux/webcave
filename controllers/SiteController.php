<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\Session\Session;
use App\Models\LoginModel;
use App\Models\Article\Article;

class SiteController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    $articles = Article::all([
      'order' => 'available_from DESC'
    ]);
    $list = [];
    foreach ($articles as $article) {
      if ($article->availableForGuest())
        $list[] = $article;
    }
    return $this->render('article/public-list', [
      'articles' => $list,
      'title' => 'Najnowsze artykuły',
    ]);
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
