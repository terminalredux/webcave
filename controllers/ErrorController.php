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

  public function pageNotFound($errorMessage) {
    $this->view->errorMessage = $errorMessage;
    $this->view->render('error/page_not_found');
  }

}
