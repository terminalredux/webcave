<?php
namespace Libs\Formatters;

class DateTimeFormatter
{
  /**
   * Returns date time like 31/12/2017 12:45 or different
   * @param int $timestamp
   * @param bool $fullDateTime if set to true show full format
   */
  public function dateTime(int $timestamp, bool $fullDateTime = false) : string {
    if ($fullDateTime) {
      return $this->default($timestamp);
    }

    if ($this->differentYear($timestamp)) {
      return $this->default($timestamp);
    }

    return date('d/m H:i', $timestamp);
  }

  /**
   * Returns default datetiem format, e.g. 31/12/2017 12:45
   */
  private function default($timestamp) : string {
    return date('d/m/Y H:i', $timestamp);
  }

  /**
   * If year from the timestamp is diffrent in compare
   * to the current year, returns true
   * @param int $timestamp
   */
  private function differentYear(int $timestamp) : bool {
    $time = time();
    $result = true;
    if (date('Y', $time) == date('Y', $timestamp)) {
      $result = false;
    }
    return $result;
  }
}
