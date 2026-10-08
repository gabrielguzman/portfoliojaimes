import SiteHead from '../Components/SiteHead';
import { Link } from '@inertiajs/react';
import Layout from '../Components/Layout';
import { useCopy } from '../content';
import type { Profile, Work } from '../types';

export default function Portfolio({profile,projects}:{profile:Profile|null;projects:Work[]}) {
 const copy=useCopy('home'),teaching=useCopy('teaching'),about=useCopy('about');
 const focus=projects[0];
 return <Layout profile={profile} active="/">
  <SiteHead title={copy.menu_label}><meta name="description" content={copy.seo_description || profile?.intro || 'Portfolio de artes visuales.'}/></SiteHead>
  <section className="portfolio-opening">
   <div className="opening-work">{focus?<>
    <Link className="opening-feature" href={`/proyectos/${focus.slug}`} aria-labelledby="opening-work-title"><figure>
     <div className="opening-image">{focus.cover?<img src={focus.cover} alt={focus.title} fetchPriority="high"/>:<div className="empty-cover">{focus.title}</div>}</div>
     <figcaption><div><span className="eyebrow">{copy.focus_label}</span><h2 id="opening-work-title">{focus.title}</h2><span className="opening-work-details">{focus.category} · {focus.year}</span></div><span className="opening-project-link">Ver proyecto</span></figcaption>
    </figure></Link>
    {focus.cover?.includes('/demo/')&&<p className="opening-demo">Imagen de demostración; no es una obra de Romina.</p>}
   </>:<div className="entry-no-work"><span className="eyebrow">{copy.selection_label}</span><h2>{copy.heading} {copy.accent}</h2><p>Las obras estarán disponibles próximamente.</p></div>}</div>
   <div className="opening-introduction"><span className="eyebrow">{copy.eyebrow}</span><h1>{profile?.name || 'Romina Elizabeth Jaimes'}</h1><p className="opening-role">{profile?.role || 'Profesora de artes visuales'}</p><p className="opening-intro">{profile?.intro}</p><Link className="action-primary" href="/obra">{copy.all_works_cta}</Link><Link className="text-link opening-contact" href="/contacto">{copy.contact_cta}</Link></div>
  </section>
  {projects.length>1&&<section className="portfolio-selection" aria-labelledby="more-work-heading"><div className="selection-heading"><h2 id="more-work-heading">{copy.selection_title}</h2><span className="eyebrow">{copy.selection_label}</span></div><div className="selection-list">{projects.slice(1).map(project=><Link href={`/proyectos/${project.slug}`} key={project.id}>{project.cover&&<img src={project.cover} alt="" loading="lazy"/>}<div><h3>{project.title}</h3><p>{project.category} · {project.year}</p>{project.cover?.includes('/demo/')&&<p className="selection-demo">Demostración</p>}</div></Link>)}</div></section>}
  <nav className="portfolio-paths" aria-label="Otros recorridos"><Link href="/docencia"><h2>{teaching.menu_label}</h2><p>{copy.docencia_description}</p><span>Explorar docencia</span></Link><Link href="/sobre-mi"><h2>{about.menu_label}</h2><p>{copy.about_description}</p><span>Conocer a Romina</span></Link></nav>
 </Layout>;
}
