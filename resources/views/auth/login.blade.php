<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Авторизация</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger text-center">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Логин">
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Пароль">
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">Войти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
