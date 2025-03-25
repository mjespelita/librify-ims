
@extends('layouts.main')

@section('content')
    <h1>Technicians Details</h1>

    <div class='card'>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table'>
                    <tr>
                        <th>Profile Photo</th>
                        <td>
                            <img src="{{ $item->profile_photo_path ? url('storage/' . $item->profile_photo_path) : '/assets/profile_photo_placeholder.png' }}" height="200" style="border-radius: 50%; border: 3px #F69639 solid;" width="200" alt="User Profile Photo">
                        </td>
                    </tr>

                    <tr>
                        <th>Name</th>
                        <td>{{ $item->name }}</td>
                    </tr>
                
                    <tr>
                        <th>Email</th>
                        <td>{{ $item->email }}</td>
                    </tr>
    
                    <tr>
                        <th>Created At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <a href='{{ route('technicians.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
