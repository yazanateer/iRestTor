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
        Schema::create('business_branding_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('logo_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('primary_color')->default('#2563ff');
            $table->string('secondary_color')->default('#3b82f6');
            $table->string('accent_color')->default('#16a34a');
            $table->string('public_title')->nullable();
            $table->string('public_subtitle')->nullable();
            $table->text('public_description')->nullable();
            $table->string('theme_style')->default('default');
            $table->timestamps();
            $table->unique('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_branding_settings');
    }
};
