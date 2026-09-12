<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('biteship_order_id')->nullable()->index();
            $table->string('courier_company');
            $table->string('courier_type');
            $table->string('tracking_id')->nullable()->index();
            $table->string('waybill_id')->nullable()->index();
            $table->enum('status', [
                'pending',
                'requested',
                'picked_up',
                'on_process',
                'delivered',
                'cancelled',
            ])->default('pending');
            $table->json('rate_snapshot')->nullable();
            $table->dateTime('pickup_scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
