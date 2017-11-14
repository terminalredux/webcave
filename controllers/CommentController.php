<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;

class CommentController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    return (new \App\Controllers\ErrorController)->pageNotFound();
  }

  public function actionAdd() {
    

  }
}
