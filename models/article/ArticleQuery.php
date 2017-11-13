<?php
namespace App\Models\Article;

use App\Components\Helpers\ArticleHelper;
use App\Models\Article\Article;
use App\Models\Category\Category;
use Libs\Base\Query;
use Libs\Database\DbConnection;
use PDO;

class ArticleQuery extends Query
{
  const FLAG_LIST_ACTIVE = 'active';
  const FLAG_LIST_PUBLIC = 'public';
  const FLAG_LIST_NOT_PUBLIC = 'not-public';
  const FLAG_LIST_REMOVED = 'removed';
  const FLAG_LIST_PENDING = 'pending';

  /**
   * @inheritdoc
   */
  public static function getTableName() : string {
    return Article::tableName();
  }

  /**
   * @inheritdoc
   */
  public static function mapping(array $row) {
    $article = new Article();
    $article->id = $row['id'];
    $article->category_id = $row['category_id'];
    $article->title = $row['title'];
    $article->slug = $row['slug'];
    $article->user_id = $row['user_id'];
    $article->content = $row['content'];
    $article->views = $row['views'];
    $article->available_from = $row['available_from'];
    $article->status = $row['status'];
    $article->created_at = $row['created_at'];
    $article->updated_at = $row['updated_at'];
    return $article;
  }

  public static function getBySlug(string $slug) : ? Article {
    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . static::getTableName() . " WHERE slug = :slug");
      $statement->bindParam(':slug', $slug, PDO::PARAM_STR);
      $statement->execute();
      $row = $statement->fetch();
      $result = null;
      if ($row) {
        $result = static::mapping($row);
      }
      $db = null;
      return $result;
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }
}
