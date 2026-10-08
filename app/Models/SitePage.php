<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SitePage extends Model {
 protected $fillable=['content','seo_description'];
 protected function casts(): array {return ['content'=>'array'];}
 public function getTitleAttribute(): string { return config('site_content.'.$this->key.'.label',$this->key); }
 public function getPathAttribute(): string { return config('site_content.'.$this->key.'.path','/'); }
 public static function publicContent(): array {
  $saved=static::all()->keyBy('key');$result=[];
  foreach(config('site_content') as $key=>$page) {
   $record=$saved->get($key);
   $result[$key]=array_replace($page['defaults'],$record?->content??[],['seo_description'=>$record?->seo_description]);
  }
  return $result;
 }
}
