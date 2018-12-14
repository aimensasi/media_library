<?php

namespace App\Services;


use App\Media;
/**
 *
 */
class MediaService extends TransformerService{

		public function all(){
			$media = Media::all();

			return respond($this->transformCollection($media));
		}



		public function transform($media){
			return [
				"id" => $media->id,
				"name" => $media->name,
				"path" => $media->path,
			];
		}
}
