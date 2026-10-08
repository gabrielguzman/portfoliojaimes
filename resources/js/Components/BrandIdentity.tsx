export default function BrandIdentity({name,subtitle}:{name:string;subtitle?:string}) {
 const parts=name.trim().split(/\s+/);
 const surname=parts.length>1?parts.pop():'';
 return <span className="artist-identity">
  <img className="artist-symbol" src="/brand-symbol.svg" alt="" width="72" height="80"/>
  <span className="artist-lettering"><span className="artist-given-name">{parts.join(' ')}</span>{surname&&<span className="artist-surname">{surname}</span>}{subtitle&&<small>{subtitle}</small>}</span>
 </span>;
}
