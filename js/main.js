// ============================================
//  EventPro - Main JS Utilities
// ============================================

// ---- API Helper ----
const API = {
    base: '../php/',

    async get(endpoint, params = {}) {
        const url = new URL(this.base + endpoint, window.location.href);
        Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v));
        const res = await fetch(url);
        return res.json();
    },

    async post(endpoint, data = {}) {
        const res = await fetch(this.base + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    }
};

// ---- Toast Notifications ----
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const icons = { success: '✅', error: '❌', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${icons[type]}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// ---- Modal ----
function openModal(id) {
    document.getElementById(id)?.classList.add('active');
}
function closeModal(id) {
    document.getElementById(id)?.classList.remove('active');
}
// Close modal on overlay click
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

// ---- Tabs ----
function initTabs() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const group = tab.closest('.tabs');
            group.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(c => {
                c.classList.toggle('active', c.id === target);
            });
        });
    });
}

// ---- Formatters ----
function formatCurrency(amount) {
    return '৳ ' + parseFloat(amount || 0).toLocaleString('en-BD');
}
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-BD', { year: 'numeric', month: 'short', day: 'numeric' });
}
function formatStatus(status) {
    const map = {
        pending:   '<span class="badge badge-pending">Pending</span>',
        approved:  '<span class="badge badge-approved">Approved</span>',
        ongoing:   '<span class="badge badge-ongoing">Ongoing</span>',
        completed: '<span class="badge badge-completed">Completed</span>',
        cancelled: '<span class="badge badge-cancelled">Cancelled</span>',
        paid:      '<span class="badge badge-paid">Paid</span>',
        active:    '<span class="badge badge-active">Active</span>',
        inactive:  '<span class="badge badge-inactive">Inactive</span>',
    };
    return map[status] || `<span class="badge">${status}</span>`;
}

function getCategoryEmoji(cat) {
    const map = { wedding:'💍', birthday:'🎂', conference:'🎤', corporate:'🏢', other:'🎉' };
    return map[cat] || '🎉';
}

// ---- Sidebar Toggle ----
function initSidebar() {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    toggle?.addEventListener('click', () => sidebar?.classList.toggle('open'));
}

// ---- Auth Check ----
async function checkAuth(expectedRole) {
    try {
        const res = await API.get('users.php', { action: 'me' });
        if (!res.success || (expectedRole && res.data.role !== expectedRole)) {
            window.location.href = '../index.html';
        }
        return res.data;
    } catch {
        window.location.href = '../index.html';
    }
}

// ---- Stars Rating ----
function renderStars(rating) {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}

// ---- Table Search ----
function initTableSearch(inputId, tableId) {
    document.getElementById(inputId)?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ---- Confirm Dialog ----
function confirmAction(message) {
    return confirm(message);
}

// ---- Init ----
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initSidebar();
});