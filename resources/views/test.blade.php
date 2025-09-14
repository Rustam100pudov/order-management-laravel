++ test.blade.php
@extends('welcome')

@section('content')
<div class="container mt-5">
    <h2>Тестовая страница</h2>
    <form>
        <div class="mb-3">
            <label for="field1" class="form-label">Поле 1</label>
            <input type="text" class="form-control" id="field1" name="field1" placeholder="Введите что-нибудь">
        </div>
        <div class="mb-3">
            <label for="field2" class="form-label">Поле 2</label>
            <input type="text" class="form-control" id="field2" name="field2" placeholder="Введите что-нибудь ещё">
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
</div>
@endsection