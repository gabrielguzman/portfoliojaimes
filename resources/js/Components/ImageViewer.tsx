import { useEffect, useRef, useState } from 'react';

type Image = { original?: string; url: string; caption: string | null; alt: string };

function ViewerImage({ image }: { image: Image }) {
 const [zoomed,setZoomed]=useState(false);
 const [status,setStatus]=useState<'loading'|'ready'|'error'>('loading');
 const [attempt,setAttempt]=useState(0);
 const [detailWidth,setDetailWidth]=useState<number|null>(null);
 const zoomButton=useRef<HTMLButtonElement>(null);
 const imageElement=useRef<HTMLImageElement>(null);
 const stage=useRef<HTMLDivElement>(null);
 useEffect(()=>{
  if(imageElement.current?.complete&&imageElement.current.naturalWidth>0)setStatus('ready');
 },[attempt]);
 useEffect(()=>{
  if(!zoomed||!stage.current)return;
  stage.current.focus({preventScroll:true});
  stage.current.scrollTo({left:(stage.current.scrollWidth-stage.current.clientWidth)/2,top:(stage.current.scrollHeight-stage.current.clientHeight)/2});
 },[zoomed]);
 useEffect(()=>{if(status==='ready'&&attempt>0)zoomButton.current?.focus({preventScroll:true});},[status,attempt]);

 return <>
  <div className="viewer-image-toolbar"><button ref={zoomButton} type="button" onClick={()=>{if(!zoomed)setDetailWidth((imageElement.current?.getBoundingClientRect().width||0)*2);setZoomed(!zoomed);}} aria-pressed={zoomed} disabled={status!=='ready'}>{zoomed?'Ver imagen completa':'Ampliar detalle'}</button><span id="viewer-detail-help">{zoomed?'Desplazate dentro de la imagen para recorrer los detalles.':'La imagen se muestra completa, sin recortes.'}</span></div>
  <div className={zoomed?'viewer-stage viewer-stage-zoomed':'viewer-stage'} ref={stage} role="region" aria-label={zoomed?'Detalle ampliado de la obra':'Imagen completa de la obra'} aria-describedby="viewer-detail-help" tabIndex={zoomed?0:undefined} aria-busy={status==='loading'} onKeyDown={event=>{if(zoomed&&event.key.startsWith('Arrow'))event.stopPropagation();}}>
   {status==='loading'&&<div className="viewer-image-status" role="status">Cargando imagen…</div>}
   {status==='error'&&<div className="viewer-image-error" role="alert"><p>No se pudo cargar esta imagen.</p><button type="button" onClick={()=>{setStatus('loading');setAttempt(value=>value+1);}}>Volver a intentar</button></div>}
   <img key={attempt} ref={imageElement} src={image.original || image.url} alt={image.alt} style={zoomed&&detailWidth?{width:detailWidth}:undefined} className={status==='ready'?'viewer-image-ready':'viewer-image-pending'} onLoad={()=>setStatus('ready')} onError={()=>setStatus('error')}/>
  </div>
 </>;
}

export default function ImageViewer({ images, title, selectedIndex, onSelect }: {
 images: Image[];
 title: string;
 selectedIndex: number | null;
 onSelect: (index: number | null) => void;
}) {
 const dialog = useRef<HTMLDialogElement>(null);
 const isOpen = selectedIndex !== null;
 const selected = selectedIndex === null ? null : images[selectedIndex];
 const step = (offset: number) => {
  if (selectedIndex !== null && images.length > 1) {
   onSelect((selectedIndex + offset + images.length) % images.length);
  }
 };

 useEffect(() => {
  if (!isOpen) return;
  const previousOverflow = document.body.style.overflow;
  document.body.style.overflow = 'hidden';
  dialog.current?.showModal();
  return () => {
   dialog.current?.close();
   document.body.style.overflow = previousOverflow;
  };
 }, [isOpen]);

 return <dialog className="art-viewer" ref={dialog}
  onCancel={() => onSelect(null)}
  onClick={event => { if (event.target === event.currentTarget) onSelect(null); }}
  onKeyDown={event => {
   if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
    event.preventDefault();
    step(event.key === 'ArrowRight' ? 1 : -1);
   }
  }} aria-label={`Galería de ${title}`}>
  <div className="viewer-top"><span>{title}</span><button onClick={() => onSelect(null)} autoFocus aria-label="Cerrar imagen ampliada">Cerrar ×</button></div>
  {selected && <>
   <ViewerImage key={`${selectedIndex}-${selected.original || selected.url}`} image={selected}/>
   <div className="viewer-bottom">
    <div aria-live="polite" aria-atomic="true"><span className="viewer-count">Imagen {(selectedIndex ?? 0) + 1} de {images.length}</span><p>{selected.caption || selected.alt}</p></div>
    {images.length > 1 && <div className="viewer-controls"><button onClick={() => step(-1)} aria-label="Imagen anterior">←</button><button onClick={() => step(1)} aria-label="Imagen siguiente">→</button></div>}
   </div>
   {images.length > 1 && <div className="viewer-thumbnails" role="group" aria-label="Elegir imagen">{images.map((image, index) => <button key={index} onClick={() => onSelect(index)} aria-label={`Ver imagen ${index + 1}: ${image.caption || image.alt}`} aria-pressed={selectedIndex === index}><img src={image.url} alt="" loading="lazy"/><span>{String(index + 1).padStart(2, '0')}</span></button>)}</div>}
   {images.length > 1 && <p className="viewer-hint">Usá las flechas del teclado para recorrer las imágenes. Escape para cerrar.</p>}
  </>}
 </dialog>;
}
