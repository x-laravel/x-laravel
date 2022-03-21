<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\TenantCollection;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, LogsActivity;

    protected $connection = 'system';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'name',
        ];
    }

    static function findById($id)
    {
        return static::where('id', $id)->first();
    }

    public function getTenantKeyName(): string
    {
        return 'uuid';
    }

    public function newCollection(array $models = []): TenantCollection
    {
        return new TenantCollection($models);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->getConnectionName())
            ->logAll()
            ->logOnlyDirty();
    }
}
