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
        'status' => [ArticleHelper::PUBLICATED, ArticleHelper::NOT_PUBLICATED, ArticleHelper::REMOVED, ArticleHelper::SKETCH],
        'category.status' => [CategoryHelper::STATUS_ACTIVE, CategoryHelper::STATUS_HIDDEN]
      ],
      'sort' => [
        'category.name' => 'ASC',
        'title' => 'ASC'
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
}
