# Portfolio de Romina Elizabeth Jaimes

Primera versión funcional: Laravel 13, React 19, TypeScript, Inertia 3 y Filament 5. Interfaz pública en español, filtros por disciplina, detalle de proyecto, ampliación accesible de imágenes, renderizado en servidor y panel privado.

## Ejecutar esta instalación

Desde esta carpeta, en terminales separadas:

```sh
./scripts/serve-local.sh
php artisan inertia:start-ssr
```

Abrir http://127.0.0.1:8013. El panel está en `/admin`. La cuenta local está en `ACCESO-LOCAL.md`, excluido de Git. Para trabajar en los estilos o componentes ejecutar `npm run dev`; para reconstruir ejecutar `npm run build` y reiniciar el proceso SSR.

## Instalación nueva

Requiere PHP 8.3 o superior (respetar las extensiones y versiones de composer.lock), Composer, Node compatible con Vite 8 y SQLite para desarrollo. La instalación actual fue verificada con PHP 8.5 y Node 24.

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
# Crear database/database.sqlite si no existe.
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan portfolio:admin
```

La contraseña del comando administrador se ingresa oculta. No hay registro público. Solo los usuarios con `is_admin` pueden acceder al panel y a las vistas previas privadas.

## Gestión de contenido

1. Entrar al panel y abrir Proyectos → Crear proyecto.
2. Completar título, dirección única, descripción, sección (Obra o Docencia), disciplina, año y portada.
3. Usar Agregar varias imágenes (hasta 12 por carga), guardar y completar la descripción accesible y el pie de cada imagen. También se pueden agregar individualmente y arrastrar para ordenar.
4. Guardar con Publicado desactivado para mantenerlo como borrador.
5. Abrir Vista previa desde la lista de proyectos.
6. Activar Publicado para mostrarlo en la web. Destacado lo coloca primero.
7. Editar Mi perfil para cambiar nombre, presentación, biografía, correo e Instagram.

Admite JPG, PNG y WebP de hasta 10 MB por imagen. La carga múltiple se incorpora al guardar y abre la edición para completar los textos. Las imágenes conservan su proporción. Las cuatro composiciones SVG incluidas son demostraciones propias, no obras de Romina, y se instalan solo en entornos local/testing. Las cargas de usuarios no admiten SVG.

## Pruebas

```sh
php artisan test
npm run typecheck
npm run build
```

Las pruebas verifican visibilidad de borradores, acceso privado, creación con carga real de imagen y publicación desde Filament.

## Preparación para publicación

Esta entrega es local. Usa SQLite y el disco público local de Laravel. Para producción:

- Elegir alojamiento PHP, dominio y HTTPS; configurar `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false` y credenciales propias.
- Configurar PostgreSQL mediante las variables DB de Laravel y ejecutar las migraciones. PostgreSQL no fue ejecutado en esta entrega.
- Configurar almacenamiento persistente y copias de seguridad de base de datos y archivos. Para S3, instalar el adaptador Flysystem S3 y configurar un disco con las mismas referencias que utilizan modelos y formularios; la integración S3 aún no está implementada.
- Servir únicamente el directorio `public/`. Mantener supervisados PHP y el proceso SSR. Evitar perder los archivos al desplegar.
- Crear una cuenta administradora real. No copiar la base de datos ni credenciales de demostración.
- Reemplazar biografía de muestra y obras de demostración, completar contacto. Las fuentes se cargan desde Google Fonts, con alternativas locales del sistema.
- Las nuevas imágenes JPG, PNG y WebP de hasta 20 megapíxeles generan derivados WebP: portada de hasta 1000 px y galería de hasta 1800 px, conservando proporción y originales. Archivos mayores conservan la versión original sin conversión para limitar memoria. Hace falta GD con WebP en el servidor. Los archivos cargados antes de esta mejora no se reprocesan automáticamente. Para mayor volumen, mover el procesamiento a una cola.
- Ajustar los límites de carga de PHP y del servidor para permitir 10 MB por imagen y los envíos múltiples.

Los borradores no tienen una página pública accesible. Los archivos se guardan en un disco público: no usarlo para material confidencial. Enviar un proyecto a la papelera conserva sus registros y archivos. Recuperarlo lo devuelve como borrador. Los archivos reemplazados se conservan; la limpieza de archivos huérfanos queda para una siguiente iteración.

## Páginas públicas

- `/`: portada breve con dos proyectos, priorizando los destacados.
- `/obra`: producción artística con filtros por disciplina.
- `/docencia`: proyectos educativos con filtros; se administra eligiendo la sección Docencia en cada proyecto.
- `/sobre-mi`: presentación y biografía editables desde Mi perfil.
- `/contacto`: correo e Instagram configurados desde Mi perfil.
- `/proyectos/{slug}`: detalle con retorno a la sección correspondiente.

Todas las páginas comparten menú, estado activo y menú desplegable en celular.

## Refinamiento visual

La portada muestra la primera obra de la selección en foco. En celular la colección se presenta a una columna. El visor muestra originales y permite navegar con flechas, botones y cerrar con Escape; incluye contador y pie de imagen.

El servidor local del proyecto permite cargas de hasta 10 MB por archivo y 128 MB por petición; estos valores se aplican solo al proceso, sin modificar PHP globalmente.

Obra usa una composición asimétrica en escritorio y una columna en celular. Sobre mí incluye una composición tipográfica original como identidad provisional hasta contar con materiales reales. Contacto muestra únicamente los enlaces configurados en Mi perfil.

## Administración ampliada

- **Escritorio:** indicadores de publicaciones, borradores e imágenes; accesos rápidos y pendientes de contenido.
- **Proyectos:** Obra/Docencia, disciplina, imágenes individuales o en lote, orden, destacados y vista previa privada. La tabla se puede filtrar por sección, disciplina y publicación.
- **Disciplinas:** crear y renombrar categorías. Un cambio de nombre actualiza sus proyectos. Las disciplinas utilizadas no se pueden eliminar.
- **Perfil y contacto:** identidad pública, subtítulo de marca, biografía, presentación, localidad, correo, Instagram y frase del pie. Foto opcional optimizada; CV PDF de hasta 10 MB, disponible como descarga pública. Quitar una foto vuelve a mostrar la composición gráfica.
- **Páginas y textos:** editar los textos principales de Inicio, Obra, Docencia, Sobre mí y Contacto, nombres del menú y descripciones para buscadores. Inicio permite elegir entre 1 y 8 proyectos. El diseño, las direcciones de las páginas y los textos funcionales de los controles siguen definidos en código.
- **Mi cuenta:** datos de acceso de la persona que administra, separados de la identidad pública de Romina.

Los proyectos tienen borradores. Los cambios en Perfil y Páginas se hacen públicos al guardar, sin un flujo de borradores o historial de revisiones en esta versión. Se mantienen las cinco páginas existentes: no es un constructor libre de páginas.

Las migraciones conservan los proyectos existentes y trasladan sus disciplinas a la nueva tabla. Los textos iniciales mantienen la propuesta visual anterior. Para desplegar esta ampliación ejecutar las migraciones, compilar y reiniciar SSR. No volver a cargar la base local en producción.

## Papelera y edición por bloques

Los proyectos se retiran con Enviar a papelera. En la tabla, abrir Filtros → Papelera → Solo papelera para recuperarlos. La recuperación conserva las imágenes y devuelve el proyecto como borrador; después se puede revisar y publicar. Las disciplinas usadas por proyectos en papelera también se conservan. El panel no ofrece eliminación definitiva. Los textos de cada página están agrupados en bloques plegables: encabezado, selección, accesos, presentación, contacto y buscadores según la página.
