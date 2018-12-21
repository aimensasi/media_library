<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Media;

class MediaUploadTest extends TestCase{

	use RefreshDatabase;

	// /**
	//  * @test
	//  *
	//  * @media_feature
	//  */
	// public function it_should_return_media_view(){
	// 	factory(Media::class, 4)->create();
	//
	// 	$response = $this->get('/media');
	//
	// 	$response->assertStatus(200)->assertViewIs('media.index');
	// }
	//
  // /**
	//  * @test
	//  *
	//  * @media_feature
  //  */
	// public function it_should_return_media_directoies(){
	// 	factory(Media::class, 4)->create();
	//
	// 	$response = $this->json('GET', '/media');
	//
	// 	$response->assertStatus(200)->assertJsonStructure([['id', 'name', 'path', 'is_dir']]);
	// }
	//
	//
  // /**
  //  * @test
  //  *
	//  * @media_feature
  //  */
  // public function it_shoud_create_folder_inside_root(){
	// 	# specify current working directory
	// 	$root_dir = factory(Media::class)->state('root')->create();
	//
	// 	# send a request with the current working directory + the new directory name
	// 	$response = $this->json('POST', '/media/store', [
	// 		'name' => 'new_folder',
	// 		'current_dir' => 'media/'
	// 	]);
	//
	// 	# assert request is 200
	// 	$response->assertStatus(200)->assertJson([
	// 		'name' => 'new_folder',
	// 		'path' => 'media/new_folder'
	// 	]);
	// 	# assert request 422, return a message
	// 	# assert request 500, return a message
  // }
	//
	// /**
  //  * @test
  //  *
	//  * @media_feature
  //  */
  // public function it_shoud_not_create_folder_missing_params(){
	// 	# specify current working directory
	// 	$root_dir = factory(Media::class)->state('root')->create();
	//
	// 	# send a request with the current working directory + the new directory name
	// 	$response = $this->json('POST', '/media/store', [
	// 		'name' => 'new_folder',
	// 	]);
	//
	// 	# assert request 422, return a message
	// 	$response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
  // }
	//
	//
	// /**
	// * @test
	// *
	// * @media_feature
	// */
	// public function it_should_rename_directory(){
	//
	// }



















}
