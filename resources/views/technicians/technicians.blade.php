
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Employees</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            <a href='{{ url('trash-technicians') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Technicians::where('isTrash', '1')->count() }}</span></button></a>
            <a href='{{ route('technicians.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Employee</button></a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">

    
            <div class='card'>
                <div class='card-body'>
                    <h5>Technicians</h5>
                    <div class='row'>
                        <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                            <div class='row'>
                                {{-- <div class='col-4'>
                                    <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                        Action
                                    </button>
                                    <div class='dropdown-menu'>
                                        <a class='dropdown-item bulk-move-to-trash' href='#'>
                                            <i class='fa fa-trash'></i> Move to Trash
                                        </a>
                                        <a class='dropdown-item bulk-delete' href='#'>
                                            <i class='fa fa-trash'></i> <span class='text-danger'>Delete Permanently</span> <br> <small>(this action cannot be undone)</small>
                                        </a>
                                    </div>
                                </div> --}}
                                <div class='col-8'>
                                    <form action='{{ url('/technicians-paginate') }}' method='get'>
                                        <div class='input-group'>
                                            <input type='number' name='paginate' class='form-control' placeholder='Paginate' value='{{ request()->get('paginate', 10) }}'>
                                            <div class='input-group-append'>
                                                <button class='btn btn-success' type='submit'><i class='fa fa-bars'></i></button>
                                            </div>
                                        </div>
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                            <form action='{{ url('/technicians-filter') }}' method='get'>
                                <div class='input-group'>
                                    <input type='date' class='form-control' id='from' name='from' required> 
                                    <b class='pt-2'>- to -</b>
                                    <input type='date' class='form-control' id='to' name='to' required>
                                    <div class='input-group-append'>
                                        <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i></button>
                                    </div>
                                </div>
                                @csrf
                            </form>
                        </div>
                        <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                            <!-- Search Form -->
                            <form action='{{ url('/technicians-search') }}' method='GET'>
                                <div class='input-group'>
                                    <input type='text' name='search' value='{{ request()->get('search') }}' class='form-control' placeholder='Search...'>
                                    <div class='input-group-append'>
                                        <button class='btn btn-success' type='submit'><i class='fa fa-search'></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
        
                    <div class='table-responsive'>
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    {{-- <th scope='col'>
                                    <input type='checkbox' name='' id='' class='checkAll'>
                                    </th> --}}
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                @forelse($technicians as $item)
                                    <tr>
                                        {{-- <th scope='row'>
                                            <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                        </th> --}}
                                        <td><img src="{{ $item->profile_photo_path ? url('storage/' . $item->profile_photo_path) : 'assets/profile_photo_placeholder.png' }}" height="50" width="50" style="border-radius: 50%;" alt="User Profile Photo"></td>
                                        <td>
                                            <a href="{{ url('show-technicians/'.$item->id) }}" class="fw-bold text-decoration-none text-primary">{{ $item->name }}</a>
                                        </td>
                                        <td>
                                            <a class="fw-bold text-decoration-none text-primary" href="mailto: {{ $item->email }}">{{ $item->email }}</a>
                                        </td>
                                        <td>
                                            {{-- <a href='{{ route('technicians.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a>
                                            <a href='{{ route('technicians.edit', $item->id) }}'><i class='fas fa-edit text-info'></i></a> --}}
                                            <a href='{{ route('technicians.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>No Record...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            {{ $technicians->links('pagination::bootstrap-5') }}
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            
    
    <div class='card'>
        <div class='card-body'>
            <h5>Other Employees</h5>
            <div class='row'>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <div class='row'>
                        {{-- <div class='col-4'>
                            <button type='button' class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                Action
                            </button>
                            <div class='dropdown-menu'>
                                <a class='dropdown-item bulk-move-to-trash' href='#'>
                                    <i class='fa fa-trash'></i> Move to Trash
                                </a>
                                <a class='dropdown-item bulk-delete' href='#'>
                                    <i class='fa fa-trash'></i> <span class='text-danger'>Delete Permanently</span> <br> <small>(this action cannot be undone)</small>
                                </a>
                            </div>
                        </div> --}}
                        <div class='col-8'>
                            <form action='{{ url('/technicians-paginate') }}' method='get'>
                                <div class='input-group'>
                                    <input type='number' name='paginate' class='form-control' placeholder='Paginate' value='{{ request()->get('paginate', 10) }}'>
                                    <div class='input-group-append'>
                                        <button class='btn btn-success' type='submit'><i class='fa fa-bars'></i></button>
                                    </div>
                                </div>
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <form action='{{ url('/technicians-filter') }}' method='get'>
                        <div class='input-group'>
                            <input type='date' class='form-control' id='from' name='from' required> 
                            <b class='pt-2'>- to -</b>
                            <input type='date' class='form-control' id='to' name='to' required>
                            <div class='input-group-append'>
                                <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i></button>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <!-- Search Form -->
                    <form action='{{ url('/technicians-search') }}' method='GET'>
                        <div class='input-group'>
                            <input type='text' name='search' value='{{ request()->get('search') }}' class='form-control' placeholder='Search...'>
                            <div class='input-group-append'>
                                <button class='btn btn-success' type='submit'><i class='fa fa-search'></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class='table-responsive'>
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            {{-- <th scope='col'>
                            <input type='checkbox' name='' id='' class='checkAll'>
                            </th> --}}
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(App\Models\User::where('role', 'employee')->orderBy('id', 'desc')->paginate(10) as $item)
                            <tr>
                                {{-- <th scope='row'>
                                    <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                </th> --}}
                                <td><img src="{{ $item->profile_photo_path ? url('storage/' . $item->profile_photo_path) : 'assets/profile_photo_placeholder.png' }}" height="50" width="50" style="border-radius: 50%;" alt="User Profile Photo"></td>
                                <td>
                                    <a href="{{ url('show-technicians/'.$item->id) }}" class="fw-bold text-decoration-none text-primary">{{ $item->name }}</a>
                                </td>
                                <td>
                                    <a class="fw-bold text-decoration-none text-primary" href="mailto: {{ $item->email }}">{{ $item->email }}</a>
                                </td>
                                <td>
                                    {{-- <a href='{{ route('technicians.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a>
                                    <a href='{{ route('technicians.edit', $item->id) }}'><i class='fas fa-edit text-info'></i></a> --}}
                                    <a href='{{ route('technicians.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>No Record...</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ App\Models\User::where('role', 'employee')->orderBy('id', 'desc')->paginate(10)->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script src='{{ url('assets/jquery/jquery.min.js') }}'></script>
    <script>
        $(document).ready(function () {

            // checkbox

            var click = false;
            $('.checkAll').on('click', function() {
                $('.check').prop('checked', !click);
                click = !click;
                this.innerHTML = click ? 'Deselect' : 'Select';
            });

            $('.bulk-delete').click(function () {
                let array = [];
                $('.check:checked').each(function() {
                    array.push($(this).attr('data-id'));
                });

                $.post('/technicians-delete-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })

            $('.bulk-move-to-trash').click(function () {
                let array = [];
                $('.check:checked').each(function() {
                    array.push($(this).attr('data-id'));
                });

                $.post('/technicians-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
