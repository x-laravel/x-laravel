<?php

namespace App\Http\DataTables;

abstract class BaseDataTable
{
    public function toJson()
    {
        $query = app()->call([$this, 'query']);

        $dataTable = app()->call([$this, 'dataTable'], ['query' => $query]);

        return $dataTable->toJson();
    }

    public function dataTable(\Illuminate\Database\Eloquent\Builder $query): \Yajra\DataTables\EloquentDataTable
    {
        return datatables()->eloquent($query);
    }
}
