import { useEffect, useState } from 'react';

export default function SectionDirectory({ label, items }: {
 label: string;
 items: { href: string; label: string }[];
}) {
 const [active, setActive] = useState(items[0]?.href);
 const itemIds = items.map(item => item.href.slice(1)).join('|');
 useEffect(() => {
  const sections = itemIds.split('|').map(id => document.getElementById(id)).filter((element): element is HTMLElement => element !== null);
  const update = () => {
   const current = sections.filter(section => section.getBoundingClientRect().top <= window.innerHeight * .35).at(-1) || sections[0];
   if (current) setActive(`#${current.id}`);
  };
  update();
  window.addEventListener('scroll', update, { passive: true });
  return () => window.removeEventListener('scroll', update);
 }, [itemIds]);
 return <nav className="section-directory" aria-label={label}>
  {items.map((item) => <a key={item.href} href={item.href} aria-current={active === item.href ? 'location' : undefined}>
   {item.label}
  </a>)}
 </nav>;
}
