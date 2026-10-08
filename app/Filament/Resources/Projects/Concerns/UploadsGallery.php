<?php
namespace App\Filament\Resources\Projects\Concerns;
trait UploadsGallery {
    protected array $galleryUploads=[];
    protected function takeGalleryUploads(array $data): array {
        $this->galleryUploads=$data['bulk_images']??[];
        unset($data['bulk_images']);
        return $data;
    }
    protected function appendGalleryUploads(): void {
        $record=$this->getRecord();
        $order=(int)$record->images()->max('sort_order');
        foreach($this->galleryUploads as $path) {
            $order++;
            $record->images()->create(['path'=>$path,'alt'=>mb_substr($record->title,0,210).' — imagen '.$order,'sort_order'=>$order]);
        }
        $this->galleryUploads=[];
    }
}
