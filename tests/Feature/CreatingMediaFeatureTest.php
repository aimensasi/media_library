<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\FileElement;
use Storage;

/**
* @group creating_media_test
*/
class CreatingMediaFeatureTest extends TestCase{


	use RefreshDatabase;


	/**
   * @test
   */
  public function it_should_create_folder_inside_root(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		$this->assertTrue(Storage::disk('media')->exists($content->url), 'Folder does not exists');
  }


	/**
   * @test
   */
  public function it_should_create_folder_one_level_deep(){
		$dir = factory(FileElement::class)->create();

		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => $dir->id,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		$this->assertTrue(Storage::disk('media')->exists($content->url), 'Folder does not exists');
  }

	/**
	* @test
	*
	*/
	public function it_should_create_folder_three_level_deep(){
		$first_dir = factory(FileElement::class)->create();
		$second_dir = factory(FileElement::class)->create([
			'parent_id' => $first_dir->id
		]);
		$third_dir = factory(FileElement::class)->create([
			'parent_id' => $second_dir->id
		]);


		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => $third_dir->id,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		$this->assertTrue(Storage::disk('media')->exists($content->url), 'Folder does not exists');
	}


	/**
	* @test
	*/
	public function it_should_fail_to_create_folder_when_duplicate(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		$this->assertTrue(Storage::disk('media')->exists($content->url), 'Folder does not exists');


		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$content = json_decode($response->getContent());

		$response->assertStatus(422)->assertJson(["message" => "The name “new folder” is already taken. Please choose a different name."]);

	}

}
