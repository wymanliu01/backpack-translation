<?php

namespace App\Models;

use App\Traits\HasImageModel;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasImageModel;

    public $imageable = ['logo', 'map'];
    protected $fillable = ['name', 'introduction'];

    public function setLogoAttribute($value)
    {
        $this->setImage('logo', $value);
    }

    public function setMapAttribute($value)
    {
        $this->setImage('map', $value);
    }
}
