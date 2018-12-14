<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class MediaFilesMigrationTest extends TestCase{


	/**
	 * @test
	 *
	 * @return void
	 */
	public function it_has_media_files_table(){
		$this->assertTrue(Schema::hasTable('media_files'));
	}

	/**
	 * @test
	 *
	 */
	public function it_has_column_id(){
		$this->assertTrue(Schema::HasColumn('media_files', 'id'));
	}


	/**
	 * @test
	 *
	 */
	public function it_has_column_name(){
		$this->assertTrue(Schema::HasColumn('media_files', 'media_id'));
	}


	/**
	 * @test
	 *
	 */
	public function it_has_column_path(){
		$this->assertTrue(Schema::HasColumn('media_files', 'filename'));
	}
}
