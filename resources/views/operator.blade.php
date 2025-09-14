<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <form id="orderForm" method="POST" action="#" novalidate autocomplete="off">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="fio" class="form-label">ФИО <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fio" name="fio" required>
                                    <div class="invalid-feedback">Пожалуйста, введите ФИО.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                    <div class="invalid-feedback">Пожалуйста, введите телефон.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Почта</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="inn" class="form-label">ИНН</label>
                                    <input type="text" class="form-control" id="inn" name="inn">
                                </div>
                                <div class="col-md-6">
                                    <label for="company" class="form-label">Название компании</label>
                                    <input type="text" class="form-control" id="company" name="company">
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Адрес</label>
                                    <input type="text" class="form-control" id="address" name="address">
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
                                            <option value="Штуки">Штуки</option>
                                            <option value="Комплекты">Комплекты</option>
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
                    <option value="Штуки">Штуки</option>
                    <option value="Комплекты">Комплекты</option>
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

    // Bootstrap валидация
    (() => {
        'use strict';
        const form = document.getElementById('orderForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    })();
    </script>
</body>
</html>
