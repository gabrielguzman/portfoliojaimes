import { useEffect, useRef, useState } from 'react';
import { imageSwipeOffset, type TouchPoint } from '../imageGestures';

type Image = { original?: string; url: string; caption: string | null; alt: string };

function ViewerImage({ image, onSwipe }: { image: Image; onSwipe: (offset: number) => void }) {
 const [zoomed,setZoomed]=useState(false);
 const [status,setStatus]=useState<'loading'|'ready'|'error'>('loading');
 const [attempt,setAttempt]=useState(0);
 const [detailWidth,setDetailWidth]=useState<number|null>(null);
 const zoomButton=useRef<HTMLButtonElement>(null);
 const imageElement=useRef<HTMLImageElement>(null);
 const stage=useRef<HTMLDivElement>(null);
 const touchStart=useRef<TouchPoint|null>(null);
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
  <div className="viewer-image-toolbar"><button ref={zoomButton} type="button" onClick={()=>{if(!zoomed)setDetailWidth((imageElement.current?.getBoundingClientRect().width||0)*2);setZoomed(!zoomed);}} aria-pressed={zoomed} disabled={status!=='ready'}>{zoomed?'Ver imagen completa':'Ampliar detalle'}</button>{zoomed&&<span id="viewer-detail-help">Desplazate para recorrer los detalles.</span>}</div>
  <div className={zoomed?'viewer-stage viewer-stage-zoomed':'viewer-stage'} ref={stage} role="region" aria-label={zoomed?'Detalle ampliado de la obra':'Imagen completa de la obra'} aria-describedby={zoomed?'viewer-detail-help':undefined} tabIndex={zoomed?0:undefined} aria-busy={status==='loading'} onTouchStart={event=>{touchStart.current=!zoomed&&event.touches.length===1?{x:event.touches[0].clientX,y:event.touches[0].clientY}:null;}} onTouchMove={event=>{if(event.touches.length!==1)touchStart.current=null;}} onTouchEnd={event=>{const start=touchStart.current;touchStart.current=null;if(!start||zoomed||event.changedTouches.length!==1)return;const offset=imageSwipeOffset(start,{x:event.changedTouches[0].clientX,y:event.changedTouches[0].clientY});if(offset)onSwipe(offset);}} onTouchCancel={()=>{touchStart.current=null;}} onKeyDown={event=>{if(zoomed&&event.key.startsWith('Arrow'))event.stopPropagation();}}>
   {status==='loading'&&<div className="viewer-image-status" role="status">Cargando imagen…</div>}
   {status==='error'&&<div className="viewer-image-error" role="alert"><p>No se pudo cargar esta imagen.</p><button type="button" onClick={()=>{setStatus('loading');setAttempt(value=>value+1);}}>Volver a intentar</button></div>}
   <img key={attempt} ref={imageElement} src={image.original || image.url} alt={image.alt} style={zoomed&&detailWidth?{width:detailWidth}:undefined} className={status==='ready'?'viewer-image-ready':'viewer-image-pending'} onLoad={()=>setStatus('ready')} onError={()=>setStatus('error')}/>
  </div>
 </>;
}

export default function ImageViewer({ images, title, selectedIndex, onSelect, details = [] }: {
 images: Image[];
 details?: {label:string;value:string}[];
 title: string;
 selectedIndex: number | null;
 onSelect: (index: number | null) => void;
}) {
 const dialog = useRef<HTMLDialogElement>(null);
 const [showDetails,setShowDetails]=useState(false);
 const isOpen = selectedIndex !== null;
 const selected = selectedIndex === null ? null : images[selectedIndex];
 useEffect(()=>{if(selectedIndex===null||images.length<2)return;const neighbors=[images[(selectedIndex+1)%images.length],images[(selectedIndex-1+images.length)%images.length]];const requests=[...new Set(neighbors.map(image=>image.original || image.url))].map(source=>{const image=new window.Image();image.src=source;return image;});return()=>{requests.forEach(image=>{image.src='';});};},[selectedIndex,images]);
 const step = (offset: number) => {
  if (selectedIndex !== null && images.length > 1) {
   onSelect((selectedIndex + offset + images.length) % images.length);
  }
 };

 useEffect(() => {
  if (!isOpen) return;
  const previousOverflow = document.body.style.overflow;
  const previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
  setShowDetails(false);
  document.body.style.overflow = 'hidden';
  dialog.current?.showModal();
  return () => {
   dialog.current?.close();
   document.body.style.overflow = previousOverflow;
   previousFocus?.focus({preventScroll:true});
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
  <div className="viewer-top"><span>{title}</span><span className="viewer-count" aria-label={`Imagen ${(selectedIndex ?? 0)+1} de ${images.length}`} aria-live="polite" aria-atomic="true">{(selectedIndex ?? 0)+1} / {images.length}</span><button onClick={() => onSelect(null)} autoFocus aria-label="Cerrar imagen ampliada">Cerrar ×</button></div>
  {selected && <>
   <ViewerImage key={`${selectedIndex}-${selected.original || selected.url}`} image={selected} onSwipe={step}/>
   <div className="viewer-bottom">
    <button className="viewer-details-toggle" onClick={()=>setShowDetails(!showDetails)} aria-expanded={showDetails} aria-controls="viewer-details">{showDetails?'Ocultar ficha':'Ver ficha de la obra'} <span aria-hidden="true">{showDetails?'−':'+'}</span></button>
    {images.length > 1 && <div className="viewer-controls"><button onClick={() => step(-1)} aria-label="Imagen anterior"><span aria-hidden="true">←</span><span className="viewer-control-label">Anterior</span></button><button onClick={() => step(1)} aria-label="Imagen siguiente"><span className="viewer-control-label">Siguiente</span><span aria-hidden="true">→</span></button></div>}
   </div>
   <section id="viewer-details" className="viewer-details" hidden={!showDetails} aria-label="Ficha de la obra"><p aria-live="polite">{selected.caption || selected.alt}</p>{details.length>0&&<dl>{details.map(detail=><div key={detail.label}><dt>{detail.label}</dt><dd>{detail.value}</dd></div>)}</dl>}</section>
   {images.length > 1 && <div className="viewer-thumbnails" role="group" aria-label="Elegir imagen">{images.map((image, index) => <button key={index} onClick={() => onSelect(index)} aria-label={`Ver imagen ${index + 1}: ${image.caption || image.alt}`} aria-pressed={selectedIndex === index}><img src={image.url} alt="" loading="lazy"/><span>{String(index + 1).padStart(2, '0')}</span></button>)}</div>}
   {images.length > 1 && <p className="viewer-hint">Flechas del teclado o deslizá la imagen para recorrer. Escape para cerrar.</p>}
  </>}
 </dialog>;
}
