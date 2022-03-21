<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'system';
    protected string $table = 'admin_password_resets';

    public function up(): void
    {
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at', 6)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists($this->table);
    }
};
