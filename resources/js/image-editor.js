// Editor de imagen (recortar / rotar / espejar / zoom) mostrado antes de subir
// un archivo a la biblioteca multimedia. Cropper.js se carga de forma diferida
// (dynamic import) para no sumar peso al bundle público, ya que solo se usa
// dentro del panel admin.
const EDITABLE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

// value = null → "Libre". Los valores numéricos son ancho/alto (ratio).
const ASPECT_OPTIONS = [
    { value: '', label: 'Libre' },
    { value: '1', label: 'Cuadrado 1:1' },
    { value: '0.8', label: 'Retrato 4:5' },
    { value: '0.75', label: 'Retrato 3:4' },
    { value: '1.3333333333', label: 'Estándar 4:3' },
    { value: '1.7777777778', label: 'Panorámico 16:9' },
];

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

// Redondea el valor de aspect recibido (puede venir como "4/5" o como número)
// a la opción predefinida más cercana, para dejarla preseleccionada.
function normalizeAspect(aspect) {
    if (aspect === null || aspect === undefined || aspect === '') return '';
    if (typeof aspect === 'string' && aspect.includes('/')) {
        const [w, h] = aspect.split('/').map(Number);
        if (w > 0 && h > 0) aspect = w / h;
    }
    const num = parseFloat(aspect);
    if (!Number.isFinite(num) || num <= 0) return '';
    let closest = ASPECT_OPTIONS[0];
    let closestDiff = Infinity;
    for (const opt of ASPECT_OPTIONS) {
        if (opt.value === '') continue;
        const diff = Math.abs(parseFloat(opt.value) - num);
        if (diff < closestDiff) { closestDiff = diff; closest = opt; }
    }
    return closestDiff < 0.05 ? closest.value : '';
}

/**
 * @param {File} file
 * @param {{aspect?: string|number|null}} [options] proporción sugerida por
 *   defecto para preseleccionar en el editor (ej. "4/5", 1.3333, o null/"" para libre).
 * @returns {Promise<File|null>} archivo editado, el original (si se eligió
 *   "subir sin editar"), o null si el usuario canceló (el archivo debe
 *   excluirse de la subida).
 */
export function openImageEditor(file, options = {}) {
    if (!EDITABLE_MIME_TYPES.includes(file.type)) {
        return Promise.resolve(file);
    }

    const initialAspect = normalizeAspect(options.aspect);

    return new Promise((resolve) => {
        Promise.all([
            import('cropperjs'),
            import('cropperjs/dist/cropper.css'),
        ]).then(([{ default: Cropper }]) => {
            const objectUrl = URL.createObjectURL(file);

            const aspectOptionsHtml = ASPECT_OPTIONS.map(opt =>
                `<option value="${opt.value}" ${opt.value === initialAspect ? 'selected' : ''}>${opt.label}</option>`
            ).join('');

            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60';
            overlay.innerHTML = `
                <div class="relative bg-white shadow-2xl w-full max-w-3xl flex flex-col max-h-[90vh]">
                    <div class="flex items-center justify-between p-4 border-b border-stone-100 flex-shrink-0">
                        <h3 class="font-medium text-stone-800 text-sm uppercase tracking-wide">Editar imagen</h3>
                        <span class="text-xs text-stone-400 truncate max-w-[50%]">${escapeHtml(file.name)}</span>
                    </div>
                    <div class="flex-1 overflow-hidden bg-stone-900 flex items-center justify-center relative" style="min-height:320px;">
                        <img data-role="editor-image" src="${objectUrl}" style="display:block; max-width:100%; max-height:60vh;">
                        <div data-role="dimensions" class="absolute bottom-2 left-2 bg-black/70 text-white text-xs font-medium px-2 py-1 tabular-nums pointer-events-none">— × — px</div>
                    </div>
                    <div class="p-4 border-t border-stone-100 flex flex-wrap items-center gap-2 flex-shrink-0">
                        <button type="button" data-action="rotate-left" title="Rotar a la izquierda" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">⟲ Rotar</button>
                        <button type="button" data-action="rotate-right" title="Rotar a la derecha" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">⟳ Rotar</button>
                        <button type="button" data-action="flip-h" title="Espejar horizontal" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">Espejo</button>
                        <button type="button" data-action="zoom-in" title="Acercar" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">Zoom +</button>
                        <button type="button" data-action="zoom-out" title="Alejar" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">Zoom −</button>
                        <select data-role="aspect" class="input-cateura border text-xs py-1.5 px-2">
                            ${aspectOptionsHtml}
                        </select>
                        <button type="button" data-action="reset" class="px-3 py-1.5 border border-stone-300 text-xs uppercase hover:bg-stone-50">Reiniciar</button>
                        <div class="flex-1"></div>
                        <button type="button" data-action="skip" class="px-4 py-2 text-xs uppercase text-stone-500 hover:text-stone-700">Subir sin editar</button>
                        <button type="button" data-action="cancel" class="px-4 py-2 border border-stone-300 text-xs uppercase text-stone-600 hover:bg-stone-50">Cancelar</button>
                        <button type="button" data-action="apply" class="px-5 py-2 bg-copper-600 text-white text-xs font-medium uppercase hover:bg-copper-700">Aplicar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            const imgEl        = overlay.querySelector('[data-role="editor-image"]');
            const dimensionsEl = overlay.querySelector('[data-role="dimensions"]');
            let flippedX = 1;

            const cropper = new Cropper(imgEl, {
                viewMode: 1,
                autoCropArea: 1,
                background: false,
                responsive: true,
                checkOrientation: true,
                aspectRatio: initialAspect === '' ? NaN : parseFloat(initialAspect),
                crop(event) {
                    dimensionsEl.textContent = `${Math.round(event.detail.width)} × ${Math.round(event.detail.height)} px`;
                },
            });

            function cleanup(result) {
                cropper.destroy();
                overlay.remove();
                URL.revokeObjectURL(objectUrl);
                resolve(result);
            }

            overlay.querySelector('[data-action="rotate-left"]').addEventListener('click', () => cropper.rotate(-90));
            overlay.querySelector('[data-action="rotate-right"]').addEventListener('click', () => cropper.rotate(90));
            overlay.querySelector('[data-action="flip-h"]').addEventListener('click', () => {
                flippedX = flippedX * -1;
                cropper.scaleX(flippedX);
            });
            overlay.querySelector('[data-action="zoom-in"]').addEventListener('click', () => cropper.zoom(0.1));
            overlay.querySelector('[data-action="zoom-out"]').addEventListener('click', () => cropper.zoom(-0.1));
            overlay.querySelector('[data-action="reset"]').addEventListener('click', () => { flippedX = 1; cropper.reset(); });
            overlay.querySelector('[data-role="aspect"]').addEventListener('change', (e) => {
                cropper.setAspectRatio(e.target.value === '' ? NaN : parseFloat(e.target.value));
            });
            overlay.querySelector('[data-action="cancel"]').addEventListener('click', () => cleanup(null));
            overlay.querySelector('[data-action="skip"]').addEventListener('click', () => cleanup(file));
            overlay.querySelector('[data-action="apply"]').addEventListener('click', () => {
                const canvas = cropper.getCroppedCanvas({ imageSmoothingQuality: 'high' });
                if (!canvas) { cleanup(file); return; }
                canvas.toBlob((blob) => {
                    if (!blob) { cleanup(file); return; }
                    const editedFile = new File([blob], file.name, { type: file.type, lastModified: Date.now() });
                    cleanup(editedFile);
                }, file.type, 0.92);
            });
        }).catch(() => resolve(file));
    });
}

/**
 * Pasa cada archivo de la lista por el editor (solo si es una imagen editable),
 * en secuencia. Los archivos cancelados por el usuario se excluyen del resultado.
 * @param {File[]} files
 * @param {{aspect?: string|number|null}} [options]
 * @returns {Promise<File[]>}
 */
export async function editFilesBeforeUpload(files, options = {}) {
    const result = [];
    for (const file of files) {
        const edited = await openImageEditor(file, options);
        if (edited) result.push(edited);
    }
    return result;
}
