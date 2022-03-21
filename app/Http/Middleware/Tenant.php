<?php

namespace App\Http\Middleware;

use App\Models\System\Tenant as Model;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

class Tenant extends InitializeTenancyByRequestData
{
    protected function getPayload(Request $request): ?string
    {
        $tenantNo = null;
        if (static::$header && $request->hasHeader(static::$header)) {
            $tenantNo = $request->header(static::$header);
        } elseif (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenantNo = $request->get(static::$queryParameter);
        }

        if (!$tenantNo) {
            return null;
        }

        $tenant = Model::where('id', unscramble($tenantNo))->first();
        if ($tenant) {
            return $tenant->uuid;
        }

        return null;
    }
}
