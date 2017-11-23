<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\{
    BaseCategory\BaseCategory,
    Category\Category
};


class CategoryController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    AccessControl::onlyForLogged();
    return $this->executeAction('category/list');
  }

  public function actionList(string $status = 'active') {
    AccessControl::onlyForLogged();
    $status = Category::statusExists($status) ? $status : 'active';

    $baseCategories = BaseCategory::all([
      'order' => 'name ASC',
      'conditions' => ['status' => [BaseCategory::ACTIVE, BaseCategory::HIDDEN]]
    ]);

    $join = 'INNER JOIN base_category bc ON(category.base_category_id = bc.id)';
    $sel = 'category.*, bc.status as base_category_status';
    $categories = Category::all([
      'joins' => $join,
      'select' => $sel,
      'order' => 'updated_at DESC',
    ]);

    $categoryList = Category::availableCategorires($status, $categories);

    return $this->render('category/list', [
      'baseCategories' => $baseCategories,
      'categories' => $categoryList,
      'editMode' => false,
      'title' => Category::getStatusPrular()[$status]
    ]);
  }

  public function actionEdit(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'category/list');
    $category = $this->findModel($id);

    $baseCategories = BaseCategory::all([
      'order' => 'name ASC',
      'conditions' => ['status' => [BaseCategory::ACTIVE, BaseCategory::HIDDEN]]
    ]);

    if ($this->isPost()) {
      $category->loadEdition();
      if ($category->save()) {
        $this->success("Pomyślnie edytowano katogorie $category->name!");
      } else {
        $this->error(implode('<br>', $category->errors->full_messages()));
      }
      return $this->executeAction('category/list/' . Category::statusAlias()[$category->status]);
    }

    return $this->render('category/form', [
      'baseCategories' => $baseCategories,
      'model' => $category,
      'editMode' => true
    ]);
  }

  public function actionAdd() {
    AccessControl::onlyForLogged();

    if ($this->isPost()) {
      $model = new Category();
      $model->loadCreate();
      if ($model->save()) {
        $this->success("Utworzono nową kategorię $model->name!");
      } else {
        $this->error(implode('<br>', $model->errors->full_messages()));
      }
    }
    return $this->executeAction('category/list');
  }

  public function actionChangeStatus(int $id = null, string $status = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id', 'status'), 'category/index');

    if (Category::statusExists($status)) {
      $category = $this->findModel($id);
      $category->setStatus($status);
      if ($category->save()) {
        $this->success("Pomślnie zmieniono status kategorii $category->name!");
      } else {
        $this->error(implode('<br>', $category->errors->full_messages()));
      }
    } else {
      $this->error("Zadany status: $status nie istnieje!");
    }
    $param = $category->chooseParam();
    return $this->executeAction('category/list/' . $param);
  }

  public function actionDelete(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'category/list/removed');

    $category = $this->findModel($id);
    if ($category->delete()) {
      $this->success("Pomyślnie usunięto kategorę $category->name z bazy danych!");
    } else {
      $this->error(implode('<br>', $category->errors->full_messages()));
    }
    return $this->executeAction('category/list/removed');
  }

  /**
   * group is alias for base-category
   */
  public function actionMain(string $slug = null) {
    $this->checkParams(compact('slug'), 'article/list');  // TODO to change
    $baseCategory = BaseCategory::find_by_sql("SELECT * FROM base_category WHERE slug='" . $slug . "'");

    if (!$baseCategory || !$baseCategory[0]->isActive()) {
      return (new ErrorController)->pageNotFound("Nie znaleniono artykułów z tej kategorii głównej!");
    }
    $categories = $baseCategory[0]->categories;
    return $this->render('category/base-category-list', [
      'categories' => $categories
    ]);
  }

  public function actionAll() {
    $baseCategories = BaseCategory::all();
    return $this->render('category/all', ['list' => $baseCategories]);
  }

  private function findModel(int $id) {
    if (Category::exists($id)) {
      return Category::find($id);
    } else {
      $this->error("Model o zadanym ID: $id nie istnieje!");
      return $this->executeAction('category/list');
    }
  }

}
