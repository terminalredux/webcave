<?php
namespace App\Models\Article;

use Libs\Base\TableFiller\TableFiller;
use App\Models\{
  Article\Article,
  Category\Category,
  User\User
};
use App\Components\Helpers\{
  ArticleHelper,
  CategoryHelper
};

class ArticleTableFiller extends TableFiller
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
        Article::tableName() => Article::getProperties(),  // Base table
        Category::tableName() => Category::getProperties(),
        User::tableName() => User::getProperties()
      ],
      'where' => [
        'active' => [
          'status' => [ArticleHelper::PUBLICATED, ArticleHelper::NOT_PUBLICATED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN, CategoryHelper::STATUS_REMOVED]
        ],
        'removed' => [
          'status' => [ArticleHelper::REMOVED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN, CategoryHelper::STATUS_REMOVED]
        ],
        'sketch' => [
          'status' => [ArticleHelper::SKETCH],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN, CategoryHelper::STATUS_REMOVED]
        ],
        'publicated' => [
          'status' => [ArticleHelper::PUBLICATED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN, CategoryHelper::STATUS_REMOVED]
        ],
        'notpublicated' => [
          'status' => [ArticleHelper::NOT_PUBLICATED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN, CategoryHelper::STATUS_REMOVED]
        ],
      ],
      'sort' => [
        'byAvailableFrom' => [
          'available_from' => 'DESC'
        ],
        'byCategoryName' => [
          'category.name' => 'ASC',
          'title' => 'ASC'
        ],
        'theOldest' => [
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
    return Article::tableName();
  }

  /**
   * @inheritdoc
   */
  protected function getRelations() : array {
    return Article::relations();
  }

  /**
   * @inheritdoc
   */
  public function setWhereGroup(string $group) : bool {
    if (array_key_exists($group, $this->getSql()['where']) || $group == self::WHERE_GROUP_DEFAULT) {
      $this->whereGroup = $group;
      switch ($group) {
        case 'sketch':
          $this->setSortGroup('lastUpdated');
          break;
        case 'active' || 'publicated' || 'notpublicated':
          $this->setSortGroup('byAvailableFrom');
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
