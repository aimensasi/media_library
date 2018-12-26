<?php

namespace App\Services;


use App\FileElement;
use Illuminate\Http\Request;
use Storage;


class FileExplorerService extends TransformerService {

	private const DISK_DRIVER = 'media';

	public function all(){
		$fileElements = FileElement::all();

		return respond($this->transformCollection($fileElements));
	}


	public function explore(FileElement $fileElement){
		$children = $this->transformCollection($fileElement->children);
		$fileElement = $this->transform($fileElement);

		$fileElement['children'] = $children;

		return respond($fileElement);
	}

	public function addDirectory(Request $request){

		$request->validate([
			'name' => 'required|max:40',
		]);

		dd(FileElement::first());

		$fileElement = FileElement::create([
			'name' => $request->name,
			'parent_id' => $request->current_dir_id,
			'type' => 'd'
		]);


		Storage::disk(self::DISK_DRIVER)->makeDirectory($this->transformElementPath($fileElement));
		return respond($this->transform($fileElement));
	}


	/**
	*
	* Private Methods
	*
	*/


	private function transformElementPath($fileElement){
		$parentNames = [];
		$path = "";

		if (!$this->canGoUp($fileElement)) {
			return $fileElement->name;
		}

		while ($this->canGoUp($fileElement)) {
			array_push($parentNames, $fileElement->name);
			$fileElement = $fileElement->parent;
		}

		while (!empty($parentNames)) {
			$path .= array_pop($parentNames) . '/';
		}

		return $path;
	}


	private function transformElementUrl($fileElement){
		$path = $this->transformElementPath($fileElement);

		if ($fileElement->type == 'd') {
			return $path;
		}


		if (Storage::exists($path)) {
			return Storage::disk(self::DISK_DRIVER)->url($path);
		}
		return null;
	}

	private function isDirectory($fileElement){
		return $fileElement->type == 'd' ? true : false;
	}

	private function canGoUp($fileElement){
		return $fileElement->parent_id === null ? false : true;
	}

	public function transform($fileElement){
		return [
			"id" => $fileElement->id,
			"name" => $fileElement->name,
			"parent_id" => $fileElement->parent_id,
			"type" => $fileElement->type,
			"canGoUp" => $this->canGoUp($fileElement),
			"url" => $this->transformElementUrl($fileElement),
			"is_dir" => $this->isDirectory($fileElement),
		];
	}
}
