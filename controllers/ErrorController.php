<?php
namespace App\Controllers;
use Libs\Base\Controller;

class ErrorController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function pageNotFound(string $errorMessage = 'Strona nie istnieje') {
    $this->view->errorMessage = $errorMessage;
    $this->view->render('error/page_not_found');
  }

}
