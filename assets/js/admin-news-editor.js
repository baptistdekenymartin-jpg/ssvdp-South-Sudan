(function () {
    const form = document.querySelector('[data-news-form]');
    if (!form) return;

    const source = form.querySelector('[data-rich-source]');
    const editor = form.querySelector('[data-rich-editable]');
    const title = form.querySelector('[data-slug-title]');
    const slug = form.querySelector('[data-slug-input]');
    let slugTouched = Boolean(slug && slug.value.trim());

    function syncSource() {
        if (source && editor) source.value = editor.innerHTML.trim();
    }

    function slugify(value) {
        return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    if (slug) slug.addEventListener('input', () => { slugTouched = true; });
    if (title && slug) {
        title.addEventListener('input', () => {
            if (!slugTouched || slug.value.trim() === '') slug.value = slugify(title.value);
        });
    }

    if (editor) {
        editor.addEventListener('input', syncSource);
        form.addEventListener('submit', syncSource);
    }

    form.querySelectorAll('[data-cmd]').forEach((button) => {
        button.addEventListener('click', () => {
            editor?.focus();
            document.execCommand(button.dataset.cmd, false);
            syncSource();
        });
    });

    form.querySelectorAll('[data-block]').forEach((button) => {
        button.addEventListener('click', () => {
            editor?.focus();
            document.execCommand('formatBlock', false, button.dataset.block);
            syncSource();
        });
    });

    const linkButton = form.querySelector('[data-link]');
    if (linkButton) {
        linkButton.addEventListener('click', () => {
            editor?.focus();
            const url = window.prompt('Enter the link URL');
            if (!url) return;
            if (!/^(https?:\/\/|mailto:|tel:|\/)/i.test(url)) {
                window.alert('Use a full http/https URL, mailto:, tel:, or a site path starting with /.');
                return;
            }
            document.execCommand('createLink', false, url);
            syncSource();
        });
    }

    const clearButton = form.querySelector('[data-clear]');
    if (clearButton) {
        clearButton.addEventListener('click', () => {
            editor?.focus();
            document.execCommand('removeFormat', false);
            document.execCommand('formatBlock', false, 'p');
            syncSource();
        });
    }

    const summary = form.querySelector('[data-summary-counter]');
    const summaryCount = form.querySelector('[data-summary-count]');
    function updateSummaryCount() {
        if (summary && summaryCount) summaryCount.textContent = String(summary.value.length);
    }
    if (summary) {
        summary.addEventListener('input', updateSummaryCount);
        updateSummaryCount();
    }

    const featuredInput = form.querySelector('[data-featured-image]');
    const featuredPreview = form.querySelector('[data-featured-preview]');
    const featuredImg = featuredPreview?.querySelector('img');
    const featuredName = featuredPreview?.querySelector('[data-file-name]');
    const removeFeatured = form.querySelector('[data-remove-featured]');
    if (featuredInput && featuredPreview) {
        featuredInput.addEventListener('change', () => {
            const file = featuredInput.files && featuredInput.files[0];
            if (!file) { featuredPreview.hidden = true; return; }
            if (featuredImg) featuredImg.src = URL.createObjectURL(file);
            if (featuredName) featuredName.textContent = file.name;
            featuredPreview.hidden = false;
        });
    }
    if (removeFeatured && featuredInput && featuredPreview) {
        removeFeatured.addEventListener('click', () => {
            featuredInput.value = '';
            featuredPreview.hidden = true;
            if (featuredImg) featuredImg.removeAttribute('src');
        });
    }

    const additionalInput = form.querySelector('[data-additional-images]');
    const additionalPreview = form.querySelector('[data-additional-preview]');
    const additionalCount = form.querySelector('[data-additional-count]');
    let selectedAdditional = [];
    function refreshAdditionalFiles() {
        if (!additionalInput || !additionalPreview) return;
        const dt = new DataTransfer();
        selectedAdditional.forEach(file => dt.items.add(file));
        additionalInput.files = dt.files;
        additionalPreview.innerHTML = '';
        selectedAdditional.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'admin-selected-image';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            const name = document.createElement('span');
            name.textContent = file.name;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Remove';
            remove.addEventListener('click', () => {
                selectedAdditional.splice(index, 1);
                refreshAdditionalFiles();
            });
            item.append(img, name, remove);
            additionalPreview.appendChild(item);
        });
        if (additionalCount) additionalCount.textContent = String(selectedAdditional.length);
    }
    if (additionalInput) {
        additionalInput.addEventListener('change', () => {
            selectedAdditional = Array.from(additionalInput.files || []);
            refreshAdditionalFiles();
        });
    }

    const previewButton = form.querySelector('[data-news-preview]');
    if (previewButton) {
        previewButton.addEventListener('click', () => {
            syncSource();
            const data = new FormData(form);
            const preview = window.open('', '_blank');
            if (!preview) return;
            const featuredFile = featuredInput?.files?.[0];
            const featuredUrl = featuredFile ? URL.createObjectURL(featuredFile) : (document.querySelector('.admin-news-current-image img')?.src || '');
            const additionalUrls = selectedAdditional.map(file => URL.createObjectURL(file));
            const safe = (value) => String(value || '').replace(/[&<>"']/g, (ch) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch]));
            const sanitizePreviewHtml = (html) => {
                const template = document.createElement('template');
                template.innerHTML = String(html || '');
                const allowed = new Set(['P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'H2', 'H3', 'UL', 'OL', 'LI', 'BLOCKQUOTE', 'A']);
                template.content.querySelectorAll('script, iframe, object, embed, style').forEach(node => node.remove());
                Array.from(template.content.querySelectorAll('*')).forEach((node) => {
                    if (!allowed.has(node.tagName)) {
                        node.replaceWith(document.createTextNode(node.textContent || ''));
                        return;
                    }
                    const href = node.tagName === 'A' ? (node.getAttribute('href') || '') : '';
                    Array.from(node.attributes).forEach((attr) => node.removeAttribute(attr.name));
                    if (node.tagName === 'A' && /^(https?:\/\/|mailto:|tel:|\/)/i.test(href)) {
                        node.setAttribute('href', href);
                        node.setAttribute('rel', 'noopener noreferrer');
                        if (/^https?:\/\//i.test(href)) node.setAttribute('target', '_blank');
                    }
                });
                return template.innerHTML;
            };
            const previewContent = sanitizePreviewHtml(source.value);
            preview.document.write(`<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Preview: ${safe(data.get('title') || 'News Story')}</title><link rel="stylesheet" href="../../assets/css/style.css"><link rel="stylesheet" href="../../assets/css/admin.css"></head><body><div class="admin-alert" style="margin:18px">Preview only. This draft preview is visible from the signed-in admin session.</div><main class="news-article" style="margin-top:20px"><div class="container news-article-inner"><div class="news-article-meta"><span>${safe(data.get('category'))}</span><span>${safe(data.get('published_at'))}</span>${data.get('location') ? `<span>${safe(data.get('location'))}</span>` : ''}</div><h1 style="color:#063b8f">${safe(data.get('title') || 'Untitled News')}</h1>${featuredUrl ? `<figure class="news-article-featured-image"><img src="${featuredUrl}" alt=""></figure>` : ''}<div class="news-article-body"><p><strong>${safe(data.get('excerpt'))}</strong></p>${previewContent}</div>${additionalUrls.map(url => `<figure class="news-article-inline-image"><img src="${url}" alt=""></figure>`).join('')}</div></main></body></html>`);
            preview.document.close();
        });
    }
})();