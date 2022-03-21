<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'auth';
    protected string $table = 'oauth_clients';

    protected array $columns = ['id', 'user_id', 'name', 'secret', 'provider', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'created_at'];
    protected array $data = [
        ['8405ad7a-2fb9-4a43-b5c2-e583d02de966', null, 'Example Personal Access Client', 'zaNzb67PclLJ2nRfgkckoUfvJbIeSYJPqwIzhIHv', null, 'http://localhost', 1, 0, 0, '2016-01-01 00:00:00'],
        ['95d82d18-b1bc-413f-9786-16e04742ee23', null, 'Example Admin Password Grant Client', '4KvPYVU8k0iRnYHjAl9TvHmwX3axg4boFrXSrTXk', 'admins', 'http://localhost', 0, 1, 0, '2016-01-01 00:00:01'],
        ['8405ad7a-3db6-458b-9c2a-008bd8d6f5f6', null, 'Example User Password Grant Client', '0MmKCuHM4DutaurlbxJTbV3dEb30Zo9NdFdkJwM4', 'users', 'http://localhost', 0, 1, 0, '2016-01-01 00:00:01'],
    ];


    public function up(): void
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
        });

        $this->seed();
    }

    protected function seed(): void
    {
        $chunks = collect($this->data)->map(function ($item) {
            return collect($this->columns)->combine($item)->toArray();
        })->chunk(1000);

        foreach ($chunks as $chunk) {
            DB::connection($this->connection)->table($this->table)->insert($chunk->toArray());
        }
    }


    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists($this->table);
    }
};
