<?php

namespace App\Services;


use App\FileElement;
use Illuminate\Http\Request;
use Storage;


class FileExplorerService extends TransformerService {

	private $DISK_DRIVER;

	public function __construct(){
		$this->DISK_DRIVER = config('filesystems.media_library');
	}

	public function all(){
		$fileElements = FileElement::where('parent_id', null)->get();

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
			'current_dir_id' => 'integer|nullable'
		]);



		if (FileElement::where('name', $request->name)->where('parent_id', $request->current_dir_id)->first() != null) {
			return validation_error('The name “'. $request->name .'” is already taken. Please choose a different name.');
		}

		$fileElement = FileElement::create([
			'name' => $request->name,
			'parent_id' => $request->current_dir_id,
			'type' => 'd'
		]);
		Storage::disk($this->DISK_DRIVER)->makeDirectory($this->transformElementPath($fileElement));
		return respond($this->transform($fileElement));
	}


	public function rename(Request $request, FileElement $fileElement){
		$request->validate([
			'name' => 'required|max:40',
		]);

		if (FileElement::where('id', '!=', $fileElement->id)->where('name', $request->name)->where('parent_id', $fileElement->parent_id)->first() != null) {
			return validation_error('The name “'. $request->name .'” is already taken. Please choose a different name.');
		}

		$current_dir_path = $this->transformElementPath($fileElement);

		if (!Storage::disk($this->DISK_DRIVER)->exists($current_dir_path)) {
			return validation_error('The folder “'. $request->name .'” does not exists.');
		}


		$fileElement->name = $request->name;
		$fileElement->save();

		$new_dir_path = $this->transformElementPath($fileElement);

		Storage::disk($this->DISK_DRIVER)->move($current_dir_path, $new_dir_path);

		return no_content();
	}


	public function destroy(FileElement $fileElement){
		$current_dir_path = $this->transformElementPath($fileElement);

		if (!Storage::disk($this->DISK_DRIVER)->exists($current_dir_path)) {
			return validation_error('The folder “'. $request->name .'” does not exists.');
		}

		Storage::disk($this->DISK_DRIVER)->deleteDirectory($current_dir_path);

		$fileElement->delete();

		return no_content();
	}


	/**
	*
	* Private Methods | Helpers
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

		array_push($parentNames, $fileElement->name);


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
			return Storage::disk($this->DISK_DRIVER)->url($path);
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
