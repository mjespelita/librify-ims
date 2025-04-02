
@extends('layouts.main')

@section('content')
    <h1>Employee Details</h1>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
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
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5>Employee Task Summary for <span id="current-month"></span>: Pending & Completed</h5>

                    <script>
                        // Get current month name
                        const today = new Date();
                        const monthName = new Intl.DateTimeFormat('en-US', { month: 'long' }).format(today);

                        // Set the text inside the span
                        document.getElementById("current-month").textContent = monthName;
                    </script>
                    <div id="lineChartEmployeeDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <div class='card'>
        <div class='card-body'>
             <div class='row'>
                {{-- <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
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
                </div> --}}
                <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <h5>{{ $item->name }}'s Tasks</h5>
                    <b>Jump To Date.</b>
                    <form action='{{ url('/admin-my-tasks-filter') }}' method='get'>
                        <div class='input-group'>
                            <input type='date' class='form-control' id='from' name='from' required> 
                            <b class='pt-2'>- to -</b>
                            <input type='date' class='form-control' id='to' name='to' required>
                            <input type='number' class='form-control' id='to' name='user' required value="{{ $item->id }}" hidden>
                            <div class='input-group-append'>
                                <button type='submit' class='btn btn-primary form-control'><i class='fas fa-filter'></i></button>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
                {{-- <div class='col-lg-4 col-md-4 col-sm-12 mt-2'>
                    <!-- Search Form -->
                    <form action='{{ url('/tasks-search') }}' method='GET'>
                        <div class='input-group'>
                            <input type='text' name='search' value='{{ request()->get('search') }}' class='form-control' placeholder='Search...'>
                            <div class='input-group-append'>
                                <button class='btn btn-success' type='submit'><i class='fa fa-search'></i></button>
                            </div>
                        </div>
                    </form>
                </div> --}}
            </div>

            <br>

            <h5>Today</h5>
            <div class='table-responsive'>
                <div class="task-grid">
                    @forelse($taskAssignments as $taskAssignment)
                        @php
                            $isEmpty = empty($taskAssignment->tasks->name) || empty($taskAssignment->tasks->status) ||
                                       empty($taskAssignment->projects->name) || empty($taskAssignment->workspaces->name);
                        @endphp
                
                        @if(!$isEmpty) 
                            {{-- @if ($taskAssignment->tasks->status === 'pending') --}}
                                <div class="task-card {{ $taskAssignment->tasks->status === 'completed' ? 'completed' : 'pending' }}">
                                    <div class="task-header">{{ $taskAssignment->tasks->name ?? 'Untitled Task' }}</div>
                    
                                    <div class="task-info">
                                        <b>Status:</b> {{ ucfirst($taskAssignment->tasks->status ?? 'N/A') }}
                                    </div>
                    
                                    <div class="task-info">
                                        <b>Project:</b> {{ $taskAssignment->projects->name ?? 'No Project' }}
                                    </div>
                    
                                    <div class="task-info">
                                        <b>Workspace:</b> {{ $taskAssignment->workspaces->name ?? 'No Workspace' }}
                                    </div>

                                    <div class="task-info">
                                        <b>Created On:</b> {{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($taskAssignment->created_at) }}
                                    </div>
                    
                                    <div class="task-actions">
                                        {{-- <input type="checkbox" class="check" data-id="{{ $taskAssignment->id }}"> --}}
                                        <div>
                                            <a href="{{ route('tasks.show', $taskAssignment->tasks->id ?? '#') }}">
                                                <button class="btn btn-outline-secondary">
                                                    <i class="fas fa-eye text-secondary"></i> View
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {{-- @endif --}}
                        @endif
                    @empty
                        <p>No tasks found.</p>
                    @endforelse
                </div>
            </div>

            {{ $taskAssignments->links('pagination::bootstrap-5') }}

            <h5>Unfinished Tasks</h5>
            <div class='table-responsive'>
                <div class="task-grid">
                    @forelse($unfinished_taskAssignments as $unfinished_taskAssignment)
                        @php
                            $isEmpty = empty($unfinished_taskAssignment->tasks->name) || empty($unfinished_taskAssignment->tasks->status) ||
                                       empty($unfinished_taskAssignment->projects->name) || empty($unfinished_taskAssignment->workspaces->name);
                        @endphp
                
                        @if(!$isEmpty) 
                            <div class="task-card {{ $unfinished_taskAssignment->tasks->status === 'completed' ? 'completed' : 'pending' }}">
                                <div class="task-header">{{ $unfinished_taskAssignment->tasks->name ?? 'Untitled Task' }}</div>
                
                                <div class="task-info">
                                    <b>Status:</b> {{ ucfirst($unfinished_taskAssignment->tasks->status ?? 'N/A') }}
                                </div>
                
                                <div class="task-info">
                                    <b>Project:</b> {{ $unfinished_taskAssignment->projects->name ?? 'No Project' }}
                                </div>
                
                                <div class="task-info">
                                    <b>Workspace:</b> {{ $unfinished_taskAssignment->workspaces->name ?? 'No Workspace' }}
                                </div>

                                <div class="task-info">
                                    <b>Created On:</b> {{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($unfinished_taskAssignment->created_at) }}
                                </div>
                
                                <div class="task-actions">
                                    {{-- <input type="checkbox" class="check" data-id="{{ $unfinished_taskAssignment->id }}"> --}}
                                    <div>
                                        <a href="{{ route('tasks.show', $unfinished_taskAssignment->tasks->id ?? '#') }}">
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

            {{ $unfinished_taskAssignments->links('pagination::bootstrap-5') }}
        {{-- </div>
    </div> --}}


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

    <a href='{{ route('technicians.index') }}' class='btn btn-primary'>Back to List</a>
@endsection
