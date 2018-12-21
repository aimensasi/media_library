<?php

namespace App\Services;


use App\FileElement;
use Illuminate\Http\Request;
use Storage;


class FileExplorerService extends TransformerService{

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

	// public function store(Request $request){
	// 	$validatedData = $request->validate([
	// 		'name' => 'required|unique:media|max:40',
	// 		'current_dir' => 'required',
	// 	]);
	//
	// 	$dir_path = $validatedData['current_dir'] . $validatedData['name'];
	//
	// 	$media = FileElement::create([
	// 		'name' => $validatedData['name'],
	// 		'path' => $dir_path,
	// 	]);
	//
	// 	if ($media) {
	// 		Storage::makeDirectory($dir_path);
	//
	// 		return respond($this->transform($media));
	// 	}
	// }


	private function transformElementPath($fileElement){
		if ($fileElement->type == 'd') {
			return "fa-folder";
		}

		$parentElement = $fileElement;
		$parentNames = [];
		$path = "";

		while ($this->canGoUp($parentElement)) {
			$parentElement = $parentElement->parent;
			array_push($parentNames, $parentElement->name);
		}

		while (!empty($parentNames)) {
			$path .= array_pop($parentNames) . '/';
		}

		if (Storage::exists($path . $fileElement->name)) {
			return Storage::url($path . $fileElement->name);
		}
		return null;
	}

	private function isDirectory($fileElement){
		return $fileElement->type == 'd' ? true : false;
	}

	private function canGoUp($fileElement){
		return $fileElement->parent_id === null ? true : false;
	}

	public function transform($fileElement){
		return [
			"id" => $fileElement->id,
			"name" => $fileElement->name,
			"parent_id" => $fileElement->parent_id,
			"canGoUp" => $this->canGoUp($fileElement),
			"url" => $this->transformElementPath($fileElement),
			"is_dir" => $this->isDirectory($fileElement),
		];
	}
}
