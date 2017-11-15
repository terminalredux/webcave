<?php
namespace App\Models\Comment;

use Libs\Base\Model;
use App\Models\Article\Article;

class Comment extends Model
{
  public $id;
  public $article_id;
  public $nick;
  public $email;
  public $content;
  public $ip;
  public $status;
  public $created_at;
  public $updated_at;

  const NOT_PUBLICATED = 1;
  const PUBLICATED = 2;
  const REMOVED = 3;
  const ADMIN = 4;

  /**
   * @inheritdoc
   */
  public static function tableName() : string {
    return "comment";
  }

  /**
   * @inheritdoc
   */
  public static function relations() : ? array {
    return null;
    /*
    return [
      'article' => [                        // Relation name
        'has' => 'one',
        'column' => 'article_id',
        'joined-table-column' => 'id',
        'model' => Article::className()
      ]
    ];
    */
  }

  /**
   * @inheritdoc
   */
  public function getForm() : void {
    $this->nick = $_POST['nick'];
    $this->email = $_POST['email'];
    $this->content = $_POST['content'];
    $this->ip = $_SERVER['REMOTE_ADDR'];
    $this->status = self::NOT_PUBLICATED;
    $this->created_at = time();
    $this->updated_at = time();
  }
}
