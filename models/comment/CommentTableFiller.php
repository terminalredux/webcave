<?php
namespace App\Models\Comment;

use Libs\Base\TableFiller\TableFiller;
use App\Models\{
  Article\Article,
  Comment\Comment
};

class CommentTableFiller extends TableFiller
{
  /**
   * Determinate which 'where group' to execute
   */
  private $whereGroup = self::WHERE_GROUP_DEFAULT;

  /**
   * Determinate which 'sort group' to execute
   */
  private $sortGroup = self::SORT_GROUP_DEFAULT;

  /**
   * @inheritdoc
   */
  protected function getSql() : array {
    return [
      'columns' => [
        Comment::tableName() => Comment::getProperties(),  // Base table
        Article::tableName() => Article::getProperties(),
      ],
      'where' => [
        'not-publicated' => [
          'status' => [Comment::NOT_PUBLICATED]
        ],
        'removed' => [
          'status' => [Comment::REMOVED]
        ]
      ],
      'sort' => [
        'theNewest' => [
          'created_at' => 'DESC'
        ],
        'lastUpdated' => [
          'updated_at' => 'DESC'
        ]
      ]
    ];
  }

  /**
   * @inheritdoc
   */
  protected function getTableName() : string {
    return Comment::tableName();
  }

  /**
   * @inheritdoc
   */
  protected function getRelations() : array {
    return Comment::relations();
  }

  /**
   * @inheritdoc
   */
  public function setWhereGroup(string $group) : bool {
    if (array_key_exists($group, $this->getSql()['where']) || $group == self::WHERE_GROUP_DEFAULT) {
      $this->whereGroup = $group;
      switch ($group) {
        case 'not-publicated':
          $this->setSortGroup('theNewest');
          break;
        case 'removed':
          $this->setSortGroup('lastUpdated');
          break;
      }
      return true;
    }
    return false;
  }

  /**
   * @inheritdoc
   */
  public function getWhereGroup() : string {
    return $this->whereGroup;
  }

  /**
   * @inheritdoc
   */
  public function setSortGroup(string $group) : void {
    $this->sortGroup = $group;
  }

  /**
   * @inheritdoc
   */
  public function getSortGroup() : string {
    return $this->sortGroup;
  }
}
