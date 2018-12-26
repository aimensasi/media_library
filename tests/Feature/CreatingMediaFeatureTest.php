<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\FileElement;
use Storage;


class CreatingMediaFeatureTest extends TestCase{

	/**
   * @test
   *
   * @creating_media_test
   */
  public function it_should_create_folder_inside_root(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$this->assertTrue(Storage::disk('media')->exists('new folder'));
  }


	/**
   * @test
   *
   * @creating_media_test
   */
  public function it_should_create_folder_inside_posts(){
		$dir = factory(FileElement::class)->create(['name' => 'posts']);

		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => $dir->id,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$this->assertTrue(Storage::disk('media')->exists($dir->name . '/new folder'));
  }
}
