<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50', as: 'todo')]
    public $task;

    public $search;

    public $editingTodoId;

    #[Rule('required|min:3|max:50', as: 'todo')]
    public $editingTodoTask;

    public function create() 
    {
        //validate
        $validated = $this->validateOnly('task');

        //create the todo
        Todo::create($validated);

        //clear the input field
        $this->reset('task');

        //send flash message
        session()->flash('success', 'Todo created!');

        //reset to first page
        $this->resetPage();
    }

    public function delete($todoId) 
    {
        Todo::find($todoId)->delete();
    }

    public function toggle($todoId)
    {
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoId)
    {
        $this->editingTodoId = $todoId;
        $this->editingTodoTask = Todo::find($todoId)->task;
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoId', 'editingTodoTask');
    }

    public function update()
    {
        //validate
        $this->validateOnly('editingTodoTask');

        //update todo
        Todo::find($this->editingTodoId)->update([
            'task' => $this->editingTodoTask
        ]);

        //close edit
        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('task', 'like', "%{$this->search}%")->paginate(3)
        ]);
    }
}
