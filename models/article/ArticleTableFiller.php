<?php
namespace App\Models\Article;

use Libs\Base\TableFiller;
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
        Article::tableName() => [  // Base table
          'id',
          'title',
          'content',
          'slug',
          'status',
          'available_from',
          'created_at',
          'updated_at'
        ],
        Category::tableName() => [
          'id',
          'name',
          'status'
        ],
        User::tableName() => [
          'id',
          'username',
          'email',
          'alias'
        ]
      ],
      'where' => [
        'status' => [ArticleHelper::PUBLICATED, ArticleHelper::NOT_PUBLICATED],
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
