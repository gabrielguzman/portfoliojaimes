import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { renderToString } from 'react-dom/server';
const pages = import.meta.glob('./Pages/*.tsx', { eager: true });
createServer(page => createInertiaApp({page,render:renderToString,title:title=>title,resolve:name=>(pages[`./Pages/${name}.tsx`] as {default:any}).default,setup:({App,props})=><App {...props}/> }));
