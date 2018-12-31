<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use App\FileElement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * @group file_manipulation_test
 *
 */
class FileManipulationFeatureTest extends TestCase{

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
	 *
	 */
	public function it_should_delete_file(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'file' => UploadedFile::fake()->image('faker.jpg')
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir', 'path']);
		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);

		$response = $this->json('DELETE', "/explorers/{$content->id}");

		$response->assertStatus(204);

		Storage::disk($this->DISK_DRIVER)->assertMissing($content->path);
	}

	/**
	 * @test
	 *
	 * @group test_move_file
	 */
	public function it_should_move_file_to_specified_directory(){
		$directory = factory(FileElement::Class)->create();

		// Upload the File
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'file' => UploadedFile::fake()->image('faker.jpg')
		]);


		$response->assertStatus(200);
		$fileElement = json_decode($response->getContent());
		Storage::disk($this->DISK_DRIVER)->assertExists($fileElement->path);

		// Move The file
		$response = $this->json('PATCH', "/explorers/{$fileElement->id}/move", [
			'target_dir_id' => $directory->id
		]);


		// Assert that the file has been moved
		$response->assertStatus(204);
		Storage::disk($this->DISK_DRIVER)->assertMissing($fileElement->path);

		// Get the file again and assert that it exists in its new location
		$response = $this->json('GET', "/explorers/{$fileElement->id}");
		$fileElement = json_decode($response->getContent());

		$this->assertTrue($fileElement->parent_id === $directory->id);
		Storage::disk($this->DISK_DRIVER)->assertExists($fileElement->path);
	}

}
