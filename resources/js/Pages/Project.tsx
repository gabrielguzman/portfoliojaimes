import SiteHead from '../Components/SiteHead';
import { Link } from '@inertiajs/react';
import { useState } from 'react';
import ImageViewer from '../Components/ImageViewer';
import Layout from '../Components/Layout';
import type { Profile } from '../types';
type Image = {original?:string;url:string;caption:string|null;alt:string};
type Project = {section:string;title:string;description:string;category:string;year:number;technique:string|null;dimensions:string|null;cover:string|null;cover_original:string|null;images:Image[]};
export default function ProjectPage({project:p,profile,preview,collectionUrl,previous,next,position,total}:{project:Project;profile:Profile|null;preview:boolean;collectionUrl:string;previous:{title:string;url:string}|null;next:{title:string;url:string}|null;position:number|null;total:number}) {
 const images:Image[]=p.images.length?p.images:(p.cover?[{url:p.cover,original:p.cover_original || p.cover,caption:p.title,alt:p.title}]:[]);
 const [selectedIndex,setSelectedIndex]=useState<number|null>(null);

 return <Layout profile={profile} active={p.section==='teaching'?'/docencia':'/obra'}><SiteHead title={p.title}><meta name="description" content={p.description.slice(0,155)}/>{preview&&<meta name="robots" content="noindex,nofollow"/>}</SiteHead>
 <article className="project-page"><Link className="text-link" href={collectionUrl}>← {p.section==='teaching'?'Volver a docencia':'Volver a la obra'}</Link>{position&&<span className="project-position">{String(position).padStart(2,'0')} / {String(total).padStart(2,'0')}</span>}<div className="project-spacing"/>{preview&&<div className="preview-banner">Vista previa privada · Revisá el proyecto antes de publicarlo. <a href="/admin/projects">Volver al panel</a></div>}<span className="eyebrow">{p.category} / {p.year}</span><h1>{p.title}</h1><div className="project-intro"><p>{p.description}</p><dl><div><dt>Técnica</dt><dd>{p.technique||'—'}</dd></div><div><dt>Año</dt><dd>{p.year}</dd></div>{p.dimensions&&<div><dt>Medidas</dt><dd>{p.dimensions}</dd></div>}</dl></div>
 {images.length>0&&<div className="project-gallery-heading"><h2>Imágenes del proyecto</h2><span>{images.length} {images.length===1?'imagen':'imágenes'} · Tocá para ampliar</span></div>}<div className="project-gallery">{images.map((img,i)=><figure key={i}><button onClick={()=>setSelectedIndex(i)} aria-label={`Ampliar: ${img.alt}`}><img src={img.url} alt={img.alt} loading={i===0?'eager':'lazy'}/><span>Ampliar</span></button><figcaption className="project-image-caption"><span className="image-caption-index">{String(i+1).padStart(2,'0')} / {String(images.length).padStart(2,'0')}</span><p>{img.caption || img.alt}</p></figcaption></figure>)}</div><Link className="text-link" href={collectionUrl}>← Volver a la colección</Link>{!preview&&(previous||next)&&<nav className="project-pagination" aria-label="Recorrer proyectos">{previous?<Link href={previous.url}><span className="eyebrow">Anterior</span><strong>{previous.title}</strong><span aria-hidden="true">←</span></Link>:<div/>}{next?<Link href={next.url}><span className="eyebrow">Siguiente</span><strong>{next.title}</strong><span aria-hidden="true">→</span></Link>:<div/>}</nav>}</article>
 <ImageViewer images={images} title={p.title} selectedIndex={selectedIndex} onSelect={setSelectedIndex}/>
 </Layout>;
}
