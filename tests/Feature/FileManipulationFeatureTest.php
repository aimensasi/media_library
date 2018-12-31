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
		$fileElement = $this->createFileElement();

		$response = $this->json('DELETE', "/explorers/{$fileElement->id}");


		// Assertation
		$response->assertStatus(204);
		Storage::disk($this->DISK_DRIVER)->assertMissing($fileElement->path);
	}

	/**
	 * @test
	 *
	 * @group test_move_file
	 */
	public function it_should_move_file_to_specified_directory(){
		$directory = factory(FileElement::Class)->create();
		$fileElement = $this->createFileElement();

		// Move The file
		$response = $this->json('PATCH', "/explorers/{$fileElement->id}/move", [
			'target_dir_id' => $directory->id
		]);

		// Assert that the file has been moved
		$response->assertStatus(204);
		Storage::disk($this->DISK_DRIVER)->assertMissing($fileElement->path);

		$fileElement = $this->getFileElement($fileElement->id);

		$this->assertTrue($fileElement->parent_id === $directory->id);
		Storage::disk($this->DISK_DRIVER)->assertExists($fileElement->path);
	}



	/**
	 *
	 * Private Helper Methods
	 *
	 */


	// send a post request to create a file
	private function createFileElement($parent_id = null){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => $parent_id,
			'file' => UploadedFile::fake()->image('faker.jpg')
		]);

		$fileElement = json_decode($response->getContent());

		return $fileElement;
	}

	// Get FileElement with path & url
	public function getFileElement($id){
		$response = $this->json('GET', "/explorers/{$id}");
		$fileElement = json_decode($response->getContent());

		return $fileElement;
	}

}
