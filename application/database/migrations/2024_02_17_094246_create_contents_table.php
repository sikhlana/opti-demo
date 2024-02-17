<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('url');
            $table->string('state')->index();
            $table->foreignUlid('parent_id')->nullable()->constrained('contents')->cascadeOnDelete();
            $table->text('canonical_url')->nullable();
            $table->char('hash', 96)->nullable()->index();
            $table->text('error')->nullable();
            $table->string('content_type')->nullable();
            $table->json('meta')->nullable();
            $table->text('title')->nullable();
            $table->mediumText('body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
