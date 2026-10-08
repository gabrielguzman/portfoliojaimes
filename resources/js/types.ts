export type Profile = {name:string;intro:string;bio:string;email?:string;instagram?:string;location?:string;role?:string;brand_subtitle?:string;footer_text?:string;portrait_url?:string;portrait_alt?:string;cv_url?:string;teaching_statement?:string;trajectory?:{period:string;category:string;title:string;description?:string}[]};
export type Work = {id:number;title:string;slug:string;category:string;year:number;cover:string|null;technique:string|null;dimensions?:string|null;excerpt?:string;featured:boolean};

export type ContactContext = {title:string;url:string;subject:string};
