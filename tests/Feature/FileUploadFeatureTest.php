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
 *
 * @group file_upload_test
 */
class FileUploadFeatureTest extends TestCase{


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
	public function it_should_upload_file_inside_root(){
		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => null,
			'file' => UploadedFile::fake()->image('faker.jpg')
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir', 'path']);
		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);
	}


	/**
	* @test
	*
	*/
	public function it_should_upload_file_three_level_deep(){
		$first_dir = factory(FileElement::class)->create();
		$second_dir = factory(FileElement::class)->create([
			'parent_id' => $first_dir->id
		]);
		$third_dir = factory(FileElement::class)->create([
			'parent_id' => $second_dir->id
		]);

		$response = $this->json('POST', '/explorers', [
			'current_dir_id' => $third_dir->id,
			'file' => UploadedFile::fake()->image('faker.jpg')
		]);

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir', 'path']);


		$content = json_decode($response->getContent());

		Storage::disk($this->DISK_DRIVER)->assertExists($content->path);
	}
}
