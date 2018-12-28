<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\FileElement;


/**
* @group file_explorer_feature
*/
class FileExplorerTest extends TestCase{

	use RefreshDatabase;

	/**
	 * @test
	 *
	 */
	public function it_should_return_file_explorer_view(){
		factory(FileElement::class, 4)->create();

		$response = $this->get('/explorers');

		$response->assertStatus(200)->assertViewIs('fileExplorers.index');
	}

  /**
	 * @test
	 *
   */
	public function it_should_return_media_directoies(){
		factory(FileElement::class, 4)->create();

		$response = $this->json('GET', '/explorers');

		$response->assertStatus(200)->assertJsonStructure([['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir']]);
	}

	/**
	 * @test
	 *
	 */
	public function it_should_return_selected_folder_and_its_children(){
		$parent = factory(FileElement::class)->create();
		factory(FileElement::class, 10)->create(['parent_id' => $parent->id]);

		$response = $this->json('GET', "/explorers/{$parent->id}");

		$response->assertStatus(200)->assertJsonStructure(['id', 'name', 'parent_id', 'canGoUp', 'url', 'is_dir', 'children']);
	}


}
