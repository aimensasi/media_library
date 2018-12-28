<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileElement extends Model{


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		"name", "parent_id", "type"
	];


	public function parent(){
		return $this->belongsTo(self::class, 'parent_id');
	}

	public function children(){
		return $this->hasMany(self::class, 'parent_id');
	}


	public static function boot(){
		parent::boot();

		// Handling cascading delete of fileElement
		static::deleting(function($fileElement){
			if ($fileElement->type === 'd') {
				$fileElement->children()->get()->each(function($fileElement){
					$fileElement->delete();
				});
			}
		});
	}
}
