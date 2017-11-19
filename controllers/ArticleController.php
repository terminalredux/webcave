<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Models\Article\Article;
use App\Models\Category\Category;

class ArticleController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    parent::__construct();
  }

  public function actionIndex() {
    AccessControl::onlyForLogged();
    return $this->executeAction('article/list');
  }

  public function actionList(string $slug = 'publicated') {
    AccessControl::onlyForLogged();

    if ($slug == 'pending') {
      $status = Article::PUBLICATED;
      $order = 'available_from DESC';
    } elseif (Article::statusExistsForRoute($slug)) {
      $status = Article::statusByAlias()[$slug];
      $order = 'updated_at DESC';
    } else {
      return (new ErrorController)->pageNotFound("Nie znaleziono!");
    }

    $articles = Article::all([
      'order' => $order,
      'conditions' => ['status' => $status]
    ]);
    if ($slug == 'pending') {
      //$articles = Article::pendingList($articles);
    }

    return $this->render('article/list', [
      'articles' => $articles
    ]);
  }

  public function actionView(string $slug = null) {
    $this->checkParams(compact('id'), 'article/list');
    $article = Article::find_by_sql("SELECT * FROM article WHERE slug='" . $slug . "'");

    if (!$article) {
      return (new ErrorController)->pageNotFound("Nie znaleniono artykułu!");
    }

    return $this->render('article/article', ['article' => $article[0]]);
  }

  public function actionChangeStatus(int $id = null, string $status = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id', 'status'), 'article/index');

    if (Article::statusToSetExists($status)) {
      $article = $this->findModel($id);
      $article->setStatus($status);
      if ($article->save()) {
        $this->success("Pomślnie zmieniono status artykułu $article->title!");
      } else {
        $this->error(implode('<br>', $article->errors->full_messages()));
      }
    } else {
      $this->error("Zadany status: $status nie istnieje!");
    }
    //$param = $category->chooseParam();
    return $this->executeAction('article/list/');
  }

  public function actionAdd() {
    AccessControl::onlyForLogged();
    $join = 'INNER JOIN base_category bc ON(category.base_category_id = bc.id)';
    $sel = 'category.*, bc.status as base_category_status';
    $categories = Category::all([
      'joins' => $join,
      'select' => $sel,
      'order' => 'name ASC'
    ]);
    $categories = Category::getActiveCategory($categories);

    if ($this->isPost()) {
      $article = new Article();
      $article->loadCreate();
      $param = '';
      if ($article->save()) {
        $param = $article->isSketch() ? 'sketch' : 'unpublicated';
        $this->success('Pomyślnie utworzono nowy artykuł!');
      } else {
        $this->error(implode('<br>', $article->errors->full_messages()));
      }
      $this->executeAction('article/list/' . $param);
    }

    return $this->render('article/form', [
      'categories' => $categories,
      'editMode' => false
    ]);
  }

  public function actionEdit(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'article/list');
    $article = $this->findModel($id);

    $join = 'INNER JOIN base_category bc ON(category.base_category_id = bc.id)';
    $sel = 'category.*, bc.status as base_category_status';
    $categories = Category::all([
      'joins' => $join,
      'select' => $sel,
      'order' => 'name ASC'
    ]);
    $categories = Category::getActiveCategory($categories);

    if ($this->isPost()) {
      if ($article->loadEdition()) {
        if ($article->save()) {
          $this->success("Pomyślnie edytowano zawartość artykułu $article->title!");
        } else {
          $this->error(implode('<br>', $article->errors->full_messages()));
        }
      } else {
        $this->success("Nie trzeba było niczego zmieniać!");
      }
      return $this->executeAction('article/list');
    }

    return $this->render('article\form', [
      'article' => $article,
      'editMode' => true,
      'categories' => $categories
    ]);
  }

  public function actionDelete(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'article/list/removed');

    $article = $this->findModel($id);
    if ($article->delete()) {
      $this->success("Pomyślnie usunięto artykuł $article->title z bazy danych!");
    } else {
      $this->error(implode('<br>', $article->errors->full_messages()));
    }
    return $this->executeAction('article/list/removed');
  }

  private function findModel(int $id) {
    if (Article::exists($id)) {
      return Article::find($id);
    } else {
      $this->error("Model o zadanym ID: $id nie istnieje!");
      return $this->executeAction('article/list');
    }
  }

}
