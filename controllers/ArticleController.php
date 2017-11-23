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
      if ($slug != 'pending' && !Article::statusExistsForRoute($slug)) {
        return (new ErrorController)->pageNotFound("Nie znaleziono!");
      }
      $articles = Article::prepareList($slug);
      return $this->render('article/list', [
        'articles' => $articles,
        'listType' => Article::listType()[$slug]
      ]);

  }

  public function actionView(string $slug = null) {
    $this->checkParams(compact('id'), 'article/list');
    $article = Article::find_by_sql("SELECT * FROM article WHERE slug='" . $slug . "'");
    if (!$article) {
      return (new ErrorController)->pageNotFound("Nie znaleniono artykułu!");
    }
    if (AccessControl::isGuest()) {
      if ($article[0]->availableForGuest()) {
        return $this->render('article/article', ['article' => $article[0]]);
      } else {
        return (new ErrorController)->pageNotFound("Nie znaleniono artykułu!");
      }
    } else {
      AccessControl::onlyForLogged();
      return $this->render('article/article', ['article' => $article[0]]);
    }
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
    $param = $article->getStautsAlias();
    return $this->executeAction('article/list/' . $param);
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
      $param = $article->getStautsAlias();
      return $this->executeAction('article/list/' . $param);
    }

    return $this->render('article/form', [
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

  public function actionCategory(string $slug = null) {
    $this->checkParams(compact('slug'), 'article/list');  // TODO to change
    $category = Category::find_by_sql("SELECT * FROM category WHERE slug='" . $slug . "'");
    if (!$category || (!$category[0]->base_category->isActive() || !$category[0]->isActive())) {
      return (new ErrorController)->pageNotFound("Nie znaleniono artykułów z tej kategorii!");
    }
    $articles = Article::all([
      'order' => 'available_from DESC',
      'conditions' => ['category_id' => $category[0]->id]
    ]);
    $list = [];
    foreach ($articles as $article) {
      if ($article->availableForGuest())
        $list[] = $article;
    }

    return $this->render('article/public-list', [
      'articles' => $list,
      'title' => $category[0]->base_category->name . ' - ' .  $category[0]->name,
    ]);
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
