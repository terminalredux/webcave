<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\BaseCategory\BaseCategory;

class BaseCategoryController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    AccessControl::onlyForLogged();
    return $this->executeAction('basecategory/list');
  }

  public function actionList() {
    AccessControl::onlyForLogged();
    $list = BaseCategory::all();
    return $this->render('basecategory/list', [
      'list' => $list,
      'editMode' => false
    ]);
  }

  public function actionChangeStatus(int $id = null, string $status = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id', 'status'), 'basecategory/index');

    if (BaseCategory::statusExists($status)) {
      $baseCategory = $this->findModel($id);
      $baseCategory->setStatus($status);
      if ($baseCategory->save()) {
        $this->success("Pomślnie zmieniono status kategorii bazowej $baseCategory->name!");
      } else {
        $this->error("Błąd podczas zmiany status kategorii bazowej $baseCategory->name!");
      }
    } else {
      $this->error("Zadany status: $status nie istnieje!");
    }
    return $this->executeAction('basecategory/list');
  }

  public function actionEdit(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'basecategory/index');
    $baseCategory = $this->findModel($id);

    if ($this->isPost()) {
      $baseCategory->loadEdition();
      if ($baseCategory->save()) {
        $this->success("Pomyślnie edytowano katogorie bazową $baseCategory->name!");
      } else {
        $this->error("Błąd podczas edycji kategorii bazowej $baseCategory->name!");
      }
      return $this->executeAction('basecategory/list');
    }

    return $this->render('basecategory/form', [
      'model' => $baseCategory,
      'editMode' => true
    ]);
  }

  public function actionDelete(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'basecategory/index');

    $baseCategory = $this->findModel($id);
    if ($baseCategory->delete()) {
      $this->success("Kategorę bazową $baseCategory->name pomyślnie usunięto z bazy danych!");
    } else {
      $this->error("Błąd podczas usuwania kategorii bazowej $baseCategory->name z bazy danych!");
    }
    return $this->executeAction('basecategory/list');
  }

  public function actionAdd() {
    AccessControl::onlyForLogged();
    if ($this->isPost()) {
      $model = new BaseCategory();
      $model->loadCreate();
      if ($model->save()) {
        $this->success("Utworzono kategorie bazową $model->name");
      } else {
        $this->error("Błąd podczas tworzenia kategorii bazowej $model->name");
      }
    }
    return $this->executeAction('basecategory/list');
  }

  private function findModel(int $id) {
    if (BaseCategory::exists($id)) {
      return BaseCategory::find($id);
    } else {
      $this->error("Model o zadanym ID: $id nie istnieje!");
      return $this->executeAction('basecategory/list');
    }
  }

}
