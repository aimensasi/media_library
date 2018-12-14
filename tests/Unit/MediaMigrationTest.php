<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaMigrationTest extends TestCase{

	/**
   * @test
   *
   * @return void
   */
  public function it_has_media_table(){
    $this->assertTrue(Schema::hasTable('media'));
  }

  /**
   * @test
   *
   */
  public function it_has_column_id(){
    $this->assertTrue(Schema::HasColumn('media', 'id'));
  }


	/**
	 * @test
	 *
	 */
	public function it_has_column_name(){
		$this->assertTrue(Schema::HasColumn('media', 'name'));
	}

	/**
	 * @test
	 *
	 */
	public function it_has_column_path(){
		$this->assertTrue(Schema::HasColumn('media', 'path'));
	}

}
