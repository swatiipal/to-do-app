<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Todoapp extends Component
{
    use WithPagination;

    #[Validate('required|min:3')] 
    public $name;

    public $search;
    public $editingTodoId;

    #[Validate('required|min:3')] 
    public $editingTodoName;

    public function create(){
        $validated = $this->validateOnly('name');
        Todo::create($validated);
        $this->reset('name');
        session()->flash('success','Todo created successfully...');
        $this->resetPage();
    }
    public function edit($todoId){
        $todo = Todo::find($todoId);
        $this->editingTodoId = $todoId;
        $this->editingTodoName = Todo::find($todoId)->name;
    }
    public function update(){
        $validated = $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoId)->update([
            'name' => $this->editingTodoName
        ]);
        session()->flash('success','Todo Updated Successfully...');
        $this->resetPage();
        return;
    }

    public function cancelEdit(){
        $this->reset('editingTodoId','editingTodoName');
    }

    public function delete($todoId){
        try{
            Todo::findorfail($todoId)->delete();
        }catch(Exception $e){
            session()->flash('error','Failed to delete Todo...');
            return;
        }
    }

    public function toggle($todoId){
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function render()
    {
        // $todos = Todo::paginate(10);

        return view('livewire.todoapp',[
            'todos' => Todo::latest()->where('name','like',"%{$this->search}%")->paginate(5)

        ]);
    }
}
