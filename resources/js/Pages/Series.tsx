import { Link } from '@inertiajs/react';
import Layout from '../Components/Layout';
import SiteHead from '../Components/SiteHead';
import Gallery from '../Components/Gallery';
import type { Profile,Work } from '../types';
export default function Series({profile,series,projects,seriesSlug}:{profile:Profile|null;series:{title:string;description:string};projects:Work[];seriesSlug:string}){
 return <Layout profile={profile} active="/obra"><SiteHead title={series.title}><meta name="description" content={series.description.slice(0,155)}/></SiteHead><section className="editorial-intro"><Link className="text-link" href="/series">← Series y colecciones</Link><span className="eyebrow">COLECCIÓN · {projects.length} {projects.length===1?'OBRA':'OBRAS'}</span><h1>{series.title}</h1><p className="curatorial-text">{series.description}</p></section><section className="works collection" aria-label={`Obras de ${series.title}`}><Gallery projects={projects} seriesSlug={seriesSlug}/></section><aside className="collection-end"><Link href="/series">Seguir recorriendo<span>Ver todas las colecciones</span></Link></aside></Layout>;
}
