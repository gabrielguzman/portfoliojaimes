import SiteHead from '../Components/SiteHead';
import { Link } from '@inertiajs/react';
import ContactForm from '../Components/ContactForm';
import SectionDirectory from '../Components/SectionDirectory';
import Layout from '../Components/Layout';
import { useCopy, sectionLabel } from '../content';
import type { Profile } from '../types';
export default function Information({profile,page,contactReceived=false}:{profile:Profile|null;contactReceived?:boolean;page:'about'|'contact'}) {
 const copy=useCopy(page);
 const name=profile?.name || 'Romina Elizabeth Jaimes';
 return <Layout profile={profile} active={page==='about'?'/sobre-mi':'/contacto'}>
 <SiteHead title={copy.menu_label}><meta name="description" content={copy.seo_description || (page==='about'?`Conocé a ${name}, profesora de artes visuales.`:`Contacto y propuestas para ${name}.`)}/></SiteHead>
 {page==='about'?<>
 <section className="biography-hero"><div className="biography-title"><span className="eyebrow">{sectionLabel(copy.eyebrow)}</span><h1>{copy.heading}<br/><em>{copy.accent}</em></h1></div>{profile?.portrait_url?<figure className="profile-portrait"><img src={profile.portrait_url} alt={profile.portrait_alt || name}/><figcaption className="biography-name">{name}<span>{profile?.role || 'Profesora de artes visuales'}</span></figcaption></figure>:<div className="profile-nameplate personal-introduction"><p className="personal-name">{name}</p><div><p className="nameplate-role">{profile?.role || 'Profesora de artes visuales'}</p>{profile?.location&&<p className="nameplate-location">{profile.location}</p>}</div></div>}</section>
 <SectionDirectory label="Contenido de Sobre mí" items={[{href:'#presentacion',label:'Presentación'},...((profile?.trajectory?.length??0)>0?[{href:'#trayectoria',label:'Trayectoria'}]:[]),{href:'#recorridos',label:'Obra y docencia'}]}/>
 <section className="biography-body" id="presentacion" aria-labelledby="biography-presentation-heading"><div><span className="eyebrow">{copy.presentation_label}</span><h2 id="biography-presentation-heading">{copy.presentation_heading}<br/><em>{copy.presentation_accent}</em></h2></div><div><p className="biography-intro">{profile?.intro || 'Un espacio para compartir obras, procesos y experiencias en torno a las artes visuales.'}</p><p className="bio">{profile?.bio || 'La biografía y la presentación de su práctica artística estarán disponibles próximamente.'}</p>{profile?.cv_url&&<a className="text-link" href={profile.cv_url}>{copy.cv_cta} <span>↓</span></a>}{profile?.location&&<p className="biography-location">Desde {profile.location}</p>}</div></section>
 {(profile?.trajectory?.length??0)>0&&<section className="trajectory" id="trayectoria" aria-labelledby="biography-trajectory-heading"><div><span className="eyebrow">{copy.trajectory_label}</span><h2 id="biography-trajectory-heading">{copy.trajectory_heading}</h2></div><ol>{profile?.trajectory?.map((entry,index)=><li key={index}><div className="trajectory-period">{entry.period}<span>{entry.category}</span></div><div><h3>{entry.title}</h3>{entry.description&&<p>{entry.description}</p>}</div></li>)}</ol></section>}
 <section className="about-paths" id="recorridos" aria-label="Continuar hacia Obra o Docencia"><Link href="/obra"><span className="eyebrow">{sectionLabel(copy.art_path_label)}</span><h2>{copy.art_path_heading} </h2><p>{copy.art_path_description}</p></Link><Link href="/docencia"><span className="eyebrow">{sectionLabel(copy.teaching_path_label)}</span><h2>{copy.teaching_path_heading} </h2><p>{copy.teaching_path_description}</p></Link></section>
 <div className="home-contact"><p>{copy.contact_invitation}</p><Link className="action-primary action-light" href="/contacto">{copy.contact_cta} </Link></div>
 </>:<>
 <section className="contact-editorial"><span className="eyebrow">{sectionLabel(copy.eyebrow)}</span><div className="contact-heading"><h1>{copy.heading}<br/><em>{copy.accent}</em></h1></div><div className="contact-content"><div className="contact-invitation"><p>{copy.description}</p><span>{copy.greeting} <em>{copy.greeting_accent}</em></span>{profile?.location&&<p className="contact-location">{profile.location}</p>}</div><div className="contact-channels"><a className="action-primary contact-write-link" href="#mensaje">Escribir una consulta</a>
 <div className="contact-channel"><span className="channel-label">CORREO</span>{profile?.email?<a href={`mailto:${profile.email}`}><span>{profile.email}</span></a>:<div className="channel-unavailable">{copy.email_unavailable}</div>}<p>{copy.email_description}</p></div>
 <div className="contact-channel"><span className="channel-label">INSTAGRAM</span>{profile?.instagram?<a href={profile.instagram} target="_blank" rel="noreferrer"><span>{copy.instagram_cta}</span></a>:<div className="channel-unavailable">{copy.instagram_unavailable}</div>}<p>{copy.instagram_description}</p></div>
 </div></div></section><ContactForm received={contactReceived}/><div className="contact-signoff"><span>{copy.signoff}</span><Link href="/obra">{copy.return_cta}</Link></div>
 </>}
 </Layout>;
}
