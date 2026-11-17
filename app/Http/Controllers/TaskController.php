<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Task::query();

            if ($request->has('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }

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
                $query->orderBy('created_at', 'desc');
            }

            $tasks = $query->get();

            Log::info('Загружен список задач', [
                'count' => $tasks->count(),
                'filter' => $request->status ?? 'all',
                'sort' => $request->sort ?? 'default',
            ]);

            return view('tasks.index', compact('tasks'));

        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке задач: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Не удалось загрузить задачи. Попробуйте позже.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
            ]);

            $task = Task::create($validated);

            Log::info('Создана новая задача', [
                'task_id' => $task->id,
                'title' => $task->title,
            ]);

            return redirect()->route('tasks.index')->with('success', 'Задача успешно создана!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Ошибка валидации при создании задачи', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);

            throw $e;

        } catch (\Exception $e) {
            Log::error('Ошибка при создании задачи: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return redirect()->back()->with('error', 'Не удалось создать задачу. Попробуйте позже.')->withInput();
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'status' => 'required|in:pending,in-progress,done',
            ]);

            $oldStatus = $task->status;
            $task->update($validated);

            Log::info('Задача обновлена', [
                'task_id' => $task->id,
                'title' => $task->title,
                'old_status' => $oldStatus,
                'new_status' => $task->status,
            ]);

            return redirect()->route('tasks.index')->with('success', 'Задача обновлена!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Ошибка валидации при обновлении задачи', [
                'task_id' => $task->id,
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);

            throw $e;

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении задачи: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Не удалось обновить задачу. Попробуйте позже.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $taskId = $task->id;
            $taskTitle = $task->title;
            $task->delete();

            Log::info('Задача удалена', [
                'task_id' => $taskId,
                'title' => $taskTitle,
            ]);

            return redirect()->route('tasks.index')->with('success', 'Задача удалена!');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении задачи: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Не удалось удалить задачу. Попробуйте позже.');
        }
    }
}