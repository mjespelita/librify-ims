
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Tasks</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            <a href='{{ url('trash-tasks') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Tasks::where('isTrash', '1')->count() }}</span></button></a>
            {{-- <a href='{{ route('tasks.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Tasks</button></a> --}}
        </div>
    </div>
    
    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <div class='row'>
                        <div class='col-4'>
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
                        </div>
                        <div class='col-8'>
                            <form action='{{ url('/tasks-paginate') }}' method='get'>
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
                    <form action='{{ url('/tasks-filter') }}' method='get'>
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
                    <form action='{{ url('/tasks-search') }}' method='GET'>
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
                            <th scope='col'>
                            <input type='checkbox' name='' id='' class='checkAll'>
                            </th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Projects</th>
                            <th>Workspace</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (Auth::user()->role === "admin")
                            @forelse($tasks as $item)
                                <tr>
                                    <th scope='row'>
                                        <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                    </th>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>
                                        <a class="fw-bold nav-link text-primary" href="{{ url('show-projects/'.($item->projects->id ?? "no data")) }}">{{ $item->projects->name ?? "no data" }}</a>
                                    </td>
                                    <td>
                                        <a class="fw-bold nav-link text-primary" href="{{ url('show-workspaces/'.($item->workspaces->id ?? "no data")) }}">{{ $item->workspaces->name ?? "no data" }}</a>
                                    </td>
                                    <td>
                                        <a href='{{ route('tasks.show', $item->id) }}'><i class='fas fa-eye text-success'></i></a>
                                        <a href='{{ route('tasks.edit', $item->id) }}'><i class='fas fa-edit text-info'></i></a>
                                        <a href='{{ route('tasks.delete', $item->id) }}'><i class='fas fa-trash text-danger'></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td>No Record...</td>
                                </tr>
                            @endforelse
                        @endif

                        @if (Auth::user()->role === "technician")
                            @forelse(App\Models\Taskassignments::where('users_id', Auth::user()->id)->get() as $item)
                                <tr>
                                    <th scope='row'>
                                        <input type='checkbox' name='' id='' class='check' data-id='{{ $item->id }}'>
                                    </th>
                                    <td>{{ $item->tasks->id ?? "no data" }}</td>
                                    <td>{{ $item->tasks->name ?? "no data"  }}</td>
                                    <td>{{ $item->tasks->status ?? "no data" }}</td>
                                    <td>
                                        <a class="fw-bold nav-link text-primary" href="{{ url('show-projects/'.($item->projects->id ?? "no data")) }}">{{ $item->projects->name ?? "no data" }}</a>
                                    </td>
                                    <td>
                                        <a class="fw-bold nav-link text-primary" href="{{ url('show-workspaces/'.($item->workspaces->id ?? "no data")) }}">{{ $item->workspaces->name ?? "no data" }}</a>
                                    </td>
                                    <td>
                                        <a href='{{ route('tasks.show', $item->tasks->id ?? "no data") }}'><i class='fas fa-eye text-success'></i></a>
                                        <a href='{{ route('tasks.edit', $item->tasks->id ?? "no data") }}'><i class='fas fa-edit text-info'></i></a>
                                        <a href='{{ route('tasks.delete', $item->tasks->id ?? "no data") }}'><i class='fas fa-trash text-danger'></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td>No Record...</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $tasks->links('pagination::bootstrap-5') }}

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

                $.post('/tasks-delete-all-bulk-data', {
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

                $.post('/tasks-move-to-trash-all-bulk-data', {
                    ids: array,
                    _token: $("meta[name='csrf-token']").attr('content')
                }, function (res) {
                    window.location.reload();
                })
            })
        });
    </script>
@endsection
