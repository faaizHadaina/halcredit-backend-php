<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_eligible')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_invoice')->default(false);
            $table->boolean('is_statement')->default(false);
            $table->boolean('is_details')->default(false);
            $table->boolean('is_guarantor')->default(false);
            $table->string('password');
            $table->string('password_reset_token')->nullable();
            $table->dateTime('password_reset_expires')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->boolean('has_transaction_pin')->default(false);
            $table->timestamps(); // createdAt and updatedAt
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
