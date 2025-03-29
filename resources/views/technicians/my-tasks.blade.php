
@extends('layouts.main')

@section('content')
    <div class='row'>
        <div class='col-lg-6 col-md-6 col-sm-12'>
            <h1>All Tasks</h1>
        </div>
        <div class='col-lg-6 col-md-6 col-sm-12' style='text-align: right;'>
            {{-- <a href='{{ url('trash-tasks') }}'><button class='btn btn-danger'><i class='fas fa-trash'></i> Trash <span class='text-warning'>{{ App\Models\Tasks::where('isTrash', '1')->count() }}</span></button></a>
            <a href='{{ route('tasks.create') }}'><button class='btn btn-success'><i class='fas fa-plus'></i> Add Tasks</button></a> --}}
            <input type="text" class="search-my-tasks form-control" placeholder="Search...">
        </div>
    </div>
    
    {{-- <div class='card'>
        <div class='card-body'> --}}
            {{-- <div class='row'>
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
            </div> --}}

            <div class='table-responsive'>
                <style>
                    .task-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                        gap: 50px;
                        padding: 20px;
                    }
                
                    .task-card {
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                        padding: 15px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        border-left: 5px solid #007bff;
                        transition: 0.3s;
                    }
                
                    .task-card.completed {
                        border-left-color: #28a745; /* Green for completed */
                    }
                
                    .task-card.pending {
                        border-left-color: #dc3545; /* Red for pending */
                    }
                
                    .task-header {
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                
                    .task-info {
                        font-size: 14px;
                        color: #666;
                        margin-bottom: 8px;
                    }
                
                    .task-actions {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: auto;
                    }
                
                    .task-actions a {
                        text-decoration: none;
                        font-size: 18px;
                    }
                </style>
                
                <div class="task-grid">
                    @forelse(App\Models\Taskassignments::where('users_id', Auth::user()->id)->orderBy('id', 'desc')->get() as $item)
                        @php
                            $isEmpty = empty($item->tasks->name) || empty($item->tasks->status) ||
                                       empty($item->projects->name) || empty($item->workspaces->name);
                        @endphp
                
                        @if(!$isEmpty) 
                            <div class="task-card {{ $item->tasks->status === 'completed' ? 'completed' : 'pending' }}">
                                <div class="task-header">{{ $item->tasks->name ?? 'Untitled Task' }}</div>
                
                                <div class="task-info">
                                    <b>Status:</b> {{ ucfirst($item->tasks->status ?? 'N/A') }}
                                </div>
                
                                <div class="task-info">
                                    <b>Project:</b> {{ $item->projects->name ?? 'No Project' }}
                                </div>
                
                                <div class="task-info">
                                    <b>Workspace:</b> {{ $item->workspaces->name ?? 'No Workspace' }}
                                </div>
                
                                <div class="task-actions">
                                    {{-- <input type="checkbox" class="check" data-id="{{ $item->id }}"> --}}
                                    <div>
                                        <a href="{{ route('tasks.show', $item->tasks->id ?? '#') }}">
                                            <button class="btn btn-outline-secondary">
                                                <i class="fas fa-eye text-secondary"></i> View
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p>No tasks found.</p>
                    @endforelse
                </div>
                
            </div>
        {{-- </div>
    </div> --}}

    {{-- {{ $tasks->links('pagination::bootstrap-5') }} --}}

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
