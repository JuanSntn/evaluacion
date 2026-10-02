import 'flowbite';
import './algorithm';
import './services';

const SIDEBAR_PREFERENCE_KEY = 'sidebar-collapsed';

document.querySelectorAll('[data-sidebar]').forEach((sidebar) => {
    const toggle = sidebar.querySelector('[data-sidebar-toggle]');

    if (!(toggle instanceof HTMLButtonElement)) {
        return;
    }

    const collapseIcon = toggle.querySelector('[data-sidebar-icon="collapse"]');
    const expandIcon = toggle.querySelector('[data-sidebar-icon="expand"]');

    const setCollapsed = (collapsed) => {
        sidebar.classList.toggle('sidebar-collapsed', collapsed);
        document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
        toggle.setAttribute('aria-expanded', String(!collapsed));

        const label = collapsed ? 'Expandir menú' : 'Contraer menú';

        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('title', label);
        collapseIcon?.toggleAttribute('hidden', collapsed);
        expandIcon?.toggleAttribute('hidden', !collapsed);
    };

    try {
        setCollapsed(localStorage.getItem(SIDEBAR_PREFERENCE_KEY) === 'true');
    } catch {
        setCollapsed(false);
    }

    toggle.addEventListener('click', () => {
        const collapsed = !sidebar.classList.contains('sidebar-collapsed');

        setCollapsed(collapsed);

        try {
            localStorage.setItem(SIDEBAR_PREFERENCE_KEY, String(collapsed));
        } catch {
            // El menú continúa funcionando aunque el navegador bloquee almacenamiento local.
        }
    });
});



//RFC y CURP
const RFC_PATTERN = /^([A-ZÑ&]{3,4})(\d{6})([A-Z\d]{3})$/;
const CURP_PATTERN = /^([A-ZÑ])([AEIOUX])([A-ZÑ])([A-ZÑ])(\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])([HMX])([A-Z]{2})([B-DF-HJ-NP-TV-ZÑX])([B-DF-HJ-NP-TV-ZÑX])([B-DF-HJ-NP-TV-ZÑX])([0-9A-Z])(\d)$/u;
const CURP_CHECKSUM_DICTIONARY = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';

const normalizeRfc = (value) => {
    return value
        .trim()
        .toUpperCase()
        .replace(/\s+/g, '');
};

const normalizeCurp = (value) => {
    return value
        .trim()
        .toUpperCase()
        .replace(/[\s-]+/g, '');
};

const normalizePhone = (value) => {
    return value.replace(/[\s()-]+/g, '');
};

const normalizeWebsite = (value) => {
    const website = value.trim();

    if (!website || /^[a-z][a-z\d+.-]*:\/\//i.test(website)) {
        return website;
    }

    return `https://${website}`;
};

// El año del RFC tiene 2 dígitos: se prueba como 19xx y como 20xx.
const isRealDate = (datePart) => {
    const yy = Number(datePart.slice(0, 2));
    const mm = Number(datePart.slice(2, 4));
    const dd = Number(datePart.slice(4, 6));

    return [1900 + yy, 2000 + yy].some((year) => {
        const date = new Date(year, mm - 1, dd);

        return (
            date.getFullYear() === year &&
            date.getMonth() === mm - 1 &&
            date.getDate() === dd
        );
    });
};

const isValidRfc = (rfc) => {
    const match = RFC_PATTERN.exec(rfc);

    return (
        match !== null &&
        isRealDate(match[2])
    );
};

const isValidCurpDate = (year, month, day, differentiator) => {
    const fullYear = /\d/.test(differentiator)
        ? 1900 + Number(year)
        : 2000 + Number(year);
    const date = new Date(fullYear, Number(month) - 1, Number(day));

    return (
        date.getFullYear() === fullYear &&
        date.getMonth() === Number(month) - 1 &&
        date.getDate() === Number(day)
    );
};

const hasValidCurpChecksum = (curp) => {
    const sum = [...curp.slice(0, 17)].reduce((total, character, index) => {
        const value = CURP_CHECKSUM_DICTIONARY.indexOf(character);

        return value === -1
            ? Number.NaN
            : total + value * (18 - index);
    }, 0);

    return Number.isFinite(sum) && (10 - (sum % 10)) % 10 === Number(curp.at(-1));
};


// mensajes de error para validaciones de campos
const messageFor = (input) => {
    if (input.validity.valueMissing) {
        return 'Este campo es obligatorio.';
    }

    if (input.validity.typeMismatch) {
        if (input.type === 'email') {
            return 'Escribe un correo electrónico válido.';
        }

        if (input.type === 'url') {
            return 'Escribe una dirección web válida.';
        }

        return 'El valor ingresado no es válido.';
    }

    if (input.validity.tooShort) {
        return `Este campo debe tener al menos ${input.minLength} caracteres.`;
    }

    if (input.validity.customError) {
        switch (input.dataset.clientErrorType) {
            case 'password-mismatch':
                return 'Las contraseñas no coinciden.';

            case 'invalid-rfc':
                return 'Escribe un RFC válido (12 o 13 caracteres, con fecha correcta).';

            case 'invalid-phone':
                return 'El teléfono debe contener 10 dígitos.';

            case 'invalid-curp-structure':
                return 'La CURP debe tener 18 caracteres y una estructura válida.';

            case 'invalid-curp-state':
                return 'La entidad federativa contenida en la CURP no es válida.';

            case 'invalid-curp-date':
                return 'La fecha de nacimiento contenida en la CURP no es válida.';

            case 'invalid-curp-checksum':
                return 'El dígito verificador de la CURP no es válido.';

            case 'password-policy':
                return 'La contraseña debe contener al menos una letra y un número.';

            case 'invalid-website':
                return 'Escribe una dirección web válida, por ejemplo www.google.com.';
        }
    }

    return 'Revisa este campo.';
};


//validaciones rfc, curp, telefono, website, password y confirmacion de password
const clearCustomValidation = (input) => {
    input.setCustomValidity('');
    delete input.dataset.clientErrorType;
};

const validateRfc = (input) => {
    if (!input.hasAttribute('data-rfc') || !input.value.trim()) {
        return;
    }

    if (!isValidRfc(normalizeRfc(input.value))) {
        input.dataset.clientErrorType = 'invalid-rfc';
        input.setCustomValidity('invalid-rfc');
    }
};

const validateCurp = (input) => {
    if (!input.hasAttribute('data-curp') || !input.value.trim()) {
        return;
    }

    const curp = normalizeCurp(input.value);
    const match = CURP_PATTERN.exec(curp);

    if (match === null) {
        input.dataset.clientErrorType = 'invalid-curp-structure';
        input.setCustomValidity('invalid-curp-structure');

        return;
    }

    const entities = new Set(
        (input.dataset.curpEntities ?? '')
            .split(',')
            .filter(Boolean),
    );

    if (!entities.has(match[9])) {
        input.dataset.clientErrorType = 'invalid-curp-state';
        input.setCustomValidity('invalid-curp-state');

        return;
    }

    if (!isValidCurpDate(match[5], match[6], match[7], match[13])) {
        input.dataset.clientErrorType = 'invalid-curp-date';
        input.setCustomValidity('invalid-curp-date');

        return;
    }

    if (!hasValidCurpChecksum(curp)) {
        input.dataset.clientErrorType = 'invalid-curp-checksum';
        input.setCustomValidity('invalid-curp-checksum');
    }
};

const validatePhone = (input) => {
    if (!input.hasAttribute('data-phone') || !input.value.trim()) {
        return;
    }

    if (!/^\d{10}$/.test(normalizePhone(input.value))) {
        input.dataset.clientErrorType = 'invalid-phone';
        input.setCustomValidity('invalid-phone');
    }
};

const validateWebsite = (input) => {
    if (!input.hasAttribute('data-website') || !input.value.trim()) {
        return;
    }

    try {
        const normalizedWebsite = normalizeWebsite(input.value);

        if (/\s/.test(normalizedWebsite)) {
            throw new Error('invalid website');
        }

        const website = new URL(normalizedWebsite);

        if (!['http:', 'https:'].includes(website.protocol) || !website.hostname) {
            throw new Error('invalid website');
        }
    } catch {
        input.dataset.clientErrorType = 'invalid-website';
        input.setCustomValidity('invalid-website');
    }
};

const validatePasswordPolicy = (input) => {
    if (!input.hasAttribute('data-password-policy') || !input.value) {
        return;
    }

    const hasLetter = /[A-Za-z]/.test(input.value);
    const hasNumber = /\d/.test(input.value);

    if (!hasLetter || !hasNumber) {
        input.dataset.clientErrorType = 'password-policy';
        input.setCustomValidity('password-policy');
    }
};

const validateConfirmation = (input) => {
    const confirmationTarget = input.dataset.confirmationFor;

    if (!confirmationTarget) {
        return;
    }

    const source = input.form?.elements.namedItem(confirmationTarget);

    if (source && input.value !== source.value) {
        input.dataset.clientErrorType = 'password-mismatch';
        input.setCustomValidity('password-mismatch');
    }
};

const validateInput = (input) => {
    clearCustomValidation(input);

    validateRfc(input);
    validateCurp(input);
    validatePhone(input);
    validateWebsite(input);
    validatePasswordPolicy(input);
    validateConfirmation(input);

    const invalid = !input.validity.valid;

    const group = input.closest('.field-group');
    const error = group?.querySelector('[data-client-error]');

    input.setAttribute('aria-invalid', invalid ? 'true' : 'false');

    if (error) {
        error.textContent = invalid ? messageFor(input) : '';
        error.hidden = !invalid;
    }

    return !invalid;
};

const hideServerError = (input) => {
    const serverError = input
        .closest('.field-group')
        ?.querySelector('[data-server-error]');

    if (serverError) {
        serverError.hidden = true;
    }
};

//formularios
document.querySelectorAll('[data-validate-form]').forEach((form) => {
    const fields = [
        ...form.querySelectorAll('input, textarea, select'),
    ].filter((field) => !field.disabled && !['hidden', 'submit', 'button'].includes(field.type));

    fields.forEach((field) => {
        field.addEventListener('input', () => {
            hideServerError(field);

            if (field.hasAttribute('data-rfc')) {
                const normalized = normalizeRfc(field.value);

                // Solo reasigna si cambió, para que el cursor no salte al final.
                if (field.value !== normalized) {
                    field.value = normalized;
                }
            }

            if (field.hasAttribute('data-curp')) {
                const normalized = normalizeCurp(field.value);

                if (field.value !== normalized) {
                    field.value = normalized;
                }
            }

            if (field.hasAttribute('data-phone')) {
                const normalized = normalizePhone(field.value);

                if (field.value !== normalized) {
                    field.value = normalized;
                }
            }

            validateInput(field);

            const confirmation = form.querySelector(
                `[data-confirmation-for="${field.name}"]`,
            );

            if (confirmation?.value) {
                validateInput(confirmation);
            }
        });

        field.addEventListener('blur', () => {
            if (field.hasAttribute('data-website')) {
                field.value = normalizeWebsite(field.value);
            }

            validateInput(field);
        });
    });

    form.addEventListener('submit', (event) => {
        let firstInvalid = null;

        fields.forEach((field) => {
            if (field.hasAttribute('data-website')) {
                field.value = normalizeWebsite(field.value);
            }

            const valid = validateInput(field);

            if (!valid && firstInvalid === null) {
                firstInvalid = field;
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();

            return;
        }

        if (form.hasAttribute('data-front-only')) {
            event.preventDefault();

            const status = form.querySelector('[data-form-status]');

            if (status) {
                status.textContent = 'La información pasó las validaciones. Aún no se guarda porque este módulo es solo de interfaz.';
                status.hidden = false;
            }

            return;
        }

        const button = form.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        }
    });
});


//pestañas tabs
document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    const buttons = [...tabs.querySelectorAll('[role="tab"]')];

    const activate = (button) => {
        buttons.forEach((tab) => {
            const panel = document.getElementById(tab.getAttribute('aria-controls'));
            const selected = tab === button;

            tab.setAttribute('aria-selected', String(selected));
            tab.tabIndex = selected ? 0 : -1;

            if (panel) {
                panel.hidden = !selected;
            }
        });
    };

    buttons.forEach((button, index) => {
        button.addEventListener('click', () => activate(button));

        button.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();

            const nextIndex = event.key === 'Home'
                ? 0
                : event.key === 'End'
                    ? buttons.length - 1
                    : (index + (event.key === 'ArrowRight' ? 1 : -1) + buttons.length) % buttons.length;
            const nextButton = buttons[nextIndex];

            activate(nextButton);
            nextButton.focus();
        });
    });
});


//ojo de contraseña icon
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const inputId = button.getAttribute('aria-controls');
    const input = inputId ? document.getElementById(inputId) : null;

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    const showIcon = button.querySelector('[data-password-icon="show"]');
    const hideIcon = button.querySelector('[data-password-icon="hide"]');

    button.addEventListener('click', () => {
        const showPassword = input.type === 'password';

        input.type = showPassword ? 'text' : 'password';

        const label = showPassword
            ? 'Ocultar contraseña'
            : 'Mostrar contraseña';

        button.setAttribute('aria-pressed', String(showPassword));
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);

        showIcon?.toggleAttribute('hidden', showPassword);
        hideIcon?.toggleAttribute('hidden', !showPassword);
    });
});
