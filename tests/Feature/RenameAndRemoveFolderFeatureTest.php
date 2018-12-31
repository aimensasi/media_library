<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\FileElement;
use Illuminate\Support\Facades\Config;
use Storage;


/**
* @group add_remove_folders_test
*/
class RenameAndRemoveFolderFeatureTest extends TestCase{

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
	public function it_should_rename_folder_inside_root(){
		//  Creating the folder
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);

		//  Renaming the folder

		$newName = 'new folder 2';

		$response = $this->json('PATCH', "/explorers/{$content->id}/rename", [
			'name' => $newName
		]);

		$response->assertStatus(204);


		$response = $this->json('GET', "/explorers/{$content->id}");
		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);
	}

	/**
	* @test
	*/
	public function it_should_rename_folder_three_level_deep(){
		//  Creating the folder
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

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);

		//  Renaming the folder

		$newName = 'new folder 2';

		$response = $this->json('PATCH', "/explorers/{$content->id}/rename", [
			'name' => $newName
		]);

		$response->assertStatus(204);

		$response = $this->json('GET', "/explorers/{$content->id}");

		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);
	}


	/**
	* @test
	*/
	public function it_should_delete_folder_and_its_content_inside_root(){
		//  Creating the folder
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => 'new folder'
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);

		//  Deleting the folder

		$response = $this->json('DELETE', "/explorers/{$content->id}");

		$response->assertStatus(204);

		Storage::disk($this->DISK_DRIVER)->assertMissing($content->path);
	}


	/**
	* @test
	*/
	public function it_should_delete_parent_folder_and_its_content_three_level_deep(){
		//  Creating the folder

		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'name' => "posts"
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']);

		$first_dir = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($first_dir->path);
		$this->assertTrue(FileElement::find($first_dir->id) != null);

		$second_dir = factory(FileElement::class)->create([
			'parent_id' => $first_dir->id
		]);
		$third_dir = factory(FileElement::class)->create([
			'parent_id' => $second_dir->id
		]);


		//  Deleting the folder

		$response = $this->json('DELETE', "/explorers/{$first_dir->id}");

		$response->assertStatus(204);


		Storage::disk($this->DISK_DRIVER)->assertMissing($first_dir->path);
		$this->assertTrue(FileElement::find($first_dir->id) === null);
	}

}
