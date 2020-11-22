<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermitTables extends Migration
{
    public function up()
    {
        $tableNames = config('permit.table_names');
	    
        if (empty($tableNames)) {
            throw new Exception('Error: config/permit.php not found.');
        }
	
	    Schema::create($tableNames['permits'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug');
		    $table->string('path');
		    $table->string('guard_name');
		    $table->timestamps();
	    });
	
	    Schema::create($tableNames['roles'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug');
		    $table->string('path');
		    $table->string('guard_name');
		    $table->timestamps();
	    });

        Schema::create($tableNames['role_permits'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permit_id');
            
            $table->unique(['role_id', 'permit_id']);
        });

        Schema::create($tableNames['role_users'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            
            $table->unique(['role_id', 'user_id']);
        });
    }
    
    public function down()
    {
        $tableNames = config('permit.table_names');

        if (empty($tableNames)) {
            throw new Exception('Error: config/permit.php not found.');
        }

        Schema::drop($tableNames['role_permits']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permits']);
    }
}
