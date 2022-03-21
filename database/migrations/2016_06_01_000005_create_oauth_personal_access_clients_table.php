<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'auth';
    protected string $table = 'oauth_personal_access_clients';

    protected array $columns = ['id', 'client_id', 'created_at'];
    protected array $data = [
        [1, '8405ad7a-2fb9-4a43-b5c2-e583d02de966', '2016-01-01 00:00:00'],
    ];


    public function up(): void
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('client_id');
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
