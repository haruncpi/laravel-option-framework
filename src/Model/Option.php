<?php namespace Haruncpi\LaravelOptionFramework\Model;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'option_name';
    protected $fillable = ['option_name', 'option_value'];
}