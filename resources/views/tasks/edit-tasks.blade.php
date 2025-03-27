
@extends('layouts.main')

@section('content')
    <h1>Edit Tasks</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('tasks.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Status</label>
            <input type='text' class='form-control' id='status' name='status' value='{{ $item->status }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Projects_id</label>
            <input type='text' class='form-control' id='projects_id' name='projects_id' value='{{ $item->projects_id }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Projects_workspaces_id</label>
            <input type='text' class='form-control' id='projects_workspaces_id' name='projects_workspaces_id' value='{{ $item->projects_workspaces_id }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
