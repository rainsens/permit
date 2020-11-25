<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacTable extends Migration
{
    public function up()
    {
        $tableNames = config('rbac.table_names');
	    
        if (empty($tableNames)) {
            throw new Exception('Error: config/rbac.php not found.');
        }
	
	    Schema::create($tableNames['permits'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('slug')->nullable();
		    $table->string('name')->unique();
		    $table->string('method')->nullable();
		    $table->text('path')->nullable();
		    $table->string('guard');
		    $table->timestamps();
	    });
	
	    Schema::create($tableNames['roles'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('slug')->nullable();
		    $table->string('name')->unique();
		    $table->string('guard');
		    $table->timestamps();
	    });

        Schema::create($tableNames['permit_roles'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permit_id');
        	$table->unsignedBigInteger('role_id');
            
            $table->unique(['permit_id', 'role_id']);
        });
	
	    Schema::create($tableNames['permit_users'], function (Blueprint $table) use ($tableNames) {
		    $table->unsignedBigInteger('permit_id');
		    $table->unsignedBigInteger('permitable_id');
		    $table->string('permitable_type');
		
		    $table->index(['permit_id', 'permitable_id', 'permitable_type']);
	    });

        Schema::create($tableNames['role_users'], function (Blueprint $table) use ($tableNames) {
        	$table->unsignedBigInteger('role_id');
        	$table->unsignedBigInteger('rolable_id');
        	$table->string('rolable_type');
            
            $table->index(['role_id', 'rolable_id', 'rolable_type']);
        });
    }
    
    public function down()
    {
        $tableNames = config('rbac.table_names');

        if (empty($tableNames)) {
            throw new Exception('Error: config/rbac.php not found.');
        }

        Schema::drop($tableNames['role_users']);
        Schema::drop($tableNames['permit_users']);
        Schema::drop($tableNames['role_permits']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permits']);
    }
}
