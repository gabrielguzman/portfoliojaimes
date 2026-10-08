import { Head, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import type { Profile } from '../types';
export default function SiteHead({title,children}:{title:string;children?:ReactNode}) {
 const profile=usePage<{profile:Profile|null}>().props.profile;
 return <Head title={`${title} · ${profile?.name || 'Estudio visual'}`}>{children}</Head>;
}
