<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager | Dark Tech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-base: #0a0b10;
            --bg-surface: rgba(22, 24, 35, 0.7);
            --bg-surface-hover: rgba(30, 33, 48, 0.9);
            --accent-cyan: #00e5ff;
            --accent-purple: #8a2be2;
            --accent-green: #00e676;
            --accent-yellow: #ffd600;
            --text-main: #f1f3f5;
            --text-muted: #a0aab8;
            --border-light: rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Живой фон: анимированные сферы и сетка */
        .bg-animation-container {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            overflow: hidden;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatBlob 20s infinite ease-in-out alternate;
        }

        .blob-1 {
            width: 400px; height: 400px;
            background: var(--accent-purple);
            top: -10%; left: -10%;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 300px; height: 300px;
            background: var(--accent-cyan);
            bottom: -10%; right: -5%;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 250px; height: 250px;
            background: #2d46b9;
            top: 40%; left: 40%;
            animation-delay: -10s;
            opacity: 0.2;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 30px) scale(1.1); }
            100% { transform: translate(-30px, 60px) scale(0.9); }
        }

        /* Навигация */
        .navbar {
            background: rgba(10, 11, 16, 0.8) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-light);
        }
        .navbar-brand {
            color: var(--text-main) !important;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .navbar-brand i { color: var(--accent-cyan); }

        /* Формы и инпуты */
        .form-select, .form-control, .input-group-text {
            background-color: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-light);
            color: var(--text-main);
            transition: all 0.3s ease;
        }
        .form-select:focus, .form-control:focus {
            background-color: rgba(0, 0, 0, 0.6);
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 229, 255, 0.15);
            color: var(--text-main);
        }
        .form-select option { background-color: var(--bg-base); color: var(--text-main); }
        .input-group-text { border-right: none; color: var(--text-muted); }

        /* Карточки задач */
        .task-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .task-card:hover {
            background: var(--bg-surface-hover);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.8);
        }

        /* Аккуратные индикаторы статуса */
        .task-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 3px;
            background: transparent;
            transition: background 0.3s ease;
        }
        .task-card.status-pending::before { background: var(--accent-yellow); }
        .task-card.status-in-progress::before { background: var(--accent-cyan); }
        .task-card.status-done::before { background: var(--accent-green); }

        .task-card .card-body { padding: 1.5rem 1.5rem 1rem; flex-grow: 1; }
        
        .task-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .task-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-date {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 10px;
            border-radius: 6px;
        }
        .task-date i { color: var(--accent-cyan); }

        .done-text {
            text-decoration: line-through;
            color: var(--text-muted) !important;
            opacity: 0.6;
        }

        /* Бейджи статусов */
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid transparent;
        }
        .badge-pending { background: rgba(255, 214, 0, 0.1); color: var(--accent-yellow); border-color: rgba(255, 214, 0, 0.3); }
        .badge-in-progress { background: rgba(0, 229, 255, 0.1); color: var(--accent-cyan); border-color: rgba(0, 229, 255, 0.3); }
        .badge-done { background: rgba(0, 230, 118, 0.1); color: var(--accent-green); border-color: rgba(0, 230, 118, 0.3); }

        /* Подвал карточки */
        .task-card .card-footer {
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid var(--border-light);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Кнопки */
        .btn-primary {
            background: var(--text-main);
            border: none;
            color: var(--bg-base);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #fff;
            color: #000;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }
        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-light);
            color: var(--text-muted);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        
        .btn-icon {
            width: 32px; height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-edit { color: var(--text-muted); background: rgba(255,255,255,0.05); border: none; }
        .btn-edit:hover { color: var(--accent-cyan); background: rgba(0, 229, 255, 0.1); }
        .btn-delete { color: var(--text-muted); background: rgba(255,255,255,0.05); border: none; }
        .btn-delete:hover { color: #ff4757; background: rgba(255, 71, 87, 0.1); }

        /* Плавающая кнопка добавления */
        .btn-add-task {
            background: var(--text-main);
            color: var(--bg-base);
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-add-task:hover {
            background: var(--accent-cyan);
            color: #000;
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.5);
        }

        /* Модальные окна */
        .modal-content {
            background: rgba(20, 22, 32, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            color: var(--text-main);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .modal-header, .modal-footer { border-color: var(--border-light); }
        .modal-title { font-weight: 600; }
        .btn-close { filter: invert(1) brightness(0.7); }
        
        /* Селект в карточке */
        .status-select {
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-light);
            color: var(--text-muted);
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
        }
        .status-select:focus { outline: none; border-color: var(--accent-cyan); }
    </style>
</head>
<body>

<div class="bg-animation-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1"><i class="fa-solid fa-layer-group me-2"></i>TaskFlow</span>
    </div>
</nav>

<div class="container pb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0" role="alert" style="background: rgba(0,230,118,0.1); color: var(--accent-green);">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0" role="alert" style="background: rgba(255,71,87,0.1); color: #ff4757;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show border-0" role="alert" style="background: rgba(255,214,0,0.1); color: var(--accent-yellow);">
            <strong>Ошибка:</strong>
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

    <div class="row g-4">
        @forelse($tasks as $task)
            @php
                $statusClass = match($task->status) {
                    'pending' => 'status-pending',
                    'in-progress' => 'status-in-progress',
                    'done' => 'status-done',
                    default => ''
                };
                $statusBadge = match($task->status) {
                    'pending' => 'badge-pending',
                    'in-progress' => 'badge-in-progress',
                    'done' => 'badge-done',
                    default => ''
                };
                $statusText = match($task->status) {
                    'pending' => 'Не выполнена',
                    'in-progress' => 'В процессе',
                    'done' => 'Выполнена',
                    default => 'Неизвестно'
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card task-card {{ $statusClass }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="task-title {{ $task->status == 'done' ? 'done-text' : '' }}">{{ $task->title }}</h5>
                            <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                        </div>
                        <p class="task-desc">{{ $task->description ?? 'Описание отсутствует...' }}</p>
                        <div class="task-date">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : 'Без срока' }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Не выполнена</option>
                                <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>В процессе</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Выполнена</option>
                            </select>
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="description" value="{{ $task->description }}">
                            <input type="hidden" name="due_date" value="{{ $task->due_date }}">
                        </form>
                        <div class="d-flex gap-2">
                            <button class="btn btn-icon btn-edit" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить задачу?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
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
                                    <label class="form-label text-muted small mb-1">Название</label>
                                    <input type="text" class="form-control" name="title" value="{{ $task->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Описание</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $task->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Срок выполнения</label>
                                    <input type="date" class="form-control" name="due_date" value="{{ $task->due_date }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Статус</label>
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
                <div class="mb-3"><i class="fa-solid fa-clipboard-list fa-3x text-muted opacity-50"></i></div>
                <h4 class="text-muted">Список пуст</h4>
                <p class="text-muted small">Создайте первую задачу, нажав на кнопку ниже.</p>
            </div>
        @endforelse
    </div>
</div>

<button class="btn btn-add-task rounded-circle shadow position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; z-index: 1050;" data-bs-toggle="modal" data-bs-target="#createTaskModal">
    <i class="fa-solid fa-plus fa-lg"></i>
</button>

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Новая задача</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Название</label>
                        <input type="text" class="form-control" name="title" placeholder="Что нужно сделать?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Описание</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Детали задачи..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Срок выполнения</label>
                        <input type="date" class="form-control" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>