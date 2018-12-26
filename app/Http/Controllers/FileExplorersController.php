<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FileElement;
use App\Services\FileExplorerService;

class FileExplorersController extends Controller{

	protected $fileExplorerService;
	protected $path = 'fileExplorers.';

	public function __construct(FileExplorerService $fileExplorerService){
		$this->fileExplorerService = $fileExplorerService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request){
		if ($request->wantsJson()) {
			return $this->fileExplorerService->all();
		}
		return view($this->path . 'index');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request){
		return $this->fileExplorerService->addDirectory($request);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\FileElement  $fileElement
	 * @return \Illuminate\Http\Response
	 */
	public function show($id){
		$fileElement = FileElement::findOrFail($id);

		return $this->fileExplorerService->explore($fileElement);
	}
}
