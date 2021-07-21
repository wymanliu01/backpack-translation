<?php

namespace App\Traits;

use App\Models\Image;
use Storage;
use Str;

/**
 * Trait HasImageModel
 * @package App\Traits
 */
trait HasImageModel
{
    /**
     * @param $value
     * @noinspection PhpUnused
     */
    public function setImageAttribute($value)
    {
        $disk = config('filesystems.default');

        // 1.
        // when $value is a url, means $value passed the original url from cms, do nothing

        // 2.
        // when $value is a base64 string, cms update or create a new image,
        // do upload file and update/create record, then delete the original file
        if (!is_null(config("filesystems.disks.$disk.root")) && Str::before($value, '/') === 'data:image') {

            $extension = Str::after(Str::before($value, ';'), '/');
            $filename = md5($value . time()) . ".$extension";
            $filePath = "images/$this->table/$filename";

            $imageBase64 = base64_decode(Str::after($value, ','));

            Storage::disk($disk)->put($filePath, $imageBase64);

            if (!empty($this->image)) {
                $originalImage = $this->image;
                Storage::disk($disk)->delete($originalImage);
            }

            if (!empty($this->id)) {
                Image::updateOrCreate(
                    ['source_type' => self::class, 'source_id' => $this->id],
                    ['url' => $filePath]
                );
            }
        }

        // 3.
        // if $value is empty or is null, means cms didnt pass the photo or photo has been removed
        // do delete file and record if the original file and record are found
        if (empty($value)) {
            if (!empty($this->image)) {
                $originalImage = Str::after($this->image, config("filesystems.disks.$disk.url") . '/');
                Storage::disk($disk)->delete($originalImage);
            }

            Image::whereSourceType(self::class)->whereSourceId($this->id)->delete();
        }
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getImageAttribute(): ?string
    {
        if (!empty($this->id)) {

            $image = Image::whereSourceType(self::class)->whereSourceId($this->id)->first();

            if (is_null($image)) {
                return null;
            }

            $disk = config('filesystems.default');
            return config("filesystems.disks.$disk.url") . "/$image->url";

        } else {
            return null;
        }
    }

}
