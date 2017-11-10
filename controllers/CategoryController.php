<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\Category\{
  CategoryQuery,
  Category
};


class CategoryController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * This action exists only because
   * default action is index but in this Controller
   * main action is actionList
   */
  public function actionIndex() {
    AccessControl::onlyForLogged();
    return $this->executeAction('category/list');
  }

  /**
   * Main action
   * @param string $status
   */
  public function actionList(string $status = 'active') {
    AccessControl::onlyForLogged();
    if ($status == 'removed') {
      $status = [Category::STATUS_REMOVED];
    } elseif ($status == 'active') {
      $status = [Category::STATUS_ACTIVE, Category::STATUS_HIDDEN];
    } else {
      return $this->refresh();
    }

    $categories = CategoryQuery::getAll([
      'sort' => ['updated_at', 'DESC'],
      'status' => $status
    ]);
    if ($this->isPost()) {
      $category = new Category();
      if ($category->save()) {
        $this->success('Kategoria została utworzona!');
      } else {
        $this->error('Błąd podczas tworzenia nowej kategorii!');
      }
      return $this->refresh();
    }
    return $this->render('category/list', ['categories' => $categories]);
  }

  public function actionEdit(int $id = 0) {
    AccessControl::onlyForLogged();
    $category = CategoryQuery::getById($id);
    if ($category) {
      if ($this->isPost()) {
        $category->editName();
        if ($category->update()) {
          $this->success('Nazwa kategorii została uaktualniona!');
        } else {
          $this->error('Błąd podczas uaktualniania nazwy kategorii!');
        }
        return $this->executeAction('category/list');
      } else {
        return $this->render('category/edit', ['category' => $category]);
      }
    } else {
      $this->error('Kategoria o ID ' . $id . ' nie istnieje!');
      return $this->executeAction('category/list');
    }

  }

  /**
   * @param $id default 0 if user not provides a param
   */
  public function actionSoftRemove(int $id = 0) {
    AccessControl::onlyForLogged();
    $category = CategoryQuery::getById($id);
    if ($category) {
      $category->softRemove();
      if ($category->update()) {
        $this->success('Kategoria została przeniesiona do usuniętych!');
      } else {
        $this->error('Błąd podczas usuwania kategorii!');
      }
    } else {
      $this->error('Kategoria o ID ' . $id . ' nie istnieje!');
    }
    return $this->executeAction('category/list/removed');
  }

  /**
   * @param $id default 0 if user not provides a param
   */
  public function actionActivation(int $id = 0) {
    AccessControl::onlyForLogged();
    $category = CategoryQuery::getById($id);
    if ($category) {
      $category->activation();
      if ($category->update()) {
        $this->success('Kategoria została aktywowana!');
      } else {
        $this->error('Błąd podczas aktywowania kategorii!');
      }
    } else {
      $this->error('Kategoria o ID ' . $id . ' nie istnieje!');
    }
    return $this->executeAction('category/list');
  }

  /**
   * @param $id default 0 if user not provides a param
   */
  public function actionHide(int $id = 0) {
    AccessControl::onlyForLogged();
    $category = CategoryQuery::getById($id);
    if ($category) {
      $category->hide();
      if ($category->update()) {
        $this->success('Kategoria została ukryta!');
      } else {
        $this->error('Błąd podczas zmiany statusu kategorii na ukrytą!');
      }
    } else {
      $this->error('Kategoria o ID ' . $id . ' nie istnieje!');
    }
    return $this->executeAction('category/list');
  }

  /**
   * Remove category from the database
   * @param $id default 0 if user not provides a param
   */
   public function actionHardRemove(int $id = 0) {
     AccessControl::onlyForLogged();
     $category = CategoryQuery::getById($id);
     if ($category) {
       if ($category->delete()) {
         $this->success('Kategoria została na stałe usujnięta z bazy danych!');
       } else {
         $this->error('Błąd podczas usuwania kategorii!');
       }
     } else {
       $this->error('Kategoria o ID ' . $id . ' nie istnieje!');
     }
     return $this->executeAction('category/list/removed');
   }

}
