<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Str;

/**
 * Class ProductTranslation
 * @package App\Models
 */
class ProductTranslation extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * @var string
     */
    protected $table = 'product_translation';

    /**
     * @param $value
     * @noinspection PhpUnused
     */
    public function setImageAttribute($value)
    {
        //delete action
        if (is_null($value) && !empty($this->image)) {
            //Storage::disk(config('filesystems.default'))->delete($this->image);

            $disk = config('filesystems.default');

            if (!empty($this->image)) {

                $originalImage = $this->image;

                Storage::disk($disk)->delete($originalImage);
            }

            if (!empty($this->id)) {
                Image::whereSourceType(self::class)->whereSourceId($this->id)->delete();
            }

            return;
        }

        $disk = config('filesystems.default');

        //local storage
        if (!is_null(config("filesystems.disks.{$disk}.root")) && Str::before($value, '/') === 'data:image') {

            $extension = Str::after(Str::before($value, ';'), '/');
            $filename = md5($value . time()) . ".{$extension}";
            $filePath = "images/$this->table/$filename";

            $imageBase64 = base64_decode(Str::after($value, ','));

            Storage::disk($disk)->put($filePath, $imageBase64);

            if (!empty($this->image)) {

                $originalImage = $this->image;

                Storage::disk($disk)->delete($originalImage);
            }

            if (!empty($this->id)) {

                Image::updateOrCreate(
                    [
                        'source_type' => self::class,
                        'source_id' => $this->id,
                    ],
                    [
                        'url' => $filePath,
                    ]
                );
            }

        }
    }

    /**
     * @return string
     */
    public function getImageAttribute()
    {
        if (!empty($this->id)) {

            $image = Image::whereSourceType(self::class)->whereSourceId($this->id)->first();

            if (is_null($image)) {
                return null;
            }

            return $image->url;

        } else {
            return null;
        }
    }
}
