<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;
use App\Controllers\ErrorController;
use App\Models\Article\{
  ArticleTableFiller,
  ArticleQuery,
  Article
};
use App\Models\Category\{
    CategoryQuery,
    Category
};
use App\Components\Helpers\{
  CategoryHelper,
  ArticleHelper
};
use App\Models\User\User;


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

  public function actionView(string $slug = null) {
    $this->checkParams(compact('slug'), 'article/list');
    $article = ArticleQuery::getBySlug($slug);

    if ($article) {
      if (AccessControl::isGuest() && $article->availableForGuest()) {
        $article->incrementViewsNumber();
        return $this->render('article/article', ['article' => $article]);
      } elseif (AccessControl::logged()) {
        return $this->render('article/article', ['article' => $article]);
      }
    }
    return (new ErrorController)->pageNotFound("Pod adresem: <strong>" .
    URL . "article/view/$slug</strong><br>Nie znaleziono takiego artykułu!");
  }

  /**
   * @param string $status represent with articles choose, status names
   * are strictly binded with TableFiller where group names
   */
  public function actionList(string $status = 'active') {
    AccessControl::onlyForLogged();
    $tableFiller = new ArticleTableFiller();

    if (!$tableFiller->setWhereGroup($status)) {
      return (new ErrorController)->pageNotFound();
    }
    $tableFiller->setSortGroup('byAvailableFrom');
    $tableRows = $tableFiller->fetch();

    return $this->render('article/list', ['tableRows' => $tableRows]);
  }

  public function actionEdit(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'article/list');
    $article = ArticleQuery::getById($id);

    if ($article) {
      if ($this->isPost()) {
        $article->loadEditions();
        if ($article->update()) {
          $this->success("Pomyślnie edytowano artykuł pt. \"$article->title\"");
        } else {
          $this->error("Błąd podczas edycji artykułu pt. \"$article->title\"");
        }
        return $this->executeAction('article/list/' . $article->routeParam());
      }
      $categories = CategoryQuery::getAll([
          'sort' => ['name', 'ASC'],
          'status' => [CategoryHelper::STATUS_ACTIVE]
      ]);
    } else {
      $this->error("Arykuł który chcesz edytować - o zadanym ID: $id - nie istnieje!");
      return $this->executeAction('article/list');
    }
    return $this->render('article/form', [
      'categories' => $categories,
      'article' => $article,
      'editMode' => true
    ]);
  }

  public function actionAdd() {
    AccessControl::onlyForLogged();
    $categories = CategoryQuery::getAll([
        'sort' => ['name', 'ASC'],
        'status' => [CategoryHelper::STATUS_ACTIVE]
    ]);
    if ($this->isPost()) {
      $article = new Article();
      if ($article->save()) {
        $this->success("Pomyślnie dodano artykuł \"$article->title\" do bazy danych!");
      } else {
        $this->error("Wystąpił błąd podczas dodawania artykułu \"$article->title\" do bazy danych!");
      }
      return $this->executeAction('article/list/' . $article->routeParam());
    }
    return $this->render('article/form', [
      'categories' => $categories,
      'editMode' => false
    ]);
  }

  public function actionChangeStatus(string $status = null, int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('status', 'id'), 'article/list');
    $article = ArticleQuery::getById($id);

    if ($article) {
      $this->showFlash($article->updateStatus($status));
    } else {
      $this->error("Artykuł o ID: $id nie istnieje!");
    }
    return $this->executeAction('article/list');
  }

  /**
   * Removing permanently article from the database
   */
  public function actionRemove(int $id = null) {
    AccessControl::onlyForLogged();
    $this->checkParams(compact('id'), 'article/list');
    $article = ArticleQuery::getById($id);

    if ($article) {
      if ($article->delete()) {
        $this->success("Artykuł ($article->title) został permanentnie usunięty z bazy danych!");
      } else {
        $this->error("Błąd podczas trwałego usuwania artykułu ($article->title)!");
      }
      return $this->executeAction('article/list/' . $article->routeRemoveParam());
    } else {
      $this->error("Artykuł o ID: $id nie istnieje!");
    }
    return $this->executeAction('article/list');
  }

}
