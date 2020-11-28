<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacTable extends Migration
{
    public function up()
    {
        $tables = config('rbac.tables');
        $columns = config('rbac.columns');
	    
        if (empty($tables)) {
            throw new Exception('Error: config/rbac.php not found.');
        }
	
	    Schema::create($tables['permits'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug')->unique();
		    $table->text('path')->nullable();
		    $table->text('method')->nullable();
		    $table->string('guard');
		    $table->timestamps();
	    });
	
	    Schema::create($tables['roles'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug')->unique();
		    $table->string('guard');
		    $table->timestamps();
	    });

        Schema::create($tables['permit_roles'], function (Blueprint $table) use ($tables) {
            $table->unsignedBigInteger('permit_id');
        	$table->unsignedBigInteger('role_id');
            
            $table->unique(['permit_id', 'role_id']);
        });
	
	    Schema::create($tables['permit_users'], function (Blueprint $table) use ($tables, $columns) {
		    $table->unsignedBigInteger($columns['permit_morph_id']);
		    $table->unsignedBigInteger($columns['permit_morph_key']);
		    $table->string($columns['permit_morph_type']);
		
		    $table->index([$columns['permit_morph_id'], $columns['permit_morph_key'], $columns['permit_morph_type']]);
	    });

        Schema::create($tables['role_users'], function (Blueprint $table) use ($tables, $columns) {
        	$table->unsignedBigInteger($columns['role_morph_id']);
        	$table->unsignedBigInteger($columns['role_morph_key']);
        	$table->string($columns['role_morph_type']);
            
            $table->index([$columns['role_morph_id'], $columns['role_morph_key'], $columns['role_morph_type']]);
        });
    }
    
    public function down()
    {
        $tables = config('rbac.tables');

        if (empty($tables)) {
            throw new Exception('Error: config/rbac.php not found.');
        }

        Schema::drop($tables['role_users']);
        Schema::drop($tables['permit_users']);
        Schema::drop($tables['role_permits']);
        Schema::drop($tables['roles']);
        Schema::drop($tables['permits']);
    }
}
