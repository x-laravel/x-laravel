<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'auth';
    protected string $table = 'oauth_access_tokens';

    public function up(): void
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->uuid('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('expires_at', 6)->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists($this->table);
    }
};
