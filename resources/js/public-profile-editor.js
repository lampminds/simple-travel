import Quill from 'quill';
import 'quill/dist/quill.snow.css';

document.querySelectorAll('[data-about-editor]').forEach((element) => {
    const inputId = element.getAttribute('data-about-editor');
    const input = inputId ? document.getElementById(inputId) : null;
    if (! input) {
        return;
    }

    const quill = new Quill(element, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link'],
                ['clean'],
            ],
        },
    });

    element.closest('form')?.addEventListener('submit', () => {
        input.value = quill.getSemanticHTML();
    });
});
