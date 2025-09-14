<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Система заказов</a>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Выход</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
    <h2>Создание нового заказа</h2>
    <div id="orderAlert"></div>
    <form id="orderForm">
            <div class="row">
                <div class="col-md-6">
                    <h4>Информация о клиенте</h4>
                    <div class="mb-3">
                        <label>ФИО *</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Телефон *</label>
                        <input type="tel" class="form-control" name="customer_phone" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="customer_email">
                    </div>
                    <div class="mb-3">
                        <label>ИНН</label>
                        <input type="text" class="form-control" name="customer_inn">
                    </div>
                    <div class="mb-3">
                        <label>Название компании</label>
                        <input type="text" class="form-control" name="company_name">
                    </div>
                    <div class="mb-3">
                        <label>Адрес</label>
                        <textarea class="form-control" name="customer_address"></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h4>Товары</h4>
                    <div id="itemsContainer">
                        <div class="item-row mb-3 border p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Наименование</label>
                                    <input type="text" class="form-control" name="items[0][product_name]" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Количество</label>
                                    <input type="number" class="form-control" name="items[0][quantity]" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Единица</label>
                                    <select class="form-control" name="items[0][unit]" required>
                                        <option value="pieces">Штуки</option>
                                        <option value="sets">Комплекты</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" onclick="addItem()">Добавить товар</button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Создать заказ</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let itemIndex = 1;
        
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'item-row mb-3 border p-3';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <label>Наименование</label>
                        <input type="text" class="form-control" name="items[${itemIndex}][product_name]" required>
                    </div>
                    <div class="col-md-3">
                        <label>Количество</label>
                        <input type="number" class="form-control" name="items[${itemIndex}][quantity]" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label>Единица</label>
                        <select class="form-control" name="items[${itemIndex}][unit]" required>
                            <option value="pieces">Штуки</option>
                            <option value="sets">Комплекты</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.parentElement.remove()">Удалить</button>
            `;
            container.appendChild(newItem);
            itemIndex++;
        }

        document.getElementById('orderForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertDiv = document.getElementById('orderAlert');
            alertDiv.innerHTML = '';
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            // Обработка массива товаров
            const items = [];
            Object.keys(data).forEach(key => {
                if (key.startsWith('items[')) {
                    const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        if (!items[index]) items[index] = {};
                        items[index][field] = data[key];
                    }
                }
            });
            const payload = {
                customer_name: data.customer_name,
                customer_phone: data.customer_phone,
                customer_email: data.customer_email,
                customer_inn: data.customer_inn,
                company_name: data.company_name,
                customer_address: data.customer_address,
                items: items.filter(item => item.product_name)
            };
            try {
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                if (response.ok) {
                    alertDiv.innerHTML = '<div class="alert alert-success">Заказ успешно создан!</div>';
                    e.target.reset();
                    document.getElementById('itemsContainer').innerHTML = `
                        <div class="item-row mb-3 border p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Наименование</label>
                                    <input type="text" class="form-control" name="items[0][product_name]" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Количество</label>
                                    <input type="number" class="form-control" name="items[0][quantity]" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Единица</label>
                                    <select class="form-control" name="items[0][unit]" required>
                                        <option value="pieces">Штуки</option>
                                        <option value="sets">Комплекты</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    itemIndex = 1;
                } else {
                    const data = await response.json();
                    let message = 'Ошибка при создании заказа';
                    if (data && data.errors) {
                        message = Object.values(data.errors).flat().join('<br>');
                    } else if (data && data.message) {
                        message = data.message;
                    }
                    alertDiv.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="alert alert-danger">Ошибка при создании заказа</div>';
            }
        });
    </script>
</body>
</html>
