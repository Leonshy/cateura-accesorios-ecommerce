import './bootstrap';

import Alpine from 'alpinejs';
import { editFilesBeforeUpload } from './image-editor';

window.Alpine = Alpine;
window.editFilesBeforeUpload = editFilesBeforeUpload;

Alpine.start();
