import { usePage } from '@inertiajs/react';
export function useCopy(key:string):Record<string,string> {
 const site=usePage<{site:Record<string,Record<string,string|number|null>>}>().props.site;
 return Object.fromEntries(Object.entries(site?.[key] ?? {}).map(([field,value])=>[field,String(value??'')]));
}

export function sectionLabel(value:string):string {
 return value.replace(/^\s*\d{1,2}\s*\/\s*/, '');
}
