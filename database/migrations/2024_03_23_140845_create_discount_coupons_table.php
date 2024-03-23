<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            //discount coupon code
            $table->string('code');
            //human readable discount code
            $table->string('name')->nullable();
            //description of coupon
            $table->text('description')->nullable();
            //
            $table->integer('max_uses')->nullable();
            //how many times can user can use this code
            $table->integer('max_uses_user')->nullable();
            //whether the coupon is percentage or fixed price
            $table->enum('type',['percent','fixed'])->default('fixed');
            //the amount to discount based on type
            $table->double('discount_amount',10,2);
            //
            $table->double('min_amount',10,2);

            $table->integer('status')->default(0);
            //start at
            $table->timestamp('start_at')->nullable();
            //end at
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_coupons');
    }
}
