<?php
namespace Libs\Base\TableFiller;

use Libs\Database\DbConnection;

abstract class TableFiller
{
  /**
   * Returns table name
   */
  protected abstract function getTableName() : string;

  /**
   * Returns table relations with other tables
   */
  protected abstract function getRelations() : array;

  /**
   * Gets SQL params like: columns
   *                       sort
   */
  protected abstract function getSql() : array;

  /**
   * Gets rows from table that depends
   * on provided SQL. First table in
   * array columns is the base table
   */
  public function fetch() : ? array {
    $sql = $this->prepareSql();

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare($sql);
      $statement->execute();
      $rows = $statement->fetchAll();

      $data = [];
      if ($rows) {
        foreach($rows as $row) {
          $data[] = new class($row) {
            public function __construct(array $row) {
              foreach ($row as $key => $value) {
                $this->$key = $value;
              }
            }
          };
        }
      }
      $db = null;
      return $data;
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  /**
   * @return string SQL
   */
  private function prepareSql() : string {
    $sql  = $this->addJoins();
    $sql .= $this->addWhere();
    $sql .= $this->addOrder();
    return $sql;
  }

  /**
   * Init SQL: add columns and inner joins
   * For the columns from the outside the base table
   * adds aliases depends on thier table name prefix like: "user_id"
   * for the id column from the user table
   */
  private function addJoins() : string {
    $columns = [];
    $joinedTables = [];

    foreach ($this->getSql()['columns'] as $tableName => $tableColumns) {
      foreach ($tableColumns as $column) {
        if ($tableName == $this->getTableName()) {
          $columns[] = $tableName . '.' . $column;
        } else {
          $columns[] = $tableName . '.' . $column . ' AS ' . $tableName . '_' . $column;
        }
      }
      if ($tableName != $this->getTableName()) {
        $joinedTables[] = $tableName;
      }
    }

    $selectColumns = implode(', ', $columns);
    $sql = 'SELECT ' . $selectColumns . ' FROM ' . $this->getTableName();

    $relations = $this->getRelations();

    foreach ($joinedTables as $table) {
      if (isset($relations[$table])) {
        $sql .= ' INNER JOIN ' . $table . ' ON ' . $this->getTableName() . '.' . $relations[$table]['column'] . ' = ' . $table . '.' . $relations[$table]['related_column'];
      }
    }
    return $sql;
  }

  /**
   * Sets WHERE
   */
  private function addWhere() : string {
    $sql = '';
    $firstWhere = true;
    foreach ($this->getSql()['where'] as $column => $values) {
      if (!strpos($column, '.')) {
        $column = $this->getTableName() . '.' . $column;
      }
      $whereValues = [];
      foreach ($values as $value) {
        $whereValues[] = $value;
      }

      if ($firstWhere) {
        $sql .= ' WHERE ';
      } else {
        $sql .= ' AND ';
      }
      $sql .= $column . ' IN (' . implode(',', $whereValues) . ')';
      $firstWhere = false;
    }
    return $sql;
  }

  /**
   * Sets ORDER BY
   */
  private function addOrder() : string {
    $columnsToOrder = [];
    foreach ($this->getSql()['sort'] as $column => $value) {
      if (!strpos($column, '.')) {
        $column = $this->getTableName() . '.' . $column;
      }
      $columnsToOrder[] = $column . ' ' . $value;
    }
    $sql = ' ORDER BY ' . implode(', ', $columnsToOrder);
    return $sql;
  }

}
