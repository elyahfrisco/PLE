<?php

namespace App\Models\Traits;

trait EloquentExtend
{

  public function scopeWhereValueNotNullAndLTETo($query, $column_name, $request_value = null)
  {
    /**less than or equal  */

    if (is_array($column_name)) {
      foreach ($column_name as $column => $value) {
        if ($value)
          $query->where($column, '<=', $value);
      }
    } elseif ($request_value)
      $query->where($column_name, '<=', $request_value);
  }

  public function scopeWhereValueNotNullAndGTETo($query, $column_name, $request_value = null)
  {
    /**greater than or equal  */

    if (is_array($column_name)) {
      foreach ($column_name as $column => $value) {
        if ($value)
          $query->where($column, '>=', $value);
      }
    } elseif ($request_value)
      $query->where($column_name, '>=', $request_value);
  }

  public function scopeWhereValueNotNullAndEqualTo($query, $column_name, $request_value = null)
  {
    if (is_array($column_name)) {
      foreach ($column_name as $column => $value) {
        if ($value)
          $query->where($column, $value);
      }
    } elseif ($request_value)
      $query->where($column_name, $request_value);
  }

  public function scopeIncludeScopeWhereValueNotNull($query, $scope_name, $request_value = null)
  {
    if (is_array($scope_name)) {
      foreach ($scope_name as $scope => $value) {
        if ($value)
          $query->$scope();
      }
    } elseif ($request_value)
      $query->$scope_name();
  }

  public function scopeWhereValueIsNumericAndEqualTo($query, $column_name, $request_value)
  {
    if (is_numeric($request_value))
      $query->where($column_name, $request_value);
  }

  public function scopeWhereDiff($query, $column_name, $value)
  {
    $query->where($column_name, '<>', $value);
  }
}
