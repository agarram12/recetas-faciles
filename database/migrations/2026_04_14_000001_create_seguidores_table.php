<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seguidores', function (Blueprint $table) {
            $table->unsignedBigInteger('seguidor_id');
            $table->unsignedBigInteger('seguido_id');
            $table->timestamps();

            $table->primary(['seguidor_id', 'seguido_id']);
            $table->foreign('seguidor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seguido_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seguidores');
    }
};
