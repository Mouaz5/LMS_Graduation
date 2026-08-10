const submitForm = (element) => element.closest('form')?.submit();

document.addEventListener('change', (event) => {
    const element = event.target.closest('[data-auto-submit]');
    if (element) submitForm(element);
});

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-sidebar-toggle]');
    if (toggle) {
        document.getElementById('sidebar')?.classList.toggle('open');
        document.getElementById('sidebarOverlay')?.classList.toggle('open');
        return;
    }

    if (event.target.closest('[data-sidebar-close]')) {
        document.getElementById('sidebar')?.classList.remove('open');
        document.getElementById('sidebarOverlay')?.classList.remove('open');
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-confirm]');
    if (form && !window.confirm(form.dataset.confirm)) event.preventDefault();
});

document.addEventListener('click', (event) => {
    const row = event.target.closest('[data-row-href]');
    if (row && !event.target.closest('a, button, form, input, select, textarea')) {
        window.location = row.dataset.rowHref;
        return;
    }

    const node = event.target.closest('.tree [data-node-id]');
    if (node) {
        document.getElementById(`children-${node.dataset.nodeId}`)?.classList.toggle('open');
        document.getElementById(`toggle-${node.dataset.nodeId}`)?.classList.toggle('open');
    }
});
