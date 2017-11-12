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
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
        ],
        'removed' => [
          'status' => [ArticleHelper::REMOVED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
        ],
        'sketch' => [
          'status' => [ArticleHelper::SKETCH],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
        ],
        'publicated' => [
          'status' => [ArticleHelper::PUBLICATED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
        ],
        'notpublicated' => [
          'status' => [ArticleHelper::NOT_PUBLICATED],
          'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
        ],
      ],
      'sort' => [
        'byCategoryName' => [
          'category.name' => 'ASC',
          'title' => 'ASC'
        ],
        'byAvailableFrom' => [
          'available_from' => 'DESC'
        ],
        'theOldest' => [
          'created_at' => 'DESC'
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
  public function setWhereGroup(string $group) : void {
    $this->whereGroup = $group;
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
