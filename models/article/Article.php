<?php
namespace App\Models\Article;

use App\Models\Category\Category;
use App\Models\BaseCategory\BaseCategory;

class Article extends \ActiveRecord\Model
{
  const UNPUBLICATED = 1;
  const PUBLICATED = 2;
  const REMOVED = 3;
  const SKETCH = 4;

  static $table_name = 'article';

  static $belongs_to  = [
    ['category' ,'class_name' => '\App\Models\Category\Category', 'foreign_key' => 'category_id']
  ];

  static $validates_presence_of  = [
    ['title', 'message' => ': musisz podać tytuł', 'on' => 'create'],
    ['category_id', 'message' => ': musisz podać kategorię', 'on' => 'create'],
    ['content', 'message' => ': musisz dodać zawartość', 'on' => 'create']
  ];
  static $validates_size_of = [
    ['title', 'within' => [10, 255], 'message' => ': nazwa musi mieć od 10 do 255 znaków']
  ];
  static $validates_numericality_of = [
    ['category_id', 'only_integer' => true, 'message' => ': ID musi być liczbą'],
  ];

  public function loadCreate() {
    $this->title = $_POST['title'];
    $this->slug = $this->generateSlug();
    $this->user_id = 1; //TODO only for testing!!!
    if (isset($_POST['category_id'])) {
      $this->category_id = $_POST['category_id'];
    }
    $this->content = $_POST['content'];
    if (isset($_POST['is_sketch'])) {
      $this->status = self::SKETCH;
    } else {
      $this->status = self::UNPUBLICATED;
    }
    if (!$this->isSketch()) {
      if (!empty($_POST['available_from'])) {
        $this->available_from = $_POST['available_from'];
      } else {
        $this->available_from = date('Y-m-d H:i:s', time());
      }
    }
  }

  public static function statusAlias() {
    return [
      1 => 'unpublicated',
      2 => 'publicated',
      3 => 'removed',
      4 => 'sketch'
    ];
  }

  public static function listType() {
    return [
      'unpublicated' => 'niepubliczne',
      'publicated' => 'publiczne',
      'removed' => 'usunięte',
      'sketch' => 'szkice',
      'pending' => 'oczekujące'
    ];
  }

  public static function statusAliasToSet() {
    return [
      1 => 'unpublicated',
      2 => 'publicated',
      3 => 'removed'
    ];
  }

  public function setStatus(string $status) : void {
    $stat = (int) array_flip($this->statusAlias())[$status];
    if ($this->status == self::SKETCH) {
      $this->available_from = date('Y-m-d H:i:s', time());
    }
    $this->status = $stat;
  }

  public function loadEdition() : bool {
    $edited = false;
    if ($this->title != $_POST['title']) {
      $this->title = $_POST['title'];
      $this->slug = $this->generateSlug();
      $edited = true;
    }
    if ($this->content != $_POST['content']) {
      $this->content = $_POST['content'];
      if ($this->status != self::SKETCH) {
        $this->content_edited = date('Y-m-d H:i:s', time());
      }
      $edited = true;
    }
    if (isset($_POST['category_id']) && ($this->category_id != $_POST['category_id'])) {
      $this->category_id = $_POST['category_id'];
      $edited = true;
    }
    if (isset($_POST['available_from']) && ($this->available_from->format('d-m-Y H:m') != $_POST['available_from'])) {
      $this->available_from = $_POST['available_from'];
      $edited = true;
    }


    return $edited;
  }

  public static function statusExistsForRoute(string $status) : bool {
    if (in_array($status, self::statusAlias())) {
      return true;
    }
    return false;
  }

  public static function pendingList(array $articles) : array {
    return [];
  }

  public static function statusExists(string $status) : bool {
    if (in_array($status, self::statusAliasToSet())) {
      return true;
    }
    return false;
  }

  public static function statusToSetExists(string $status) : bool {
    if (in_array($status, self::statusAliasToSet())) {
      return true;
    }
    return false;
  }

  public static function statusByAlias() : array {
    return array_flip(self::statusAlias());
  }

  /**
   * Returns 40 characters
   * version of article's title
   */
  public function shortTitle() : string {
    $title = $this->title;
    $out = strlen($title) > 40 ? substr($title,0,40)."..." : $title;
    return $out;
  }

  private function generateSlug() : string {
    $slug = $this->createSlug();
    if ($this->find_by_sql("SELECT * FROM article WHERE slug='" . $slug . "'")) {
      $i = 1;
      while ($this->find_by_sql("SELECT * FROM article WHERE slug='" . $slug . "-" . $i . "'")) {
        $i++;
      }
      $slug = $slug . '-' . $i;
    }
    return $slug;
  }

  private function createSlug() : string {
    $slug = $this->title;
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
    $slug = preg_replace('~[^-\w]+~', '', $slug);
    $slug = trim($slug, '-');
    $slug = preg_replace('~-+~', '-', $slug);
    $slug = strtolower($slug);
    if (empty($slug)) {
      return 'n-a';
    }
    return $slug;
  }

  public function getStatusName() : string {
    $name = '';
    if ($this->status == self::UNPUBLICATED) {
      $name = 'Nieopublikowany';
    } elseif ($this->status == self::PUBLICATED) {
      $name = 'Publiczny';
    } elseif ($this->status == self::REMOVED) {
      $name = 'Usunięty';
    } elseif ($this->status == self::SKETCH) {
      $name = 'Szkic';
    }
    return $name;
  }

  public function getStatusClass() : string {
    $class = '';
    if ($this->status == self::UNPUBLICATED) {
      $class = 'model-hidden';
    } elseif ($this->status == self::PUBLICATED) {
      $class = 'model-active';
    } elseif ($this->status == self::REMOVED) {
      $class = 'model-removed';
    } elseif ($this->status == self::SKETCH) {
      $class = 'model-sketch';
    }
    return $class;
  }

  public function isPending() {
    $now = new \DateTime();
    $now->format('Y-m-d H:i:s');
    if ($this->available_from > $now) {
      return true;
    }
    return false;
  }

  public function availableForGuest(): bool {
    if (!$this->isPending() && $this->isPublicGlobaly()) {
      return true;
    }
    return false;
  }

  public function isPublicated() {
    if ($this->status == self::PUBLICATED) {
      return true;
    }
    return false;
  }

  public function isUnpublicated() {
    if ($this->status == self::UNPUBLICATED) {
      return true;
    }
    return false;
  }

  public function isRemoved() {
    if ($this->status == self::REMOVED) {
      return true;
    }
    return false;
  }

  public function isSketch() {
    if ($this->status == self::SKETCH) {
      return true;
    }
    return false;
  }

  public function isEdited() : bool {
    if ($this->created_at == $this->updated_at) {
      return false;
    }
    return true;
  }

  public function getStautsAlias() : string {
    $now = new \DateTime();
    $now->format('Y-m-d H:i:s');
    if ($this->isPublicated() && $this->category->isActive() && $this->category->base_category->isActive() && ($this->available_from > $now)) {
      $alias = 'pending';
    } elseif (($this->category->base_category->isHidden() && !$this->category->isRemoved() && !$this->isSketch() && !$this->isRemoved()) ||
             ($this->category->isHidden() && !$this->category->base_category->isRemoved() && !$this->isSketch() && !$this->isRemoved()) ||
             $this->isUnpublicated() && !$this->category->isRemoved() && !$this->category->base_category->isRemoved()) {
      $alias = 'unpublicated';
    } elseif (($this->isRemoved() || $this->category->isRemoved() || $this->category->base_category->isRemoved()) && !$this->isSketch()) {
      $alias = 'removed';
    } elseif ($this->isSketch()) {
      $alias = 'sketch';
    } else {
      $alias = 'publicated';
    }
    return $alias;
  }

  /**
   * Checks if article's category &
   * base category  is active.
   */
  public function isPublicGlobaly() : bool {
    if ($this->isPublicated() && $this->category->isActiveGlobaly()) {
      return true;
    }
    return false;
  }

  /**
   * For article list
   */
  public function isUnpublicatedGlobaly() : bool {
    if (($this->isUnpublicated() && !$this->category->base_category->isRemoved() && !$this->category->isRemoved()) ||
    ($this->category->isHidden() && !$this->isSketch() && !$this->isRemoved()) ||
    $this->category->base_category->isHidden()) {
      return true;
    }
    return false;
  }

  /**
   * Prepare list depends on
   * provided slug
   */
   public static function prepareList(string $slug) : array {
     $finalList = [];
     $articles = self::all();
     if ($slug == 'publicated') {
       foreach ($articles as $article) {
         if ($article->isPublicGlobaly() && !$article->isPending()) {
           $finalList[] = $article;
         }
       }
     } elseif ($slug == 'unpublicated') {
       foreach ($articles as $article) {
         if ($article->isUnpublicatedGlobaly()) {
           $finalList[] = $article;
         }
       }
     } elseif ($slug == 'sketch') {
       foreach ($articles as $article) {
         if ($article->isSketch()) {
           $finalList[] = $article;
         }
       }
     } elseif ($slug == 'pending') {
       foreach ($articles as $article) {
         if ($article->isPublicated() && $article->isPending()) {
           $finalList[] = $article;
         }
       }
     } elseif ($slug == 'removed') {
       foreach ($articles as $article) {
         if ($article->status == self::REMOVED || $article->category->status == Category::REMOVED || $article->category->base_category->status == BaseCategory::REMOVED){
           $finalList[] = $article;
         }
       }
     }



     return $finalList;
   }


}
