<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Discipline extends Model {
 protected $fillable=['name'];
 protected static function booted(): void {
  static::updated(function(Discipline $discipline) {
   if($discipline->wasChanged('name')) Project::withTrashed()->where('category',$discipline->getOriginal('name'))->update(['category'=>$discipline->name]);
  });
  static::deleting(function(Discipline $discipline) {
   if(Project::withTrashed()->where('category',$discipline->name)->exists()) throw new \LogicException('No se puede eliminar una disciplina que tiene proyectos.');
  });
 }
 public static function options(): array {return static::orderBy('name')->pluck('name','name')->all();}
}
