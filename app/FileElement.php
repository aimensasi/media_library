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
}
