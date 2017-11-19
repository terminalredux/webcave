<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\Tag\Tag;

class TagController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    AccessControl::onlyForLogged();
    return $this->executeAction('tag/list');
  }

  public function actionList() {
    AccessControl::onlyForLogged();
    $tags = Tag::all([
      'order' => 'name ASC',
      'conditions' => ['status' => Tag::ACTIVE]
    ]);

    if ($this->isPost()) {
      $tag = new Tag();
      $tag->loadCreate();
      if ($tag->save()) {
        $this->success("Pomyślnie utworzono tag $tag->name!");
      } else {
        $this->error(implode('<br>', $tag->errors->full_messages()));
      }
      return $this->executeAction('tag/list');
    }
    return $this->render('tag/list', ['tags' => $tags]);
  }
}
