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
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('task', 'like', "%{$this->search}%")->paginate(3)
        ]);
    }
}
