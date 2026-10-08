<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class PortfolioImages {
    public function optimize(?string $path, int $size): ?string {
        if (!$path || !in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'])) return null;
        $disk=Storage::disk('public');
        if (!$disk->exists($path)) return null;
        $contents=$disk->get($path);
        $dimensions=@getimagesizefromstring($contents);
        // Large originals remain intact; avoid exhausting memory during synchronous uploads.
        if (!$dimensions || $dimensions[0]*$dimensions[1]>20000000) return null;
        $target='optimized/'.hash('sha256',$contents).'-'.$size.'.webp';
        if (!$disk->exists($target)) {
            $image=(new ImageManager(new Driver()))->read($contents)->scaleDown(width:$size,height:$size);
            $disk->put($target,(string)$image->toWebp(quality:85));
        }
        return $target;
    }
}
