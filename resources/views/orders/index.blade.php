<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список заказов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
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

    <div class="container mt-4">
    <h2>Список заказов</h2>
    <div id="ordersAlert"></div>
    <div class="row mb-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label mb-1">Поиск</label>
            <input type="text" class="form-control" id="searchInput" placeholder="ФИО, компания, телефон, товар">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">С даты</label>
            <input type="date" class="form-control" id="fromDate">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">По дату</label>
            <input type="date" class="form-control" id="toDate">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Статус</label>
            <select class="form-select" id="statusFilter">
                <option value="">Все</option>
                <option value="new">Новые</option>
                <option value="in_progress">В работе</option>
                <option value="completed">Завершённые</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="d-flex gap-2 w-100">
                <button class="btn btn-secondary filter-btn flex-fill" onclick="loadOrders()">Применить</button>
                <button class="btn btn-outline-secondary filter-btn flex-fill" onclick="resetFilters()">Сброс</button>
                <button class="btn btn-outline-secondary filter-btn flex-fill" onclick="showStatistics()">Статистика</button>
            </div>
        </div>
    </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" style="background:#fff;">
                <thead class="table-light align-middle">
                    <tr>
                        <th>Дата</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>ИНН</th>
                        <th>Компания</th>
                        <th>Адрес</th>
                        <th style="min-width:180px">Товар</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody id="ordersTable"></tbody>
            </table>
        </div>
        
        <nav>
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>

    <!-- Модальное окно статистики -->
    <div class="modal fade" id="statisticsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Статистика заказов</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="statisticsContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .filter-btn {
            min-width: 80px;
            min-height: 32px;
            font-weight: 500;
            font-size: 0.82rem;
            padding: 0.18rem 0.6rem;
        }
        .gap-2 { gap: 0.5rem !important; }
        @media (max-width: 991px) {
            .filter-btn { min-width: 65px; font-size: 0.78rem; }
        }
    </style>
    <script>
        let currentPage = 1;

        function loadOrders(page = 1) {
            currentPage = page;
            const params = new URLSearchParams();
            const search = document.getElementById('searchInput').value;
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const status = document.getElementById('statusFilter').value;
            if (search) params.append('search', search);
            if (from) params.append('from', from);
            if (to) params.append('to', to);
            if (status) params.append('status', status);
            params.append('page', page);
            const alertDiv = document.getElementById('ordersAlert');
            alertDiv.innerHTML = '';
            fetch(`/api/orders?${params}`)
                .then(async response => {
                    if (!response.ok) {
                        let message = 'Ошибка загрузки заказов';
                        try {
                            const data = await response.json();
                            if (data && data.message) message = data.message;
                        } catch {}
                        alertDiv.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                        return {data: []};
                    }
                    return response.json();
                })
                .then(data => {
                    displayOrders(data.data || []);
                    displayPagination(data);
                });
        }

        function displayOrders(orders) {
            const tbody = document.getElementById('ordersTable');
            tbody.innerHTML = '';
            orders.forEach(order => {
                const row = document.createElement('tr');
                // Многострочный вывод товаров
                const items = order.items.map(item => `${item.product_name}`).join('<br>');
                row.innerHTML = `
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    <td>${order.customer_name}</td>
                    <td>${order.customer_phone}</td>
                    <td>${order.customer_inn || '-'}</td>
                    <td>${order.company_name || '-'}</td>
                    <td>${order.customer_address || '-'}</td>
                    <td style="white-space:pre-line">${items}</td>
                    <td>${order.description ? order.description : ''}</td>
                `;
                tbody.appendChild(row);
            });
        }
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('fromDate').value = '';
            document.getElementById('toDate').value = '';
            document.getElementById('statusFilter').value = '';
            loadOrders();
        }

        function getStatusBadge(status) {
            const badges = {
                'new': 'bg-primary',
                'in_progress': 'bg-warning',
                'completed': 'bg-success'
            };
            return badges[status] || 'bg-secondary';
        }

        function getStatusText(status) {
            const texts = {
                'new': 'Новый',
                'in_progress': 'В работе',
                'completed': 'Завершён'
            };
            return texts[status] || status;
        }

        function displayPagination(data) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            for (let i = 1; i <= data.last_page; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === data.current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadOrders(${i})">${i}</a>`;
                pagination.appendChild(li);
            }
        }

        function showStatistics() {
            const alertDiv = document.getElementById('ordersAlert');
            alertDiv.innerHTML = '';
            fetch('/api/orders/statistics')
                .then(async response => {
                    if (!response.ok) {
                        let message = 'Ошибка загрузки статистики';
                        try {
                            const data = await response.json();
                            if (data && data.message) message = data.message;
                        } catch {}
                        alertDiv.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) return;
                    const content = document.getElementById('statisticsContent');
                    content.innerHTML = `
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4>${data.new}</h4>
                                <p>Новых заказов</p>
                            </div>
                            <div class="col-md-3">
                                <h4>${data.in_progress}</h4>
                                <p>В работе</p>
                            </div>
                            <div class="col-md-3">
                                <h4>${data.completed}</h4>
                                <p>Завершённых</p>
                            </div>
                            <div class="col-md-3">
                                <h4>${data.total}</h4>
                                <p>Всего заказов</p>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('statisticsModal')).show();
                });
        }

        // Загрузка при открытии страницы
        loadOrders();

        // Обработчики событий
        document.getElementById('searchInput').addEventListener('input', () => loadOrders());
        document.getElementById('dateFilter').addEventListener('change', () => loadOrders());
        document.getElementById('statusFilter').addEventListener('change', () => loadOrders());
    </script>
</body>
</html>
