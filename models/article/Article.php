<?php
namespace App\Models\Article;

use Libs\Database\DbConnection;
use Libs\Base\Model;
use Libs\FlashMessage\FlashConf;
use App\Components\Helpers\ArticleHelper;
use App\Models\Category\Category;
use App\Models\User\User;
use DateTime;

class Article extends Model
{
  public $id;
  public $category_id;
  public $user_id;
  public $title;
  public $slug;
  public $content;
  public $available_from;
  public $status;
  public $created_at;
  public $updated_at;

  /**
   * @inheritdoc
   */
  public static function tableName() : string {
    return "article";
  }

  /**
   * @inheritdoc
   */
  public static function relations() : ? array {
     return [
       Category::tableName() => [
         'column' => 'category_id',
         'related_column' => 'id'
       ],
       User::tableName() => [
         'column' => 'user_id',
         'related_column' => 'id'
       ]
     ];
   }

  /**
   * @inheritdoc
   */
  public function getForm() : void {
    $this->title = $_POST['title'];
    $this->category_id = $_POST['category_id'];
    $this->user_id = 1;
    $this->content = $_POST['content'];
    $this->slug = $this->checkSlug($this->createSlug());
    $this->available_from = $this->getAvailableFrom();
    $this->status = $this->getStatus();
    $this->created_at = time();
    $this->updated_at = time();
  }

  private function createSlug() : string {
    $text = $this->title;
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
      return 'n-a';
    }
    return $text;
  }

  /**
   * If article is available
   * in the futrue returns true
   */
  public function isPending() : bool {
    $pending = false;
    if ($this->available_from > time()) {
      $pending = true;
    }
    return $pending;
  }

  private function getAvailableFrom() : int {
    if (empty($_POST['available_from'])) {
      $result = time();
    } else {
      $result = strtotime($_POST['available_from']);
    }
    return $result;
  }

  private function getStatus() : int {
    if (isset($_POST['is_sketch'])) {
      $status = ArticleHelper::SKETCH;
    } else {
      $status = ArticleHelper::NOT_PUBLICATED;
    }
    return $status;
  }

  /**
   * Returns 30 characters
   * version of article's title
   */
  public function shortTitle() : string {
    $title = $this->title;
    $out = strlen($title) > 40 ? substr($title,0,40)."..." : $title;
    return $out;
  }

  /*
   * Created for article table, sets
   * class thats decorated the status info
   */
  public function statusClass() : string {
    if ($this->status == ArticleHelper::NOT_PUBLICATED) {
      $class = 'model-hidden';
    } elseif ($this->status == ArticleHelper::PUBLICATED) {
      $class = 'model-active';
    } elseif ($this->status == ArticleHelper::REMOVED) {
      $class = 'model-removed';
    } elseif ($this->status == ArticleHelper::SKETCH) {
      $class = 'model-sketch';
    }
    return $class;
  }

  public function isRemoved() : bool {
    if ($this->status == ArticleHelper::REMOVED) {
      return true;
    }
    return false;
  }

  public function isEdited() : bool {
    if ($this->created_at < $this->updated_at) {
      return true;
    }
    return false;
  }

  /**
   * Changes article status depends on passed param
   * @param string $status
   * @param array $message has two assoc array: content & type
   * @return FlashConf
   */
  public function updateStatus(string $status) : FlashConf {
    $flashConf = new FlashConf();
    $availableStatus = true;

    if ($status == ArticleHelper::STATUS_PUBLIC) {
      $this->status = ArticleHelper::PUBLICATED;
    } elseif ($status == ArticleHelper::STATUS_NOT_PUBLIC) {
      $this->status = ArticleHelper::NOT_PUBLICATED;
    } elseif ($status == ArticleHelper::STATUS_REMOVED) {
      $this->status = ArticleHelper::REMOVED;
    } else {
      $flashConf->set('error', "Błędny status: $status");
      $availableStatus = false;
    }

    if ($availableStatus) {
      if ($this->update(false)) {
        $flashConf->set('success', "Pomyślnie zmieniono status artukułu ($this->title) na: " . ArticleHelper::getStatus()[$this->status]);
      } else {
        $flashConf->set('error', "Błąd podczas zmiany statusu artykułu ($this->title)!");
      }
    }
    return $flashConf;
  }

  /**
   * If slug alredy exists in table,
   * add number in the slug's end
   * @param string $slug
   * @return string $slug
   */
  private function checkSlug(string $slug) : string {
    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM article WHERE slug = '" . $slug . "'");
      $statement->execute();
      $rows = $statement->fetchAll();

      if (count($rows)) {
        $process = true;
        $i = 1;
        while ($process) {
            $statement = $db->prepare("SELECT * FROM article WHERE slug = '" . $slug . "-" . $i . "'");
            $statement->execute();
            $rows = $statement->fetchAll();
            if (count($rows)) {
              $i++;
            } else {
              $slug .= "-$i";
              $process = false;
              $db = null;
            }
        }
      }
    } catch (PDOException $e) {
      var_dump('Wyjątek PDO - ARTICLE - CHECKSLUG()');die;
    }
    return $slug;
  }


}