<?php

namespace App\Services;


use App\FileElement;
use Illuminate\Http\Request;
use Storage;


class FileExplorerService extends TransformerService {

	private $DISK_DRIVER;

	// setup the Storage disk driver
	public function __construct(){
		$this->DISK_DRIVER = config('filesystems.media_library');
	}

	// return all root FileElement
	public function all(){
		$fileElements = FileElement::where('parent_id', null)->get();

		return respond($this->transformCollection($fileElements));
	}

	// return all FileElement inside the given Directory
	public function explore(FileElement $fileElement){
		$result = $this->transform($fileElement);
		$result['children'] = $this->transformCollection($fileElement->children);

		return respond($result);
	}

	// create a File/Directory in the specified Directory
	public function create(Request $request){
		if ($request->has('file')) {
			return $this->uploadFile($request);
		}
		return $this->createDirectory($request);
	}

	// rename the given folder
	public function rename(Request $request, FileElement $fileElement){
		$request->validate([
			'name' => 'required|max:40',
		]);

		if ($this->isNameTaken($request, $fileElement)) {
			return validation_error('The name “'. $request->name .'” is already taken. Please choose a different name.');
		}

		$current_dir_path = $this->transformElementPath($fileElement);

		if (!$this->fileElementExists($current_dir_path)) {
			return validation_error('The folder “'. $request->name .'” does not exists.');
		}

		$fileElement->name = $request->name;
		$fileElement->save();

		$new_dir_path = $this->transformElementPath($fileElement);

		Storage::disk($this->DISK_DRIVER)->move($current_dir_path, $new_dir_path);

		return no_content();
	}

	// move the File to the specified Directory
	public function move(Request $request, FileElement $fileElement){
		$request->validate([
			'target_dir_id' => 'integer|nullable'
		]);

		$target_dir = FileElement::find($request->target_dir_id);

		$target_path = '';
		$current_path = $this->transformElementPath($fileElement);

		if (!$target_dir && $request->target_dir_id !== null) {
			return validation_error('The selected Directory is invalid or does not exists');
		}elseif (!$this->fileElementExists($current_path)) {
			dd($current_path);
			return validation_error('The folder “'. $fileElement->name .'” does not exists anymore.');
		}


		$fileElement->parent_id = $target_dir->id;
		$fileElement->save();

		if ($target_dir) {
			$target_path = $this->transformElementPath($target_dir);
		}

		Storage::disk($this->DISK_DRIVER)->move($current_path, $target_path . $fileElement->name);

		return no_content();
	}

	// delete the given File/Directory and its children
	public function destroy(FileElement $fileElement){
		$current_dir_path = $this->transformElementPath($fileElement);

		if (!$this->fileElementExists($current_dir_path)) {
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



	// upload a File to the specified Directory
	private function uploadFile(Request $request){
		$request->validate([
			'current_dir_id' => 'integer|nullable',
			'file' => 'required|file|max:2000'
		]);

		$file = $request->file('file');
		$filename = $this->get_file_name($file);

		$fileElement = FileElement::create([
			'name' => $filename,
			'type' => 'f',
			'parent_id' => $request->current_dir_id
		]);

		$path = $this->transformElementPath($fileElement);
		Storage::disk($this->DISK_DRIVER)->putFileAs($path, $file, $filename);

		return respond($this->transform($fileElement));
	}

	// create a Directory in the specified location
	private function createDirectory(Request $request){
		$request->validate([
			'name' => 'required|max:40',
			'current_dir_id' => 'integer|nullable'
		]);


		if ($this->isNameTaken($request)) {
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

	// prepare the Storage path for the given FileElement
	private function transformElementPath($fileElement){
		// if FileElement has no parent then return just the name
		if ($this->cantGoUp($fileElement)) {
			if ($this->isDirectory($fileElement)) {
				return $fileElement->name . '/';
			}
			return $fileElement->name;
		}

		$elementStack = [];
 		$currentFileElement = $fileElement;
		$path = "";


		// iterate through the FileElement and its parent and push the name to the $elementStack
		while ($this->canGoUp($currentFileElement)) {
			array_push($elementStack, $currentFileElement->name);
			$currentFileElement = $currentFileElement->parent;
		}

		// push the root FileElement to the element stack
		array_push($elementStack, $currentFileElement->name);


		// iterate through the parents name and create a storage path for that
		while (!empty($elementStack)) {
			$element = array_pop($elementStack);
			if ($fileElement->name === $element && !$this->isDirectory($fileElement)) {
				$path .= $element;
			}else{
				$path .= $element . '/';
			}
		}

		return $path;
	}

	// prepare the Storage url for the given FileElement
	private function transformElementUrl($fileElement){
		$path = $this->transformElementPath($fileElement);

		if ($fileElement->type == 'd') {
			return $path;
		}

		if ($this->fileElementExists($path)) {
			return Storage::disk($this->DISK_DRIVER)->url($path);
		}
		return null;
	}

	// produce unique name for the file
  public function get_file_name($file){
    $file_name = $file->getClientOriginalName();
    $file_ext = $file->getClientOriginalExtension();

    $file_name = str_replace('.' . $file_ext, '', $file_name);
    // Hash a unique name for the file
    $file_unique_name = md5($file_name . time()) . '.' . $file_ext;

    return $file_unique_name;
  }

	// check if FileElement is a Directory
	private function isDirectory($fileElement){
		return $fileElement->type == 'd' ? true : false;
	}

	// check if FileElement has a parent
	private function canGoUp($fileElement){
		return $fileElement->parent_id === null ? false : true;
	}

	// check if FileElement has no parent
	private function cantGoUp($fileElement){
		return !$this->canGoUp($fileElement);
	}

	// check if Directory name is take
	private function isNameTaken(Request $request, FileElement $fileElement = null){
		if ($fileElement === null) {
			return FileElement::where('name', $request->name)
													->where('parent_id', $request->current_dir_id)
													->first() != null;
		}
		return FileElement::where('id', '!=', $fileElement->id)
											  ->where('name', $request->name)
												->where('parent_id', $fileElement->parent_id)
												->first() != null;
	}

	// check if FileElement exists
	private function fileElementExists($path){
		return Storage::disk($this->DISK_DRIVER)->exists($path);
	}

	// prepare the FileElement for a the json response
	public function transform($fileElement){
		return [
			"id" => $fileElement->id,
			"name" => $fileElement->name,
			"parent_id" => $fileElement->parent_id,
			"type" => $fileElement->type,
			"canGoUp" => $this->canGoUp($fileElement),
			"url" => $this->transformElementUrl($fileElement),
			"path" => $this->transformElementPath($fileElement),
			"is_dir" => $this->isDirectory($fileElement),
		];
	}
}
