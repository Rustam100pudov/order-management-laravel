<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg" style="background:#f8f9fa; border-bottom:1px solid #dee2e6;">
    <div class="container">
        <a class="navbar-brand fw-bold text-secondary" href="#">Система заказов</a>
        <div class="navbar-nav ms-auto">
            <form method="POST" action="/logout" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-secondary fw-bold px-4" style="color:#fff;">Выход</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="mb-4">Создание заказа</h2>

                    <form id="orderForm" autocomplete="off" novalidate>
                        <div id="orderError" class="alert alert-danger d-none" style="white-space:pre-wrap"></div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="fio" class="form-label">ФИО <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fio" name="customer_name" required>
                                <div class="invalid-feedback">Пожалуйста, введите ФИО.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="customer_phone" required>
                                <div class="invalid-feedback">Пожалуйста, введите телефон.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Почта</label>
                                <input type="email" class="form-control" id="email" name="customer_email">
                            </div>
                            <div class="col-md-6">
                                <label for="inn" class="form-label">ИНН</label>
                                <input type="text" class="form-control" id="inn" name="customer_inn">
                            </div>
                            <div class="col-md-6">
                                <label for="company" class="form-label">Название компании</label>
                                <input type="text" class="form-control" id="company" name="company_name">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Адрес</label>
                                <input type="text" class="form-control" id="address" name="customer_address">
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Товары</h5>

                        <div id="products-list">
                            <div class="row g-2 align-items-end product-row mb-2">
                                <div class="col-md-5">
                                    <label class="form-label">Наименование</label>
                                    <input type="text" class="form-control" name="products[0][name]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Кол-во</label>
                                    <input type="number" class="form-control" name="products[0][qty]" min="1" value="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ед. измерения</label>
                                    <select class="form-select" name="products[0][unit]" required>
                                        <option value="pieces">Штуки</option>
                                        <option value="sets">Комплекты</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary btn-remove-product d-none">&times;</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary" id="addProductBtn">Добавить товар</button>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary btn-lg" id="submitBtn">Создать заказ</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== Список заказов ===== --}}
            <div class="card shadow mt-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="m-0">Список заказов</h4>
                        <div>
                            <span id="ordersStatusMsg" class="text-success me-2 d-none">Сохранено</span>
                            <button id="reloadOrdersBtn" class="btn btn-outline-secondary btn-sm">Обновить</button>
                        </div>
                    </div>

                    <div id="ordersError" class="alert alert-danger d-none"></div>
                    <div id="ordersLoading" class="text-muted small mb-2">Загрузка заказов…</div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Дата</th>
                                <th>ФИО</th>
                                <th>Телефон</th>
                                <th>Компания</th>
                                <th>Адрес</th>
                                <th style="min-width:260px;">Товары</th>
                                <th>Статус</th>
                                <th style="min-width:200px;">Описание</th>
                                <th style="width:120px;">Действия</th>
                            </tr>
                            </thead>
                            <tbody id="orders-tbody">
                            {{-- Рендер через JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div id="ordersEmpty" class="text-muted small d-none">Пока нет заказов.</div>
                </div>
            </div>
            {{-- ===== /Список заказов ===== --}}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ========================
   Динамика товаров в форме
   ======================== */
let productIndex = 1;
document.getElementById('addProductBtn').addEventListener('click', function() {
    const productsList = document.getElementById('products-list');
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end product-row mb-2';
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" class="form-control" name="products[${productIndex}][name]" required placeholder="Наименование">
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="products[${productIndex}][qty]" min="1" value="1" required placeholder="Кол-во">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="products[${productIndex}][unit]" required>
                <option value="pieces">Штуки</option>
                <option value="sets">Комплекты</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-outline-secondary btn-remove-product">&times;</button>
        </div>
    `;
    productsList.appendChild(row);
    productIndex++;
    updateRemoveButtons();
});

function updateRemoveButtons() {
    const removeBtns = document.querySelectorAll('.btn-remove-product');
    removeBtns.forEach(btn => btn.classList.remove('d-none'));
    if (removeBtns.length === 1) removeBtns[0].classList.add('d-none');
}

document.getElementById('products-list').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-product')) {
        e.target.closest('.product-row').remove();
        updateRemoveButtons();
    }
});

/* ========================
   Работа с API заказов
   ======================== */
const ordersTbody = document.getElementById('orders-tbody');
const ordersError = document.getElementById('ordersError');
const ordersLoading = document.getElementById('ordersLoading');
const ordersEmpty = document.getElementById('ordersEmpty');
const ordersStatusMsg = document.getElementById('ordersStatusMsg');

document.getElementById('reloadOrdersBtn').addEventListener('click', fetchOrders);
document.addEventListener('DOMContentLoaded', () => {
    updateRemoveButtons();
    fetchOrders();
});

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function fmtDate(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    return isNaN(d) ? iso : d.toLocaleString('ru-RU');
}

function escapeHtml(str) {
    return (str || '').toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}

/* Статусы: нормализация <-> отображение */
function normalizeStatus(s) {
    const v = (s || '').toString().toLowerCase();
    if (['new','новый','нов.','нов'].includes(v) || v.includes('нов')) return 'new';
    if (['in_progress','в работе','work','processing'].includes(v) || v.includes('работ')) return 'in_progress';
    if (['completed','done','завершено','закрыт','закрыто','завершён'].includes(v) || v.includes('заверш') || v.includes('зак')) return 'completed';
    return 'new';
}
function statusLabel(val) {
    const v = normalizeStatus(val);
    return v === 'new' ? 'Новый' : v === 'in_progress' ? 'В работе' : 'Завершено';
}
function statusBadge(val) {
    const v = normalizeStatus(val);
    if (v === 'new') return '<span class="badge text-bg-secondary">Новый</span>';
    if (v === 'in_progress') return '<span class="badge text-bg-warning text-dark">В работе</span>';
    return '<span class="badge text-bg-success">Завершено</span>';
}
function statusSelect(current) {
    const v = normalizeStatus(current);
    return `
      <select class="form-select form-select-sm status-select">
        <option value="new" ${v==='new'?'selected':''}>Новый</option>
        <option value="in_progress" ${v==='in_progress'?'selected':''}>В работе</option>
        <option value="completed" ${v==='completed'?'selected':''}>Завершено</option>
      </select>
    `;
}

function mapUnit(u) {
    const v = (''+u).toLowerCase();
    if (v === 'pieces' || v === 'шт' || v === 'штуки') return 'шт.';
    if (v === 'sets'   || v === 'комплекты' || v === 'компл') return 'компл.';
    return u || '';
}
function productBadge(p) {
    const name = p.product_name || p.name || '';
    const qty  = p.quantity || p.qty || '';
    const unit = mapUnit(p.unit);
    return `<span class="badge text-bg-light border me-1 mb-1">${escapeHtml(`${name} — ${qty} ${unit}`.trim())}</span>`;
}

function rowHtml(o) {
    const customer = o.customer || {};
    const fio      = customer.fio || customer.name || o.customer_name || '';
    const phone    = customer.phone || o.customer_phone || '';
    const company  = customer.company || o.company_name || '';
    const address  = customer.address || o.customer_address || '';
    const items    = (o.items || o.products || []).map(productBadge).join(' ');
    const dateStr  = fmtDate(o.created_at || o.date);
    const status   = o.status || 'new';
    const desc     = o.description || o.note || o.notes || o.comment || '';

    const id = o.id ?? o.uuid ?? o.order_id; // подстраиваемся под разные схемы
    const editBtn = id ? `<button class="btn btn-sm btn-outline-secondary js-edit">Изменить</button>` :
                         `<button class="btn btn-sm btn-outline-secondary" disabled title="Нет id">Изменить</button>`;

    return `
      <tr data-id="${escapeHtml(id ?? '')}" data-editing="0">
        <td class="text-nowrap">${escapeHtml(dateStr)}</td>
        <td>${escapeHtml(fio)}</td>
        <td>${escapeHtml(phone)}</td>
        <td>${escapeHtml(company)}</td>
        <td>${escapeHtml(address)}</td>
        <td>${items}</td>
        <td class="status-cell">${statusBadge(status)}</td>
        <td class="desc-cell">${desc ? escapeHtml(desc) : '<span class="text-muted">—</span>'}</td>
        <td class="actions-cell">${editBtn}</td>
      </tr>
    `;
}

async function fetchOrders() {
    ordersStatusMsg.classList.add('d-none');
    ordersError.classList.add('d-none');
    ordersError.textContent = '';
    ordersLoading.classList.remove('d-none');
    ordersEmpty.classList.add('d-none');
    ordersTbody.innerHTML = '';

    try {
        const resp = await fetch('/api/orders', { headers: { 'Accept': 'application/json' } });
        if (!resp.ok) throw new Error(await resp.text() || ('HTTP ' + resp.status));
        const json = await resp.json();
        const orders = Array.isArray(json) ? json : (json.data || json.orders || []);
        if (!orders || orders.length === 0) {
            ordersEmpty.classList.remove('d-none');
            return;
        }
        orders.sort((a,b) => new Date(b.created_at || b.date || 0) - new Date(a.created_at || a.date || 0));
        ordersTbody.innerHTML = orders.map(rowHtml).join('');
    } catch (err) {
        ordersError.textContent = 'Не удалось получить список заказов: ' + (err.message || err);
        ordersError.classList.remove('d-none');
    } finally {
        ordersLoading.classList.add('d-none');
    }
}

/* ========================
   Редактирование строки
   ======================== */
ordersTbody.addEventListener('click', async (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;
    const tr = e.target.closest('tr');
    if (!tr) return;

    const editing = tr.getAttribute('data-editing') === '1';
    const id = tr.getAttribute('data-id');
    if (!id) return;

    const statusCell = tr.querySelector('.status-cell');
    const descCell   = tr.querySelector('.desc-cell');
    const actionsCell= tr.querySelector('.actions-cell');

    if (btn.classList.contains('js-edit')) {
        // Включаем режим редактирования
        const currentStatusText = statusCell.textContent.trim();
        const currentStatus = normalizeStatus(currentStatusText);
        const currentDesc   = descCell.textContent.trim() === '—' ? '' : descCell.textContent;

        statusCell.setAttribute('data-prev', currentStatus);
        descCell.setAttribute('data-prev', currentDesc);

        statusCell.innerHTML = statusSelect(currentStatus);
        descCell.innerHTML   = `<textarea class="form-control form-control-sm desc-input" rows="2" maxlength="1000" placeholder="Описание...">${escapeHtml(currentDesc)}</textarea>`;
        actionsCell.innerHTML = `
            <button class="btn btn-sm btn-success me-1 js-save">Сохранить</button>
            <button class="btn btn-sm btn-light js-cancel">Отмена</button>
        `;
        tr.setAttribute('data-editing', '1');
        return;
    }

    if (btn.classList.contains('js-cancel')) {
        // Отмена изменений
        const prevStatus = statusCell.getAttribute('data-prev') || 'new';
        const prevDesc   = descCell.getAttribute('data-prev') || '';
        statusCell.innerHTML = statusBadge(prevStatus);
        descCell.innerHTML   = prevDesc ? escapeHtml(prevDesc) : '<span class="text-muted">—</span>';
        actionsCell.innerHTML = `<button class="btn btn-sm btn-outline-secondary js-edit">Изменить</button>`;
        tr.setAttribute('data-editing', '0');
        return;
    }

    if (btn.classList.contains('js-save')) {
        // Сохранение
        const select = tr.querySelector('.status-select');
        const textarea = tr.querySelector('.desc-input');
        const newStatus = select ? select.value : 'new';
        const newDesc   = textarea ? textarea.value.trim() : '';

        // Локально блокируем кнопки
        btn.disabled = true;
        const cancelBtn = tr.querySelector('.js-cancel');
        if (cancelBtn) cancelBtn.disabled = true;

        try {
            await updateOrder(id, { status: newStatus, description: newDesc });
            ordersStatusMsg.textContent = 'Сохранено';
            ordersStatusMsg.classList.remove('d-none');
            await fetchOrders(); // перерисуем всю таблицу
        } catch (err) {
            ordersError.textContent = 'Не удалось сохранить изменения: ' + (err.message || err);
            ordersError.classList.remove('d-none');
            // Вернём кнопкам активность
            btn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
        }
        return;
    }
});

async function updateOrder(id, payload) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };
    const csrf = getCsrf();
    if (csrf) headers['X-CSRF-TOKEN'] = csrf;

    // Если у тебя PATCH — замени method на 'PATCH'
    const resp = await fetch(`/api/orders/${encodeURIComponent(id)}`, {
        method: 'PUT',
        headers,
        body: JSON.stringify(payload),
    });

    if (!resp.ok) {
        const text = await resp.text();
        try {
            const j = JSON.parse(text);
            throw new Error(j.message || text);
        } catch (_) {
            throw new Error(text || ('HTTP ' + resp.status));
        }
    }
    return resp.json().catch(() => ({}));
}

/* ========================
   Отправка формы (POST)
   ======================== */
document.getElementById('orderForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const form = event.target;
    if (!form.checkValidity()) {
        event.stopPropagation();
        form.classList.add('was-validated');
        return;
    }

    const formData = new FormData(form);
    const data = {
        customer_name:    formData.get('customer_name'),
        customer_phone:   formData.get('customer_phone'),
        customer_email:   formData.get('customer_email'),
        customer_inn:     formData.get('customer_inn'),
        company_name:     formData.get('company_name'),
        customer_address: formData.get('customer_address'),
        items: []
    };
    document.querySelectorAll('#products-list .product-row').forEach((row) => {
        data.items.push({
            product_name: row.querySelector('input[name^="products"][name$="[name]"]').value,
            quantity:     Number(row.querySelector('input[name^="products"][name$="[qty]"]').value || 0),
            unit:         row.querySelector('select[name^="products"][name$="[unit]"]').value
        });
    });

    const errorDiv = document.getElementById('orderError');
    errorDiv.classList.add('d-none');
    errorDiv.textContent = '';

    try {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        const csrf = getCsrf();
        if (csrf) headers['X-CSRF-TOKEN'] = csrf;

        const resp = await fetch('/api/orders', {
            method: 'POST',
            headers,
            body: JSON.stringify(data)
        });

        if (!resp.ok) {
            const text = await resp.text();
            try { const j = JSON.parse(text); throw new Error(j.message || text); }
            catch { throw new Error(text || ('HTTP ' + resp.status)); }
        }

        // успех
        form.reset();
        form.classList.remove('was-validated');
        const list = document.getElementById('products-list');
        list.innerHTML = `
            <div class="row g-2 align-items-end product-row mb-2">
                <div class="col-md-5">
                    <label class="form-label">Наименование</label>
                    <input type="text" class="form-control" name="products[0][name]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Кол-во</label>
                    <input type="number" class="form-control" name="products[0][qty]" min="1" value="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ед. измерения</label>
                    <select class="form-select" name="products[0][unit]" required>
                        <option value="pieces">Штуки</option>
                        <option value="sets">Комплекты</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-remove-product d-none">&times;</button>
                </div>
            </div>
        `;
        productIndex = 1;
        updateRemoveButtons();

        alert('Заказ успешно создан!');
        fetchOrders();

    } catch (e) {
        errorDiv.textContent = 'Ошибка отправки заказа: ' + e.message;
        errorDiv.classList.remove('d-none');
    }
});
</script>
</body>
</html>
