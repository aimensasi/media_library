<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\FileElement;
use Storage;
use Illuminate\Support\Facades\Config;
/**
* @group creating_folders_test
*/
class CreatingFoldersFeatureTest extends TestCase{

	use RefreshDatabase;

	private $DISK_DRIVER;

	/**
   * @before
   */
  public function erase_all_test_folders(){
		parent::setup();

		$this->DISK_DRIVER = 'media_test';

		Config::set('filesystems.media_library', $this->DISK_DRIVER);
		Storage::deleteDirectory('tests/media');
  }


	/**
   * @test
   */
  public function it_should_create_folder_inside_root(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$content = json_decode($response->getContent());

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);
		Storage::disk($this->DISK_DRIVER)->assertExists($content->url);
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

		$content = json_decode($response->getContent());

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);
		Storage::disk($this->DISK_DRIVER)->assertExists($content->url);
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

		$content = json_decode($response->getContent());

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);
		Storage::disk($this->DISK_DRIVER)->assertExists($content->url);
	}


	/**
	* @test
	*/
	public function it_should_fail_to_create_folder_when_duplicate(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$content = json_decode($response->getContent());

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);
		Storage::disk($this->DISK_DRIVER)->assertExists($content->url);


		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$content = json_decode($response->getContent());

		$response->assertStatus(422)->assertJson(["message" => "The name “new folder” is already taken. Please choose a different name."]);

	}

}
