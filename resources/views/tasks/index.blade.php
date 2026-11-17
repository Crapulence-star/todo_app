<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .task-card { transition: transform 0.2s; }
        .task-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .status-pending { border-left: 5px solid #ffc107; }
        .status-in-progress { border-left: 5px solid #0dcaf0; }
        .status-done { border-left: 5px solid #198754; }
        .done-text { text-decoration: line-through; color: #6c757d; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <span class="navbar-brand mb-0 h1"><i class="fa-solid fa-list-check me-2"></i>Task Manager</span>
    </div>
</nav>

<div class="container pb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Проверьте введенные данные:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6 mb-2">
            <form method="GET" action="{{ route('tasks.index') }}" id="filterForm">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-filter"></i></span>
                    <select class="form-select" name="status" onchange="document.getElementById('filterForm').submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Все задачи</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Не выполненные</option>
                        <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>В процессе</option>
                        <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Выполненные</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="col-md-6 mb-2">
            <form method="GET" action="{{ route('tasks.index') }}" id="sortForm">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-sort"></i></span>
                    <select class="form-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                        <option value="date-new" {{ request('sort') == 'date-new' ? 'selected' : '' }}>Сначала новые</option>
                        <option value="date-old" {{ request('sort') == 'date-old' ? 'selected' : '' }}>Сначала старые</option>
                        <option value="alpha" {{ request('sort') == 'alpha' ? 'selected' : '' }}>По алфавиту</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($tasks as $task)
            @php
                $statusClass = match($task->status) {
                    'pending' => 'status-pending',
                    'in-progress' => 'status-in-progress',
                    'done' => 'status-done',
                    default => ''
                };
                $statusBadge = match($task->status) {
                    'pending' => 'bg-warning text-dark',
                    'in-progress' => 'bg-info text-dark',
                    'done' => 'bg-success',
                    default => 'bg-secondary'
                };
                $statusText = match($task->status) {
                    'pending' => 'Не выполнена',
                    'in-progress' => 'В процессе',
                    'done' => 'Выполнена',
                    default => 'Неизвестно'
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm task-card {{ $statusClass }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 {{ $task->status == 'done' ? 'done-text' : '' }}">{{ $task->title }}</h5>
                            <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                        </div>
                        <p class="card-text text-muted small mb-3">{{ $task->description ?? 'Без описания' }}</p>
                        <p class="card-text small mb-3">
                            <i class="fa-regular fa-calendar me-1"></i>
                            Срок: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') : 'Не указан' }}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Не выполнена</option>
                                <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>В процессе</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Выполнена</option>
                            </select>
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="description" value="{{ $task->description }}">
                            <input type="hidden" name="due_date" value="{{ $task->due_date }}">
                        </form>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить задачу?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('tasks.update', $task) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Редактировать задачу</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Название</label>
                                    <input type="text" class="form-control" name="title" value="{{ $task->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Описание</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $task->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Срок выполнения</label>
                                    <input type="date" class="form-control" name="due_date" value="{{ $task->due_date }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Статус</label>
                                    <select class="form-select" name="status">
                                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Не выполнена</option>
                                        <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>В процессе</option>
                                        <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Выполнена</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Задач пока нет</h4>
                <p class="text-muted">Нажмите на кнопку ниже, чтобы добавить первую задачу.</p>
            </div>
        @endforelse
    </div>
</div>

<button class="btn btn-primary rounded-circle shadow position-fixed bottom-0 end-0 m-4 p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; z-index: 1050;" data-bs-toggle="modal" data-bs-target="#createTaskModal">
    <i class="fa-solid fa-plus fa-xl"></i>
</button>

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Добавить задачу</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Срок выполнения</label>
                        <input type="date" class="form-control" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>