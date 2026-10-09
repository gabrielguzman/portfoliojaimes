import SiteHead from '../Components/SiteHead';
import { Link } from '@inertiajs/react';
import Layout from '../Components/Layout';
import Gallery from '../Components/Gallery';
import SectionDirectory from '../Components/SectionDirectory';
import { useCopy, sectionLabel } from '../content';
import type { Profile, Work } from '../types';
export default function Collection({profile,projects,section,activeCategory}:{profile:Profile|null;projects:Work[];section:'art'|'teaching';activeCategory?:string|null}) {
 const teaching=section==='teaching'; const copy=useCopy(teaching?'teaching':'art');
 return <Layout profile={profile} active={teaching?'/docencia':'/obra'}>
 <SiteHead title={copy.menu_label}><meta name="description" content={copy.seo_description || (teaching?'Proyectos educativos, talleres y procesos de aprendizaje de Romina Elizabeth Jaimes.':'Explorá las obras y proyectos de artes visuales de Romina Elizabeth Jaimes.')}/></SiteHead>
 <section className={`collection-intro ${teaching?'teaching-intro':'art-intro'}`}>
 <div><span className="eyebrow">{sectionLabel(copy.eyebrow)}</span><h1>{copy.heading}<br/><em>{copy.accent}</em></h1></div>
 <div className="collection-intro-note"><p>{copy.description}</p><span className="collection-index">{projects.length} {teaching?'EXPERIENCIAS':'PROYECTOS'} / {teaching?'ARTE & APRENDIZAJE':'COLECCIÓN'}</span></div>
 </section>
 <nav className="editorial-paths" aria-label="Más recorridos">{teaching?<Link href="/docencia/materiales">Materiales docentes <span>Guías y propuestas para descargar</span></Link>:<Link href="/series">Series y colecciones <span>Obras que comparten una investigación</span></Link>}<Link href="/agenda">Exposiciones y agenda <span>Encuentros, talleres y archivo</span></Link></nav>
 {teaching&&<SectionDirectory label="Contenido de Docencia" items={[{href:'#enfoque',label:'Enfoque'},{href:'#experiencias',label:'Experiencias'},{href:'#propuestas',label:'Propuestas'}]}/>}
 {teaching&&<section className="teaching-approach teaching-notebook" id="enfoque" aria-labelledby="teaching-approach-heading"><div className="section-heading"><div><span className="eyebrow">{copy.approach_label}</span><h2 id="teaching-approach-heading">{copy.approach_heading}</h2></div><p>{profile?.teaching_statement || copy.approach_description}</p></div><div className="teaching-pillars">{[1,2,3].map(number=><article key={number}><h3>{copy[`pillar_${number}_heading`]}</h3><p>{copy[`pillar_${number}_description`]}</p></article>)}</div></section>}
 <section id={teaching?'experiencias':'coleccion'} className="works collection curated-collection" aria-label={teaching?'Experiencias docentes':'Colección de obras'}>{projects.length>0?<Gallery projects={projects} teaching={teaching} filters activeCategory={activeCategory} collectionHref={teaching?'/docencia':'/obra'}/>:<div className="collection-empty editorial-empty"><div className="empty-collection-label"><span className="eyebrow">{copy.empty_label}</span></div><div><h2>{copy.empty_heading}</h2><p>{copy.empty_description}</p><Link href={teaching?'/obra':'/sobre-mi'} className="text-link">{copy.empty_cta} </Link></div></div>}</section>
 {teaching?<section id="propuestas" className="section-invitation" aria-labelledby="teaching-proposals-heading"><div><span className="eyebrow">{copy.proposals_label}</span><h2 id="teaching-proposals-heading">{copy.proposals_heading}</h2></div><div><p>{copy.proposals_description}</p><Link href="/contacto" className="action-primary action-light">{copy.proposals_cta} </Link></div></section>:<section className="section-invitation"><div><span className="eyebrow">{copy.process_label}</span><h2>{copy.process_heading}</h2></div><div><p>{copy.process_description}</p><Link href="/sobre-mi" className="text-link">{copy.process_cta} </Link></div></section>}
 <aside className="collection-end"><Link href={teaching?'/obra':'/docencia'}>{copy.continue_heading}<span> {copy.continue_cta}</span></Link></aside>
 </Layout>;
}
