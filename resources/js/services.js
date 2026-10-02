const SERVICES_PAGE_SIZE = 10;

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-services]');

    if (!(root instanceof HTMLElement)) {
        return;
    }

    const indexUrl = root.dataset.indexUrl;
    const storeUrl = root.dataset.storeUrl;
    const updateUrl = root.dataset.updateUrl;
    const destroyUrl = root.dataset.destroyUrl;
    const csrfToken = root.dataset.csrf;

    if (
        !indexUrl ||
        !storeUrl ||
        !updateUrl ||
        !destroyUrl ||
        !csrfToken
    ) {
        return;
    }

    const servicesTab = document.querySelector(
        '[data-tab-target="services-panel"]'
    );

    const createButton = root.querySelector(
        '[data-service-create]'
    );

    const formPanel = root.querySelector(
        '[data-service-form-panel]'
    );

    const form = root.querySelector(
        '[data-service-form]'
    );

    const formTitle = root.querySelector(
        '[data-service-form-title]'
    );

    const postIdInput = root.querySelector(
        '[data-service-post-id]'
    );

    const titleInput = root.querySelector(
        '[data-service-title]'
    );

    const bodyInput = root.querySelector(
        '[data-service-body]'
    );

    const submitButton = root.querySelector(
        '[data-service-submit]'
    );

    const cancelButton = root.querySelector(
        '[data-service-cancel]'
    );

    const formStatus = root.querySelector(
        '[data-service-form-status]'
    );

    const loading = root.querySelector(
        '[data-service-loading]'
    );

    const loadError = root.querySelector(
        '[data-service-load-error]'
    );

    const loadErrorMessage = root.querySelector(
        '[data-service-load-error-message]'
    );

    const retryButton = root.querySelector(
        '[data-service-retry]'
    );

    const emptyState = root.querySelector(
        '[data-service-empty]'
    );

    const postsContainer = root.querySelector(
        '[data-service-posts]'
    );

    const postTemplate = root.querySelector(
        '[data-service-post-template]'
    );

    const pagination = root.querySelector(
        '[data-service-pagination]'
    );

    const pageInfo = root.querySelector(
        '[data-service-page-info]'
    );

    const previousButton = root.querySelector(
        '[data-service-previous]'
    );

    const nextButton = root.querySelector(
        '[data-service-next]'
    );

    if (
        !(formPanel instanceof HTMLElement) ||
        !(form instanceof HTMLFormElement) ||
        !(formTitle instanceof HTMLElement) ||
        !(postIdInput instanceof HTMLInputElement) ||
        !(titleInput instanceof HTMLInputElement) ||
        !(bodyInput instanceof HTMLTextAreaElement) ||
        !(submitButton instanceof HTMLButtonElement) ||
        !(cancelButton instanceof HTMLButtonElement) ||
        !(loading instanceof HTMLElement) ||
        !(loadError instanceof HTMLElement) ||
        !(emptyState instanceof HTMLElement) ||
        !(postsContainer instanceof HTMLElement) ||
        !(postTemplate instanceof HTMLTemplateElement) ||
        !(pagination instanceof HTMLElement) ||
        !(pageInfo instanceof HTMLElement) ||
        !(previousButton instanceof HTMLButtonElement) ||
        !(nextButton instanceof HTMLButtonElement)
    ) {
        return;
    }

    let posts = [];
    let currentPage = 1;
    let loaded = false;
    let loadingPosts = false;
    let editingKey = null;
    let localPostCounter = 0;

    const buildPostUrl = (template, postId) => {
        return template.replace(
            '__POST_ID__',
            encodeURIComponent(String(postId))
        );
    };

    const createLocalKey = () => {
        localPostCounter += 1;

        return `local-${Date.now()}-${localPostCounter}`;
    };

    const normalizeApiPost = (post) => {
        return {
            ...post,
            local: false,
            clientKey: `api-${post.id}`,
        };
    };

    const findPostByKey = (key) => {
        return posts.find(
            (post) => post.clientKey === key
        );
    };

    const clearFieldError = (fieldName) => {
        const field = fieldName === 'title'
            ? titleInput
            : bodyInput;

        const error = root.querySelector(
            `[data-service-error="${fieldName}"]`
        );

        field.removeAttribute('aria-invalid');
        field.classList.remove('border-red-600');

        if (error instanceof HTMLElement) {
            error.textContent = '';
            error.hidden = true;
        }
    };

    const setFieldError = (fieldName, message) => {
        const field = fieldName === 'title'
            ? titleInput
            : bodyInput;

        const error = root.querySelector(
            `[data-service-error="${fieldName}"]`
        );

        field.setAttribute('aria-invalid', 'true');
        field.classList.add('border-red-600');

        if (error instanceof HTMLElement) {
            error.textContent = message;
            error.hidden = false;
        }
    };

    const clearFormErrors = () => {
        clearFieldError('title');
        clearFieldError('body');

        if (formStatus instanceof HTMLElement) {
            formStatus.textContent = '';
            formStatus.hidden = true;
        }
    };

    const showFormStatus = (message) => {
        if (!(formStatus instanceof HTMLElement)) {
            return;
        }

        formStatus.textContent = message;
        formStatus.hidden = false;
    };

    const validateForm = () => {
        clearFormErrors();

        let valid = true;

        if (titleInput.value.trim() === '') {
            setFieldError(
                'title',
                'Escribe el título de la publicación.'
            );

            valid = false;
        }

        if (bodyInput.value.trim() === '') {
            setFieldError(
                'body',
                'Escribe el contenido de la publicación.'
            );

            valid = false;
        }

        return valid;
    };

    const setSubmitLoading = (isLoading) => {
        submitButton.disabled = isLoading;

        if (isLoading) {
            submitButton.textContent = editingKey
                ? 'Actualizando...'
                : 'Guardando...';

            return;
        }

        submitButton.textContent = editingKey
            ? 'Guardar cambios'
            : 'Guardar publicación';
    };

    const request = async (
        url,
        options = {}
    ) => {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers ?? {}),
            },
        });

        let payload = null;

        const text = await response.text();

        if (text !== '') {
            try {
                payload = JSON.parse(text);
            } catch {
                payload = null;
            }
        }

        if (!response.ok) {
            const error = new Error(
                payload?.message ??
                'Ocurrió un error al procesar la solicitud.'
            );

            error.status = response.status;
            error.payload = payload;

            throw error;
        }

        return payload;
    };

    const hideLoadError = () => {
        loadError.hidden = true;

        if (loadErrorMessage instanceof HTMLElement) {
            loadErrorMessage.textContent = '';
        }
    };

    const showLoadError = (message) => {
        loading.hidden = true;
        postsContainer.hidden = true;
        emptyState.hidden = true;
        pagination.hidden = true;

        if (loadErrorMessage instanceof HTMLElement) {
            loadErrorMessage.textContent = message;
        }

        loadError.hidden = false;
    };

    const renderPagination = () => {
        const totalPosts = posts.length;
        const totalPages = Math.max(
            1,
            Math.ceil(totalPosts / SERVICES_PAGE_SIZE)
        );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        if (totalPosts === 0) {
            pagination.hidden = true;

            return;
        }

        const start =
            (currentPage - 1) * SERVICES_PAGE_SIZE + 1;

        const end = Math.min(
            currentPage * SERVICES_PAGE_SIZE,
            totalPosts
        );

        pageInfo.textContent =
            `Mostrando ${start}–${end} de ${totalPosts}`;

        previousButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= totalPages;

        pagination.hidden = totalPosts <= SERVICES_PAGE_SIZE;
    };

    const openCreateForm = () => {
        editingKey = null;

        postIdInput.value = '';
        titleInput.value = '';
        bodyInput.value = '';

        clearFormErrors();

        formTitle.textContent = 'Nueva publicación';
        submitButton.textContent = 'Guardar publicación';

        formPanel.hidden = false;

        titleInput.focus();
    };

    const openEditForm = (post) => {
        editingKey = post.clientKey;

        postIdInput.value = String(post.id);
        titleInput.value = post.title ?? '';
        bodyInput.value = post.body ?? '';

        clearFormErrors();

        formTitle.textContent = 'Editar publicación';
        submitButton.textContent = 'Guardar cambios';

        formPanel.hidden = false;

        formPanel.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });

        titleInput.focus();
    };

    const closeForm = () => {
        editingKey = null;

        form.reset();

        postIdInput.value = '';

        clearFormErrors();

        formPanel.hidden = true;

        submitButton.disabled = false;
        submitButton.textContent = 'Guardar publicación';
    };

    const removePost = async (
        post,
        deleteButton
    ) => {
        const confirmed = window.confirm(
            '¿Seguro que deseas eliminar esta publicación?'
        );

        if (!confirmed) {
            return;
        }

        deleteButton.disabled = true;

        try {
            if (!post.local) {
                const url = buildPostUrl(
                    destroyUrl,
                    post.id
                );

                await request(url, {
                    method: 'DELETE',
                });
            }

            posts = posts.filter(
                (item) =>
                    item.clientKey !== post.clientKey
            );

            if (editingKey === post.clientKey) {
                closeForm();
            }

            const totalPages = Math.max(
                1,
                Math.ceil(
                    posts.length /
                    SERVICES_PAGE_SIZE
                )
            );

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            renderPosts();
        } catch (error) {
            window.alert(
                error.message ??
                'No fue posible eliminar la publicación.'
            );

            deleteButton.disabled = false;
        }
    };

    const createPostElement = (
        post,
        absoluteIndex
    ) => {
        const fragment =
            postTemplate.content.cloneNode(true);

        const article = fragment.querySelector(
            '[data-service-post]'
        );

        const number = fragment.querySelector(
            '[data-service-post-number]'
        );

        const localLabel = fragment.querySelector(
            '[data-service-local-label]'
        );

        const title = fragment.querySelector(
            '[data-service-post-title]'
        );

        const body = fragment.querySelector(
            '[data-service-post-body]'
        );

        const editButton = fragment.querySelector(
            '[data-service-edit]'
        );

        const deleteButton = fragment.querySelector(
            '[data-service-delete]'
        );

        if (article instanceof HTMLElement) {
            article.dataset.postKey =
                post.clientKey;
        }

        if (number instanceof HTMLElement) {
            number.textContent =
                `#${absoluteIndex + 1}`;
        }

        if (localLabel instanceof HTMLElement) {
            localLabel.hidden = !post.local;
        }

        if (title instanceof HTMLElement) {
            title.textContent =
                post.title ?? '';
        }

        if (body instanceof HTMLElement) {
            body.textContent =
                post.body ?? '';
        }

        if (editButton instanceof HTMLButtonElement) {
            editButton.addEventListener(
                'click',
                () => {
                    openEditForm(post);
                }
            );
        }

        if (deleteButton instanceof HTMLButtonElement) {
            deleteButton.addEventListener(
                'click',
                () => {
                    void removePost(
                        post,
                        deleteButton
                    );
                }
            );
        }

        return fragment;
    };

    function renderPosts() {
        loading.hidden = true;
        hideLoadError();

        postsContainer.innerHTML = '';

        if (posts.length === 0) {
            postsContainer.hidden = true;
            emptyState.hidden = false;
            pagination.hidden = true;

            return;
        }

        emptyState.hidden = true;
        postsContainer.hidden = false;

        const startIndex =
            (currentPage - 1) *
            SERVICES_PAGE_SIZE;

        const currentPosts = posts.slice(
            startIndex,
            startIndex + SERVICES_PAGE_SIZE
        );

        currentPosts.forEach(
            (post, index) => {
                postsContainer.append(
                    createPostElement(
                        post,
                        startIndex + index
                    )
                );
            }
        );

        renderPagination();
    }

    const loadPosts = async (
        force = false
    ) => {
        if (
            loadingPosts ||
            (loaded && !force)
        ) {
            return;
        }

        loadingPosts = true;

        loading.hidden = false;
        loadError.hidden = true;
        emptyState.hidden = true;
        postsContainer.hidden = true;
        pagination.hidden = true;

        try {
            const response = await request(
                indexUrl,
                {
                    method: 'GET',
                }
            );

            const responsePosts =
                Array.isArray(response?.data)
                    ? response.data
                    : [];

            posts = responsePosts.map(
                normalizeApiPost
            );

            currentPage = 1;
            loaded = true;

            renderPosts();
        } catch (error) {
            loaded = false;

            showLoadError(
                error.message ??
                'No fue posible cargar las publicaciones.'
            );
        } finally {
            loadingPosts = false;
        }
    };

    const createPost = async (
        title,
        body
    ) => {
        const response = await request(
            storeUrl,
            {
                method: 'POST',
                body: JSON.stringify({
                    title,
                    body,
                }),
            }
        );

        const post = response?.data;

        if (!post) {
            throw new Error(
                'El servicio no devolvió la publicación creada.'
            );
        }

        const localPost = {
            ...post,
            title,
            body,
            local: true,
            clientKey: createLocalKey(),
        };

        posts.unshift(localPost);

        currentPage = 1;

        closeForm();
        renderPosts();
    };

    const updatePost = async (
        post,
        title,
        body
    ) => {
        if (post.local) {
            posts = posts.map((item) => {
                if (
                    item.clientKey !==
                    post.clientKey
                ) {
                    return item;
                }

                return {
                    ...item,
                    title,
                    body,
                };
            });

            closeForm();
            renderPosts();

            return;
        }

        const url = buildPostUrl(
            updateUrl,
            post.id
        );

        const response = await request(
            url,
            {
                method: 'PUT',
                body: JSON.stringify({
                    title,
                    body,
                }),
            }
        );

        const updated =
            response?.data ?? {};

        posts = posts.map((item) => {
            if (
                item.clientKey !==
                post.clientKey
            ) {
                return item;
            }

            return {
                ...item,
                ...updated,
                title,
                body,
                local: false,
                clientKey:
                    post.clientKey,
            };
        });

        closeForm();
        renderPosts();
    };

    form.addEventListener(
        'submit',
        async (event) => {
            event.preventDefault();

            if (!validateForm()) {
                return;
            }

            const title =
                titleInput.value.trim();

            const body =
                bodyInput.value.trim();

            clearFormErrors();
            setSubmitLoading(true);

            try {
                if (editingKey) {
                    const post =
                        findPostByKey(
                            editingKey
                        );

                    if (!post) {
                        throw new Error(
                            'No se encontró la publicación que deseas editar.'
                        );
                    }

                    await updatePost(
                        post,
                        title,
                        body
                    );

                    return;
                }

                await createPost(
                    title,
                    body
                );
            } catch (error) {
                if (
                    error.status === 422 &&
                    error.payload?.errors
                ) {
                    const errors =
                        error.payload.errors;

                    if (errors.title?.[0]) {
                        setFieldError(
                            'title',
                            errors.title[0]
                        );
                    }

                    if (errors.body?.[0]) {
                        setFieldError(
                            'body',
                            errors.body[0]
                        );
                    }

                    return;
                }

                showFormStatus(
                    error.message ??
                    'No fue posible guardar la publicación.'
                );
            } finally {
                setSubmitLoading(false);
            }
        }
    );

    titleInput.addEventListener(
        'input',
        () => {
            clearFieldError('title');
        }
    );

    bodyInput.addEventListener(
        'input',
        () => {
            clearFieldError('body');
        }
    );

    createButton?.addEventListener(
        'click',
        () => {
            openCreateForm();
        }
    );

    cancelButton.addEventListener(
        'click',
        () => {
            closeForm();
        }
    );

    retryButton?.addEventListener(
        'click',
        () => {
            void loadPosts(true);
        }
    );

    previousButton.addEventListener(
        'click',
        () => {
            if (currentPage <= 1) {
                return;
            }

            currentPage -= 1;

            renderPosts();

            root.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }
    );

    nextButton.addEventListener(
        'click',
        () => {
            const totalPages = Math.ceil(
                posts.length /
                SERVICES_PAGE_SIZE
            );

            if (
                currentPage >= totalPages
            ) {
                return;
            }

            currentPage += 1;

            renderPosts();

            root.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }
    );

    servicesTab?.addEventListener(
        'click',
        () => {
            void loadPosts();
        }
    );

    if (!root.hidden) {
        void loadPosts();
    }
});
