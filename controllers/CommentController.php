<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\{
  Comment\Comment,
  Comment\CommentTableFiller
};

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

  public function actionList(string $status = 'not-publicated') {
    AccessControl::onlyForLogged();

    $tableFiller = new CommentTableFiller();
    if (!$tableFiller->setWhereGroup($status)) {
      return (new ErrorController)->pageNotFound();
    }
    $tableRows = $tableFiller->fetch();

    return $this->render('comment/admin-list', ['comments' => $tableRows]);
  }

}
