<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;
use App\Services\MediaService;

class MediaController extends Controller{

  protected $mediaService;
	protected $path = 'media.';

	public function __construct(MediaService $mediaService){
		$this->mediaService = $mediaService;
	}

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request){
		if ($request->wantsJson()) {
			return $this->mediaService->all();
		}

		return view($this->path . 'index');
  }
}
