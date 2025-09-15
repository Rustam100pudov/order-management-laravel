## Быстрый старт (без Docker)

1. Клонируйте репозиторий:
	```
	git clone https://github.com/Rustam100pudov/order-management-laravel.git
	cd order-management-laravel
	```

2. Установите зависимости:
	```
	composer install
	npm install
	```

3. Скопируйте и настройте .env:
	```
	cp .env.example .env
	```
	Отредактируйте параметры подключения к базе данных в `.env` при необходимости.

4. Сгенерируйте ключ приложения:
	```
	php artisan key:generate
	```

5. Выполните миграции и наполните тестовыми данными:
	```
	php artisan migrate --seed
	```

6. Соберите фронтенд:
	```
	npm run build
	```


7. Запустите сервер:
	```
	php artisan serve
	```
		Откройте в браузере http://127.0.0.1:8000/login для входа в систему.

---
## Запуск с помощью Docker

1. Убедитесь, что у вас установлен Docker и Docker Compose.

2. Скопируйте файл окружения:
	```
	cp .env.example .env
	```
	При необходимости отредактируйте параметры подключения к БД в `.env`.

3. Соберите и запустите контейнеры:
	```
	docker compose up --build
	```

4. После запуска приложение будет доступно по адресу:
	- http://127.0.0.1:8000
	- http://127.0.0.1:8000/operator — Оператор
	- http://127.0.0.1:8000/manager — Руководитель

5. Для применения миграций и наполнения тестовыми данными выполните в контейнере:
	```
	docker compose exec app php artisan migrate --seed
	```

6. Для сборки фронтенда (если требуется):
	```
	docker compose exec app npm run build
	```

---


## Тестовые пользователи

**Руководитель**  
Email: testrole@example.com
Пароль: testpass123

**Оператор**  
Email: operator@example.com  
Пароль: password123

## Возможности ролей

**Оператор:**
- Создание заказов через форму
- Просмотр и поиск своих заказов
- Редактирование статуса и описания заказа (inline)
- Фильтрация заказов по дате, статусу, поиску

**Руководитель:**
- Просмотр всех заказов
- Фильтрация и поиск по всем заказам
- Просмотр описания, товаров, клиентов
- Просмотр статистики по заказам

---

**Роли:**
- Оператор: http://127.0.0.1:8000/operator
- Руководитель: http://127.0.0.1:8000/manager

**Требования:**
- PHP 8.2+
- Composer
- Node.js и npm
- SQLite (по умолчанию) или другая поддерживаемая СУБД
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


