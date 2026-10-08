import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot, hydrateRoot } from 'react-dom/client';
const pages = import.meta.glob('./Pages/*.tsx', { eager: true });
createInertiaApp({
    title: title => title,
    defaults: { visitOptions: (_href, options) => ({viewTransition: options.viewTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches}) },
    resolve: name => (pages[`./Pages/${name}.tsx`] as {default: any}).default,
    setup({ el, App, props }) { el.hasChildNodes() ? hydrateRoot(el, <App {...props} />) : createRoot(el).render(<App {...props} />); },
});
