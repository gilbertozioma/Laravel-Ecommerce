function csrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

async function jsonFetch(url, opts = {}) {
    const headers = Object.assign({
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
    }, opts.headers || {});

    const res = await fetch(url, Object.assign({ credentials: 'same-origin', headers }, opts));
    const contentType = res.headers.get('content-type') || '';
    const isJson = contentType.includes('application/json');
    const body = isJson ? await res.json() : await res.text();
    if (!res.ok) throw { status: res.status, body };
    return body;
}

function serializeForm(form) {
    const data = {};
    new FormData(form).forEach((v, k) => { data[k] = v; });
    return data;
}

async function handleProfileSubmit(form) {
    const data = serializeForm(form);
    try {
        const res = await jsonFetch(form.action, { method: 'POST', body: JSON.stringify(data) });
        window.dispatchEvent(new CustomEvent('profile-updated', { detail: res.name }));
    } catch (err) {
        // TODO: show errors inline
        console.error(err);
    }
}

async function handlePasswordSubmit(form) {
    const data = serializeForm(form);
    try {
        await jsonFetch(form.action, { method: 'POST', body: JSON.stringify(data) });
        window.dispatchEvent(new CustomEvent('password-updated'));
    } catch (err) {
        console.error(err);
    }
}

async function handleDeleteSubmit(form) {
    const data = serializeForm(form);
    try {
        await jsonFetch(form.action, { method: 'POST', body: JSON.stringify(data) });
        // logout/redirect to home on success
        window.location = '/';
    } catch (err) {
        if (err.status === 422) {
            alert('Password incorrect');
            return;
        }
        console.error(err);
    }
}

export function attachProfileBindings() {
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-action="profile-update"]');
        if (form) {
            e.preventDefault();
            handleProfileSubmit(form);
            return;
        }

        const pw = e.target.closest('form[data-action="update-password"]');
        if (pw) {
            e.preventDefault();
            handlePasswordSubmit(pw);
            return;
        }

        const del = e.target.closest('form[data-action="delete-user"]');
        if (del) {
            e.preventDefault();
            handleDeleteSubmit(del);
            return;
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => attachProfileBindings());
} else {
    attachProfileBindings();
}

export default { attachProfileBindings };
