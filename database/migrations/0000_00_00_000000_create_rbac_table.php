<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacTable extends Migration
{
	private $tables = [];
	private $columns = [];
	
	public function __construct()
	{
		$tables = config('rbac.tables', []);
		$columns = config('rbac.columns', []);
		
		$this->tables['permits'] = isset($tables['permits']) ? $tables['permits'] : 'permits';
		$this->tables['roles'] = isset($tables['roles']) ? $tables['roles'] : 'roles';
		$this->tables['permit_roles'] = isset($tables['permit_roles']) ? $tables['permit_roles'] : 'permit_roles';
		$this->tables['permit_users'] = isset($tables['permit_users']) ? $tables['permit_users'] : 'permit_users';
		$this->tables['role_users'] = isset($tables['role_users']) ? $tables['role_users'] : 'role_users';
		
		$this->columns['permit_morph_id'] = isset($columns['permit_morph_id']) ? $columns['permit_morph_id'] : 'permit_id';
		$this->columns['permit_morph_name'] = isset($columns['permit_morph_name']) ? $columns['permit_morph_name'] : 'permitable';
		$this->columns['permit_morph_key'] = isset($columns['permit_morph_key']) ? $columns['permit_morph_key'] : 'permitable_id';
		$this->columns['permit_morph_type'] = isset($columns['permit_morph_type']) ? $columns['permit_morph_type'] : 'permitable_type';
		
		$this->columns['role_morph_id'] = isset($columns['role_morph_id']) ? $columns['role_morph_id'] : 'role_id';
		$this->columns['role_morph_name'] = isset($columns['role_morph_name']) ? $columns['role_morph_name'] : 'rolable';
		$this->columns['role_morph_key'] = isset($columns['role_morph_key']) ? $columns['role_morph_key'] : 'rolable_id';
		$this->columns['role_morph_type'] = isset($columns['role_morph_type']) ? $columns['role_morph_type'] : 'rolable_type';
	}
	
	public function up()
    {
    	$columns = $this->columns;
    	
	    Schema::create($this->tables['permits'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug')->unique();
		    $table->text('path')->nullable();
		    $table->text('method')->nullable();
		    $table->string('guard');
		    $table->timestamps();
	    });
	
	    Schema::create($this->tables['roles'], function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('name');
		    $table->string('slug')->unique();
		    $table->string('guard');
		    $table->timestamps();
	    });

        Schema::create($this->tables['permit_roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('permit_id');
        	$table->unsignedBigInteger('role_id');
            
            $table->unique(['permit_id', 'role_id']);
        });
	
	    Schema::create($this->tables['permit_users'], function (Blueprint $table) use ($columns) {
		    $table->unsignedBigInteger($columns['permit_morph_id']);
		    $table->unsignedBigInteger($columns['permit_morph_key']);
		    $table->string($columns['permit_morph_type']);
		
		    $table->index([$columns['permit_morph_id'], $columns['permit_morph_key'], $columns['permit_morph_type']]);
	    });

        Schema::create($this->tables['role_users'], function (Blueprint $table) use ($columns) {
        	$table->unsignedBigInteger($columns['role_morph_id']);
        	$table->unsignedBigInteger($columns['role_morph_key']);
        	$table->string($columns['role_morph_type']);
            
            $table->index([$columns['role_morph_id'], $columns['role_morph_key'], $columns['role_morph_type']]);
        });
    }
    
    public function down()
    {
        Schema::drop($this->tables['role_users']);
        Schema::drop($this->tables['permit_users']);
        Schema::drop($this->tables['role_permits']);
        Schema::drop($this->tables['roles']);
        Schema::drop($this->tables['permits']);
    }
}
