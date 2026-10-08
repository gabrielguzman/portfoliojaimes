export default function SectionDirectory({ label, items }: {
 label: string;
 items: { href: string; label: string }[];
}) {
 return <nav className="section-directory" aria-label={label}>
  {items.map((item) => <a key={item.href} href={item.href}>
   {item.label}
  </a>)}
 </nav>;
}
