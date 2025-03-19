
@extends('layouts.main')

@section('content')
    <h1>Create a new sites</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('sites.store') }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Phonenumber</label> <br>
            <small>(Ex: 09xxx... 11 characters)</small>
            <input type='number' class='form-control' id='phonenumber' name='phonenumber' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Create</button>
            </form>
        </div>
    </div>

@endsection
