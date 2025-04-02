<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('text'); // text, boolean, number, json
            $table->boolean('is_public')->default(true); // Whether this setting should be visible to customers
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'PISA Pizza', 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'tax_enabled', 'value' => 'true', 'group' => 'tax', 'type' => 'boolean', 'is_public' => true],
            ['key' => 'tax_rate', 'value' => '13', 'group' => 'tax', 'type' => 'number', 'is_public' => true],
            ['key' => 'business_hours', 'value' => json_encode([
                'monday' => ['11:00-21:00'],
                'tuesday' => ['11:00-21:00'],
                'wednesday' => ['11:00-21:00'],
                'thursday' => ['11:00-21:00'],
                'friday' => ['11:00-22:00'],
                'saturday' => ['11:00-22:00'],
                'sunday' => ['12:00-20:00']
            ]), 'group' => 'business', 'type' => 'json', 'is_public' => true],
            ['key' => 'discounts_enabled', 'value' => 'true', 'group' => 'discount', 'type' => 'boolean', 'is_public' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
