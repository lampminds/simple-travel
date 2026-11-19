import Quill from 'quill';
import 'quill/dist/quill.snow.css';

function mountAboutEditor(element) {
    if (element.dataset.quillReady === '1') {
        return;
    }

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

    element.dataset.quillReady = '1';
    element.closest('form')?.addEventListener('submit', () => {
        input.value = quill.getSemanticHTML();
    });
}

document.querySelectorAll('[data-about-editor]').forEach((element) => {
    const pane = element.closest('.tab-pane');
    if (! pane || pane.classList.contains('active')) {
        mountAboutEditor(element);

        return;
    }

    const tab = document.querySelector(`[data-bs-target="#${pane.id}"]`);
    tab?.addEventListener('shown.bs.tab', () => mountAboutEditor(element));
});
