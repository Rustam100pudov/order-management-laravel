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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Система заказов</a>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="/logout" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Выход</button>
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
                                        <button type="button" class="btn btn-danger btn-remove-product d-none">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary" id="addProductBtn">Добавить товар</button>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">Создать заказ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Динамическое добавление товаров
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
                <button type="button" class="btn btn-danger btn-remove-product">&times;</button>
            </div>
        `;
        productsList.appendChild(row);
        productIndex++;
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const removeBtns = document.querySelectorAll('.btn-remove-product');
        removeBtns.forEach(btn => btn.classList.remove('d-none'));
        if (removeBtns.length === 1) {
            removeBtns[0].classList.add('d-none');
        }
    }

    // Удаление товара
    document.getElementById('products-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-product')) {
            e.target.closest('.product-row').remove();
            updateRemoveButtons();
        }
    });

    // AJAX отправка заказа через API
    document.getElementById('orderForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        const form = event.target;
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        // Сбор данных
        const formData = new FormData(form);
        const data = {
            customer_name: formData.get('customer_name'),
            customer_phone: formData.get('customer_phone'),
            customer_email: formData.get('customer_email'),
            customer_inn: formData.get('customer_inn'),
            company_name: formData.get('company_name'),
            customer_address: formData.get('customer_address'),
            items: []
        };
        const products = document.querySelectorAll('#products-list .product-row');
        products.forEach((row, idx) => {
            data.items.push({
                product_name: row.querySelector('input[name^="products"][name$="[name]"]').value,
                quantity: row.querySelector('input[name^="products"][name$="[qty]"]').value,
                unit: row.querySelector('select[name^="products"][name$="[unit]"]').value
            });
        });
        // Отправка
        const errorDiv = document.getElementById('orderError');
        errorDiv.classList.add('d-none');
        errorDiv.textContent = '';
        try {
            const resp = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (resp.ok) {
                form.reset();
                form.classList.remove('was-validated');
                errorDiv.classList.add('d-none');
                errorDiv.textContent = '';
                alert('Заказ успешно создан!');
            } else {
                let text = await resp.text();
                let errMsg = '';
                try {
                    const err = JSON.parse(text);
                    errMsg = err.message || JSON.stringify(err);
                } catch (e) {
                    errMsg = text;
                }
                errorDiv.textContent = 'Ошибка: ' + errMsg;
                errorDiv.classList.remove('d-none');
            }
        } catch (e) {
            errorDiv.textContent = 'Ошибка отправки заказа: ' + e.message;
            errorDiv.classList.remove('d-none');
        }
    });
    </script>
</body>
</html>
