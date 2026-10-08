import { Link } from '@inertiajs/react';
import type { Work } from '../types';
export default function Gallery({projects,filters=false,activeCategory,collectionHref='/obra',teaching=false}:{projects:Work[];filters?:boolean;activeCategory?:string|null;collectionHref?:string;teaching?:boolean}) {
 const category=activeCategory || 'Todas';
 const suffix=category==='Todas'?'':`?disciplina=${encodeURIComponent(category)}`;
 const categories=['Todas',...new Set(projects.map(p=>p.category))];
 const visible=projects.filter(p=>category==='Todas'||p.category===category);
 return <>{filters&&<div className="collection-toolbar"><div className="filters" aria-label="Filtrar por disciplina">{categories.map(c=><Link key={c} href={collectionHref+(c==='Todas'?'':`?disciplina=${encodeURIComponent(c)}`)} preserveScroll aria-current={category===c?'true':undefined} className={category===c?'active':''}>{c}{' '}<span>{c==='Todas'?projects.length:projects.filter(p=>p.category===c).length}</span></Link>)}</div><div className="collection-summary"><p className="collection-result" role="status">{visible.length} {visible.length===1?'proyecto':'proyectos'}{category!=='Todas'?` en ${category}`:' en la colección'}</p>{category!=='Todas'&&<Link href={collectionHref} preserveScroll className="collection-reset">Ver todas las disciplinas <span aria-hidden="true">×</span></Link>}</div></div>}
 {projects.some(p=>p.cover?.includes("/demo/"))&&<p className="demo-note">VISTA DE DEMOSTRACIÓN · Estas composiciones ilustran el diseño y no son obras de Romina.</p>}
 <div className={teaching?'gallery teaching-journal':visible.length===1?'gallery gallery-single':'gallery gallery-editorial'}>{visible.map((p,i)=><Link id={`proyecto-${p.slug}`} className="work" aria-labelledby={`work-title-${p.id}`} href={`/proyectos/${p.slug}${suffix}`} key={p.id}>
 <figure className="exhibition-work"><div className="work-image">{p.cover?<img src={p.cover} alt={p.title} loading={i<2?'eager':'lazy'}/>:<div className="empty-cover">{p.title}</div>}</div>
 <figcaption className="exhibition-card"><div className="work-caption"><h3 id={`work-title-${p.id}`}>{p.title}</h3><span className="work-year">{p.year}</span></div>
 <p className="work-medium">{p.technique || p.category}</p>
 {teaching&&p.excerpt&&<p className="journal-note">{p.excerpt}</p>}
 </figcaption></figure></Link>)}</div>
 {projects.length===0&&<p className="empty">Próximamente, nuevas obras y proyectos.</p>}
 </>;
}
