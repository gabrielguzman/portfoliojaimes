import { Form } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import { useCopy } from '../content';

export default function ContactForm({received}:{received:boolean}) {
 const copy=useCopy('contact');
 const [writingAgain,setWritingAgain]=useState(false);
 const confirmation=useRef<HTMLDivElement>(null);
 const showConfirmation=received&&!writingAgain;
 useEffect(()=>{if(showConfirmation)confirmation.current?.focus();else if(writingAgain)document.getElementById('contact-name')?.focus();},[showConfirmation,writingAgain]);

 return <section className="contact-form-section" id="mensaje" aria-labelledby="contact-form-heading"><div><span className="eyebrow">{copy.form_label}</span><h2 id="contact-form-heading">{copy.form_heading}</h2><p>{copy.form_description}</p>{!showConfirmation&&<p className="form-required-note">Todos los campos son obligatorios.</p>}</div>
 {showConfirmation?<div className="contact-confirmation" ref={confirmation} tabIndex={-1} role="status"><span className="eyebrow">Consulta recibida</span><h3>Gracias por escribir.</h3><p>Tu mensaje quedó guardado para que Romina pueda leerlo y responder al correo que indicaste.</p><button className="text-link" type="button" onClick={()=>setWritingAgain(true)}>Escribir otra consulta</button></div>:<Form action="/contacto" method="post" resetOnSuccess options={{preserveScroll:true}} onSuccess={()=>setWritingAgain(false)} onError={errors=>document.getElementById(`contact-${Object.keys(errors)[0]}`)?.focus()}>
 {({errors,processing,hasErrors})=><>
 {hasErrors&&<p className="form-error-summary" role="alert">Revisá los campos señalados para poder enviar tu consulta.</p>}
 <div className="contact-form-grid">
 <label htmlFor="contact-name"><span id="contact-name-label">Nombre</span><input id="contact-name" aria-labelledby="contact-name-label" name="name" autoComplete="name" required maxLength={120} aria-invalid={!!errors.name} aria-describedby={errors.name?'error-name':undefined}/>{errors.name&&<span id="error-name" className="form-error">{errors.name}</span>}</label>
 <label htmlFor="contact-email"><span id="contact-email-label">Correo electrónico</span><input id="contact-email" aria-labelledby="contact-email-label" name="email" type="email" autoComplete="email" required maxLength={255} aria-invalid={!!errors.email} aria-describedby={`contact-email-hint${errors.email?' error-email':''}`}/><span id="contact-email-hint" className="field-hint">Usaremos este correo para responderte.</span>{errors.email&&<span id="error-email" className="form-error">{errors.email}</span>}</label>
 <label htmlFor="contact-subject" className="full-field"><span id="contact-subject-label">Asunto</span><input id="contact-subject" aria-labelledby="contact-subject-label" name="subject" required maxLength={160} aria-invalid={!!errors.subject} aria-describedby={errors.subject?'error-subject':undefined}/>{errors.subject&&<span id="error-subject" className="form-error">{errors.subject}</span>}</label>
 <label htmlFor="contact-message" className="full-field"><span id="contact-message-label">Mensaje</span><textarea id="contact-message" aria-labelledby="contact-message-label" name="message" required minLength={10} maxLength={5000} rows={6} aria-invalid={!!errors.message} aria-describedby={`contact-message-hint${errors.message?' error-message':''}`}/><span id="contact-message-hint" className="field-hint">Contá tu propuesta o consulta. Entre 10 y 5.000 caracteres.</span>{errors.message&&<span id="error-message" className="form-error">{errors.message}</span>}</label>
 </div>
 <div className="contact-honeypot" aria-hidden="true"><label>Sitio web<input name="website" tabIndex={-1} autoComplete="off"/></label></div>
 <label className="contact-consent"><input id="contact-consent" type="checkbox" name="consent" value="1" required aria-invalid={!!errors.consent} aria-describedby={`contact-privacy${errors.consent?' error-consent':''}`}/>Autorizo que se guarden mis datos para gestionar esta consulta.</label>
 <p id="contact-privacy" className="contact-privacy">{copy.form_privacy_description}</p>
 {errors.consent&&<p id="error-consent" className="form-error">{errors.consent}</p>}
 {errors.website&&<p className="form-error" role="alert">{errors.website}</p>}
 <div className="contact-form-actions"><button type="submit" disabled={processing} className="contact-submit action-primary" aria-busy={processing}>{processing?'Enviando…':'Enviar consulta'}</button><span role="status">{processing?'Estamos guardando tu mensaje.':'La consulta se envía directamente desde este formulario.'}</span></div>
 </>}
 </Form>}</section>;
}
