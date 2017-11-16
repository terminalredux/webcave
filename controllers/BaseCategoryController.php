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

  public function actionList(string $status = 'active') {
    AccessControl::onlyForLogged();
    $status = BaseCategory::statusExists($status) ? $status : 'active';
    $list = BaseCategory::all([
      'order' => 'updated_at desc',
      'conditions' => ['status' => BaseCategory::getStatusByAlias($status)]
    ]);
    return $this->render('basecategory/list', [
      'list' => $list,
      'editMode' => false,
      'title' => BaseCategory::getStatusPrular()[$status]
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
    $param = $baseCategory ? BaseCategory::statusAlias()[$baseCategory->status] : 'active';
    return $this->executeAction('basecategory/list/' . $param);
  }

  public function actionEdit(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'basecategory/list');
    $baseCategory = $this->findModel($id);

    if ($this->isPost()) {
      $baseCategory->loadEdition();
      if ($baseCategory->save()) {
        $this->success("Pomyślnie edytowano katogorie bazową $baseCategory->name!");
      } else {
        $this->error(implode('<br>', $baseCategory->errors->full_messages()));
      }
      return $this->executeAction('basecategory/list/' . BaseCategory::statusAlias()[$baseCategory->status]);
    }

    return $this->render('basecategory/form', [
      'model' => $baseCategory,
      'editMode' => true
    ]);
  }

  public function actionDelete(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'basecategory/list/removed');

    $baseCategory = $this->findModel($id);
    if ($baseCategory->delete()) {
      $this->success("Kategorę bazową $baseCategory->name pomyślnie usunięto z bazy danych!");
    } else {
      $this->error("Błąd podczas usuwania kategorii bazowej $baseCategory->name z bazy danych!");
    }
    return $this->executeAction('basecategory/list/removed');
  }

  public function actionAdd() {
    AccessControl::onlyForLogged();
    if ($this->isPost()) {
      $model = new BaseCategory();
      $model->loadCreate();
      if ($model->save()) {
        $this->success("Utworzono kategorie bazową $model->name");
      } else {
        $this->error(implode('<br>', $model->errors->full_messages()));
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
