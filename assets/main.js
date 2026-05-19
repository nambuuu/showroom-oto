// assets/js/main.js - Global UI utilities (vanilla JS + jQuery)

/* -------------------------------------------------
   Sidebar functionality
--------------------------------------------------- */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main-content');
    const isCollapsed = sidebar.classList.toggle('collapsed');
    // store state
    localStorage.setItem('sidebarCollapsed', isCollapsed);
}

function initSidebar() {
    const saved = localStorage.getItem('sidebarCollapsed') === 'true';
    const sidebar = document.querySelector('.sidebar');
    if (saved) sidebar.classList.add('collapsed');
    // auto collapse on small screens
    function handleResize() {
        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
        } else if (!saved) {
            sidebar.classList.remove('collapsed');
        }
    }
    window.addEventListener('resize', handleResize);
    handleResize();

    // active nav based on URL
    const path = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar nav a').forEach(a => {
        if (a.getAttribute('href').includes(path)) a.classList.add('active');
    });
}

/* -------------------------------------------------
   Table utilities
--------------------------------------------------- */
function initDataTable(selector) {
    const $table = $(selector);
    if ($table.length === 0) return;

    // Add search input
    const $search = $('<input>', {
        type: 'search',
        class: 'form-control mb-2',
        placeholder: 'Tìm kiếm...'
    });
    $table.before($search);

    $search.on('keyup', function () {
        const term = $(this).val().toLowerCase();
        $table.find('tbody tr').each(function () {
            const txt = $(this).text().toLowerCase();
            $(this).toggle(txt.includes(term));
        });
    });

    // Simple pagination – 10 rows per page
    const rowsPerPage = 10;
    const $rows = $table.find('tbody tr');
    const totalPages = Math.ceil($rows.length / rowsPerPage);
    let currentPage = 1;

    const $pagination = $('<div>', { class: 'mt-2 pagination' });
    function renderPage(page) {
        currentPage = page;
        $rows.hide();
        $rows.slice((page - 1) * rowsPerPage, page * rowsPerPage).show();
        $pagination.empty();
        for (let i = 1; i <= totalPages; i++) {
            const $link = $('<a>', {
                href: '#',
                text: i,
                class: i === page ? 'mx-1 fw-bold' : 'mx-1'
            }).on('click', e => {
                e.preventDefault();
                renderPage(i);
            });
            $pagination.append($link);
        }
    }
    renderPage(1);
    $table.after($pagination);

    // Row hover highlight (already via CSS, but ensure class)
    $table.find('tbody tr').hover(
        function () { $(this).addClass('table-hover'); },
        function () { $(this).removeClass('table-hover'); }
    );

    // Bulk select checkbox
    const $theadCheckbox = $table.find('thead input[type=checkbox]');
    const $rowCheckboxes = $table.find('tbody input[type=checkbox]');
    $theadCheckbox.on('change', function () {
        const checked = $(this).prop('checked');
        $rowCheckboxes.prop('checked', checked);
    });
}

/* -------------------------------------------------
   Form validation
--------------------------------------------------- */
function validateForm(formId) {
    const $form = $(`#${formId}`);
    let valid = true;
    $form.find('.error').remove();
    $form.find('[required]').each(function () {
        const $el = $(this);
        if (!$el.val().trim()) {
            valid = false;
            $el.after('<div class="error">Trường này là bắt buộc.</div>');
        }
    });
    // email validation
    const $email = $form.find('input[type=email]');
    if ($email.length) {
        const emailVal = $email.val().trim();
        const emailRegex = /^[\w.-]+@[\w.-]+\.\w+$/;
        if (emailVal && !emailRegex.test(emailVal)) {
            valid = false;
            $email.after('<div class="error">Định dạng email không hợp lệ.</div>');
        }
    }
    // phone VN validation (optional) – expects input[name=phone]
    const $phone = $form.find('input[name=phone]');
    if ($phone.length) {
        const phoneVal = $phone.val().trim();
        const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
        if (phoneVal && !phoneRegex.test(phoneVal)) {
            valid = false;
            $phone.after('<div class="error">Số điện thoại không hợp lệ.</div>');
        }
    }
    // remove error on input
    $form.find('input, textarea, select').on('input', function () {
        $(this).next('.error').remove();
    });
    return valid;
}

function confirmDelete(message) {
    return new Promise(resolve => {
        const $modal = $(`
      <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content bg-dark text-light">
            <div class="modal-header">
              <h5 class="modal-title">Xác nhận</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">${message}</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-danger" id="confirmYes">Xóa</button>
            </div>
          </div>
        </div>
      </div>`);
        $modal.modal('show');
        $modal.find('#confirmYes').on('click', () => {
            $modal.modal('hide');
            resolve(true);
        });
        $modal.on('hidden.bs.modal', () => $modal.remove());
    });
}

/* -------------------------------------------------
   Toast notifications
--------------------------------------------------- */
function showToast(message, type = 'info') {
    const containerId = 'toastContainer';
    let $container = $(`#${containerId}`);
    if ($container.length === 0) {
        $container = $(`<div id="${containerId}" class="toast-container"></div>`);
        $('body').append($container);
    }
    const $toast = $(`
    <div class="toast ${type}">
      <span>${message}</span>
      <button class="close" type="button">&times;</button>
    </div>`);
    $container.append($toast);
    $toast.find('.close').on('click', () => $toast.remove());
    setTimeout(() => $toast.fadeOut(400, () => $toast.remove()), 3000);
}

/* -------------------------------------------------
   Image upload preview & drag‑drop
--------------------------------------------------- */
function previewImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const files = input.files;
    preview.innerHTML = '';
    Array.from(files).forEach(file => {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            showToast('Định dạng ảnh không hợp lệ (jpeg/png/webp).', 'error');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showToast('Kích thước ảnh không được vượt quá 5 MB.', 'error');
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-img';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// Drag & drop zone (multiple images)
function initDropzone(dropzoneId, inputId, previewId) {
    const dz = document.getElementById(dropzoneId);
    const input = document.getElementById(inputId);
    dz.addEventListener('dragover', e => {
        e.preventDefault();
        dz.classList.add('dragover');
    });
    dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('dragover');
        input.files = e.dataTransfer.files;
        previewImage(inputId, previewId);
    });
}

/* -------------------------------------------------
   Utility helpers
--------------------------------------------------- */
function formatPrice(num) {
    return new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yy = d.getFullYear();
    return `${dd}/${mm}/${yy}`;
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Sao chép thành công!', 'success');
    }).catch(() => showToast('Không thể sao chép.', 'error'));
}

/* -------------------------------------------------
   Document ready – initialise components
--------------------------------------------------- */
$(document).ready(() => {
    initSidebar();
    // Example initialisations (adjust selectors as needed)
    // initDataTable('.my-table');
});


