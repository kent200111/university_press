<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ims', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('college_id')->nullable()->constrained('colleges');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('publisher')->nullable();
            $table->string('edition')->nullable();
            $table->string('isbn')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ims');
    }
};