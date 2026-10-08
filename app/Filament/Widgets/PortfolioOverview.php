<?php
namespace App\Filament\Widgets;
use App\Models\{Project,ProjectImage};
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class PortfolioOverview extends StatsOverviewWidget {
 protected static ?int $sort=1;
 protected ?string $heading='Tu portfolio en números';
 protected function getStats(): array {
  return [
   Stat::make('Proyectos publicados',Project::where('published',true)->count())->description('Visibles en el sitio')->color('success')->url('/admin/projects'),
   Stat::make('Borradores',Project::where('published',false)->count())->description('Pendientes de publicar')->color('warning')->url('/admin/projects'),
   Stat::make('Proyectos en papelera',Project::onlyTrashed()->count())->description('Recuperables como borrador')->url('/admin/projects'),
  ];
 }
}
