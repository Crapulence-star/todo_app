<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 1. Чтение задач (с сортировкой и фильтрацией)
    public function index(Request $request)
    {
        $query = Task::query();

        // Фильтрация по статусу
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Сортировка
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'date-new':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date-old':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'alpha':
                    $query->orderBy('title', 'asc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc'); // По умолчанию новые сверху
        }

        $tasks = $query->get();

        return view('tasks.index', compact('tasks'));
    }

    // 2. Создание задачи
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Задача успешно создана!');
    }

    // 3. Обновление задачи
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in-progress,done',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Задача обновлена!');
    }

    // 4. Удаление задачи
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Задача удалена!');
    }
}