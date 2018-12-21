<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use Illuminate\Support\Facades\Schema;

class FileElementsMigrationTest extends TestCase{

	/**
   * @test
   *
   * @return void
   */
  public function it_has_file_elements_table(){
    $this->assertTrue(Schema::hasTable('file_elements'));
  }

  /**
   * @test
   *
   */
  public function it_has_column_id(){
    $this->assertTrue(Schema::HasColumn('file_elements', 'id'));
  }


	/**
	 * @test
	 *
	 */
	public function it_has_column_name(){
		$this->assertTrue(Schema::HasColumn('file_elements', 'name'));
	}

	/**
	 * @test
	 *
	 */
	public function it_has_column_parent_id(){
		$this->assertTrue(Schema::HasColumn('file_elements', 'parent_id'));
	}

	/**
	 * @test
	 *
	 */
	public function it_has_column_is_type(){
		$this->assertTrue(Schema::HasColumn('file_elements', 'type'));
	}
}
