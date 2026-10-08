<x-filament-widgets::widget>
 <x-filament::section heading="Administrar el sitio" description="Elegí qué querés actualizar. Los textos y el perfil se publican al guardar; los proyectos pueden mantenerse como borrador.">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-bottom:24px">
   @foreach($tasks as [$url,$title,$description])
   <a href="{{ $url }}" style="display:block;padding:22px;border:1px solid #9ca3af66;border-radius:12px;border-top:3px solid #64754f">
    <strong style="font-size:18px">{{ $title }}</strong><p style="margin-top:10px;font-size:14px;line-height:1.6">{{ $description }}</p>
   </a>
   @endforeach
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px">
   @foreach([
    ['/admin/profiles','Perfil y contacto','Biografía, trayectoria, enfoque docente, foto y CV.'],
    ['/admin/site-pages','Páginas y textos','Portada, secciones, menú y buscadores.'],
    ['/admin/disciplines','Disciplinas','Organizá las categorías de tus proyectos.'],
    ['/admin/contact-messages','Consultas','Leé los mensajes del sitio y gestioná su seguimiento.'],
   ] as [$url,$title,$description])
   <a href="{{ $url }}" style="display:block;padding:18px;border:1px solid #9ca3af66;border-radius:12px">
    <strong>{{ $title }}</strong><p style="margin-top:8px;font-size:13px;line-height:1.6;opacity:.8">{{ $description }}</p>
   </a>
   @endforeach
  </div>
  <div style="margin-top:26px;display:flex;gap:14px;flex-wrap:wrap"><x-filament::button tag="a" href="/" target="_blank">Ver sitio público</x-filament::button><x-filament::button tag="a" href="/admin/profile" color="gray">Mi cuenta</x-filament::button></div>
  @if(count($pending))
   <div style="margin-top:28px;border-top:1px solid #9ca3af66;padding-top:22px">
    <h3 style="font-weight:600">Para completar tu portfolio</h3>
    <ul style="margin:12px 0 0;padding-left:20px;list-style:disc;line-height:2;font-size:14px">@foreach($pending as $item)<li>{{ $item }}</li>@endforeach</ul>
   </div>
  @endif
 </x-filament::section>
</x-filament-widgets::widget>
