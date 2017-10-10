<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    use App\Traits\MysqlVersion;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->bigInteger('address_id');
            $table->string('invoice_id')->nullable();
            $table->text('transaction_hash')->nullable();
            $table->double('price_usd')->nullable();
            $table->double('price_iota')->nullable();
            $table->text('ipn')->nullable();
            $table->string('ipn_verify_code')->nullable();

            if ($this->version() >= '5.7.8') {
                $table->json('metadata')->default(null);
            }else {
                $table->text('metadata')->default(null);
            }

            $table->integer('status');
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
        Schema::dropIfExists('payments');
    }
}
