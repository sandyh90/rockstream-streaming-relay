<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamRepeaterDestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_ingest_dest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_stream_id')->nullable()->constrained('input_stream')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name_stream_dest');
            $table->enum('platform_dest', ['youtube', 'twitch', 'custom']);
            $table->string('url_stream_dest');
            $table->string('key_stream_dest');
            $table->boolean('active_stream_dest');
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
        //
    }
}
