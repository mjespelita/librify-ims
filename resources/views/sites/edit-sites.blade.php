
@extends('layouts.main')

@section('content')
    <h1>Edit Sites</h1>

    <div class='card'>
        <div class='card-body'>
            <form action='{{ route('sites.update', $item->id) }}' method='POST'>
                @csrf
                
        <div class='form-group'>
            <label for='name'>Name</label>
            <input type='text' class='form-control' id='name' name='name' value='{{ $item->name }}' required>
        </div>
    
        <div class='form-group'>
            <label for='name'>Phonenumber</label>
            <input type='number' class='form-control' id='phonenumber' name='phonenumber' value='{{ $item->phonenumber }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
