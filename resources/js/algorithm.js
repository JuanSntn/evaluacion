const MIN_VISIBLE_WORDS = 2;
const MIN_WORDS_TO_EVALUATE = 3;
const MAX_WORDS = 20;

const createWordField = () => {
    const field = document.createElement('div');
    field.className = 'word-field-row';
    field.dataset.wordField = '';

    const content = document.createElement('div');
    content.className = 'min-w-0';

    const label = document.createElement('label');
    label.className = 'field-label';

    const required = document.createElement('span');
    required.className = 'text-red-700';
    required.setAttribute('aria-hidden', 'true');
    required.textContent = '*';

    const input = document.createElement('input');
    input.type = 'text';
    input.maxLength = 100;
    input.autocomplete = 'off';
    input.required = true;
    input.className = 'field-input';
    input.dataset.wordInput = '';

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'word-remove-button';
    removeButton.dataset.removeWord = '';
    removeButton.textContent = 'Eliminar';

    label.append('Palabra ', required);
    content.append(label, input);
    field.append(content, removeButton);

    return field;
};

document.querySelectorAll('[data-palindrome-form]').forEach((form) => {
    const fieldsContainer = form.querySelector('[data-word-fields]');
    const addButton = form.querySelector('[data-add-word]');
    const evaluateButton = form.querySelector('[data-evaluate-words]');
    const status = form.querySelector('[data-word-status]');
    const clientError = form.querySelector('[data-words-client-error]');

    if (!fieldsContainer || !addButton || !evaluateButton || !status || !clientError) {
        return;
    }

    const getFields = () => [...fieldsContainer.querySelectorAll('[data-word-field]')];

    const getInput = (field) => field.querySelector('[data-word-input]');

    const updateFields = () => {
        const fields = getFields();

        fields.forEach((field, index) => {
            const input = field.querySelector('[data-word-input]');
            const label = field.querySelector('label');
            const removeButton = field.querySelector('[data-remove-word]');

            if (!(input instanceof HTMLInputElement) || !(label instanceof HTMLLabelElement)) {
                return;
            }

            input.id = `word-${index}`;
            input.name = `words[${index}]`;
            label.htmlFor = input.id;
            label.firstChild.textContent = `Palabra ${index + 1} `;

            if (removeButton instanceof HTMLButtonElement) {
                removeButton.setAttribute('aria-label', `Eliminar palabra ${index + 1}`);
                removeButton.hidden = index < MIN_VISIBLE_WORDS;
            }
        });

        const count = fields.length;
        const canEvaluate = count >= MIN_WORDS_TO_EVALUATE;
        const canAdd = count < MAX_WORDS;
        const allWordsAreFilled = fields.every((field) => {
            const input = getInput(field);

            return input instanceof HTMLInputElement && input.value.trim() !== '';
        });

        addButton.disabled = !canAdd;
        evaluateButton.disabled = !canEvaluate;
        status.textContent = !canEvaluate
            ? `${count} de ${MAX_WORDS} palabras. Agrega una palabra más para poder evaluar.`
            : allWordsAreFilled
                ? `${count} de ${MAX_WORDS} palabras listas para evaluar.`
                : `Completa las ${count} palabras para poder evaluar.`;
    };

    addButton.addEventListener('click', () => {
        if (getFields().length >= MAX_WORDS) {
            return;
        }

        const field = createWordField();
        fieldsContainer.append(field);
        updateFields();
        field.querySelector('[data-word-input]')?.focus();
    });

    fieldsContainer.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const removeButton = target.closest('[data-remove-word]');
        const field = removeButton?.closest('[data-word-field]');
        const index = field ? getFields().indexOf(field) : -1;

        if (!(field instanceof HTMLElement) || index < MIN_VISIBLE_WORDS) {
            return;
        }

        field.remove();
        updateFields();
        clientError.hidden = true;
        addButton.focus();
    });

    fieldsContainer.addEventListener('input', (event) => {
        const target = event.target;

        if (!(target instanceof HTMLInputElement) || !target.matches('[data-word-input]')) {
            return;
        }

        target.removeAttribute('aria-invalid');
        clientError.hidden = true;
        updateFields();
    });

    form.addEventListener('submit', (event) => {
        const fields = getFields();

        if (fields.length < MIN_WORDS_TO_EVALUATE) {
            event.preventDefault();
            clientError.textContent = 'Agrega al menos una tercera palabra para ejecutar el algoritmo.';
            clientError.hidden = false;
            addButton.focus();

            return;
        }

        const emptyInput = fields
            .map(getInput)
            .find((input) => input instanceof HTMLInputElement && input.value.trim() === '');

        if (!(emptyInput instanceof HTMLInputElement)) {
            return;
        }

        event.preventDefault();
        emptyInput.setAttribute('aria-invalid', 'true');
        clientError.textContent = 'Completa todas las palabras. Debes rellenar al menos tres para ejecutar el algoritmo.';
        clientError.hidden = false;
        emptyInput.focus();
    });

    updateFields();
});
