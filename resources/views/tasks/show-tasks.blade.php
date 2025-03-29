
@extends('layouts.main')

@section('content')
    <h1>Tasks Details</h1>

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if ($item->status === 'pending')
                            <b class="text-info">
                                <i class="fas fa-hourglass-half"></i> Task Pending
                            </b>
                        @endif

                        @if ($item->status === 'completed')
                            <b class="text-success">
                                <i class="fas fa-check-circle"></i> Task Completed
                            </b>
                        @endif
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h1 class="timer" style="width: 100%; font-family: 'Courier New', monospace; font-size: 2.5rem; color: #ff3b30; background: #ffffff; padding: 10px; border-radius: 10px; display: inline-block; text-shadow: 2px 2px 5px rgba(255, 59, 48, 0.5);">
                                00:00:00
                            </h1>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            {{-- timer con --}}
                            
                            @if (App\Models\Tasktimelogs::where('tasks_id', $item->id)->count() === 0)
                                <form action="{{ route('tasktimelogs.store') }}" method="POST">
                                    @csrf
                                    
                                    {{-- Store start time automatically --}}
                                    <input hidden type="text" name="start_time" value="{{ now()->toDateTimeString() }}">
                            
                                    {{-- Store authenticated user ID --}}
                                    <input hidden type="text" name="users_id" value="{{ Auth::id() }}">
                            
                                    {{-- Store task-related IDs --}}
                                    <input hidden type="text" name="tasks_id" value="{{ $item->id }}">
                                    <input hidden type="text" name="tasks_projects_id" value="{{ $item->projects->id ?? '' }}">
                                    <input hidden type="text" name="tasks_projects_workspaces_id" value="{{ $item->workspaces->id ?? '' }}">
                            
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Start Timer
                                    </button>
                                </form>
                            @endif
        
                            @if (App\Models\Tasktimelogs::where('tasks_id', $item->id)->whereNull('stop_time')->whereNull('pause_time')->exists())
                                <form action="{{ route('tasktimelogs.pause', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pause_time" value="{{ now() }}">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-pause"></i> Pause Timer</button>
                                </form>

                                <form action="{{ route('tasktimelogs.stop', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pause_time" value="{{ now() }}">
                                    <button type="submit" class="btn btn-success mt-1"><i class="fas fa-check"></i> Mark As Complete</button>
                                </form>

                            @endif
        
                            @if (App\Models\Tasktimelogs::where('tasks_id', $item->id)->whereNotNull('pause_time')->whereNull('stop_time')->exists())
                                <form action="{{ route('tasktimelogs.resume', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="resume_time" value="{{ now() }}">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-play"></i> Resume Timer</button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class='card'>
                <div class='card-body'>
                    <div class='table-responsive'>
                        <table class='table'>
                            
                            <tr>
                                <th>Name</th>
                                <td>{{ $item->name }}</td>
                            </tr>

                            <tr>
                                <th>Priority</th>
                                <td>
                                    <b class="{{ $item->priority === 'high' ? 'text-danger' : 'text-success' }}">
                                        {{ ucfirst($item->priority) }}
                                    </b>
                                </td>
                            </tr>

                            <tr>
                                <th>Deadline</th>
                                <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->deadline) }} - {{ Carbon\Carbon::parse($item->deadline)->diffForHumans() }}</td>
                            </tr>
                        
                            <tr>
                                <th>Status</th>
                                <td>{{ $item->status }}</td>
                            </tr>
                        
                            <tr>
                                <th>Project</th>
                                <td>
                                    <a class="fw-bold nav-link text-primary" href="{{ url('/show-projects/'.($item->projects->id ?? "")) }}">{{ ($item->projects->name ?? "no data") }}</a>
                                </td>
                            </tr>
                        
                            <tr>
                                <th>Workspace</th>
                                <td>
                                    <a class="fw-bold nav-link text-primary" href="{{ url('/show-workspaces/'.($item->workspaces->id ?? "")) }}">{{ ($item->workspaces->name ?? "no data") }}</a>
                                </td>
                            </tr>

                            
                        
                            <tr>
                                <th>Lead Assignee</th>
                                <td>
                                    @forelse (App\Models\Taskassignments::where('tasks_id', $item->id)->where('isLeadAssignee', 1)->get() as $assignedUser)
                                        <a href="{{ route('taskassignments.destroy', $assignedUser->id) }}"><img class="mb-2" src="{{ $assignedUser->users->profile_photo_path ? url('/storage/' . $assignedUser->users->profile_photo_path) : '/assets/profile_photo_placeholder.png' }}" height="40" width="40" style="border-radius: 50%;" alt="User Profile Photo"></a>
                                    @empty
                                        <b>No Lead Assignee</b>
                                    @endforelse
                                </td>
                            </tr>

                            <tr>
                                <th>Supporting Members</th>
                                <td>
                                    @forelse (App\Models\Taskassignments::where('tasks_id', $item->id)->where('isLeadAssignee', 0)->get() as $assignedUser)
                                        <a href="{{ route('taskassignments.destroy', $assignedUser->id) }}"><img class="mb-2" src="{{ $assignedUser->users->profile_photo_path ? url('/storage/' . $assignedUser->users->profile_photo_path) : '/assets/profile_photo_placeholder.png' }}" height="40" width="40" style="border-radius: 50%;" alt="User Profile Photo"></a>
                                    @empty
                                        <b>No Supporting Members</b>
                                    @endforelse

                                    
                                </td>
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

            @if (Auth::user()->role === 'admin')
                <div class="card">
                    <div class="card-body">

                        <h5>Add Assignee</h5>

                        <form class="mt-4" action='{{ route('taskassignments.store') }}' method='POST'>
                            @csrf
                            
                            <div class='form-group'>
                                <label for='name'>Select Assignee</label>
                                <select class="form-control" name="users_id" required>
                                    @php
                                        // Get users already assigned to the task
                                        $addedUsers = App\Models\TaskAssignments::where('tasks_id', $item->id)
                                            ->pluck('users_id')
                                            ->toArray();

                                        // Get users who are participants in the workspace
                                        $workspaceUsers = App\Models\WorkspaceUsers::where('workspaces_id', $item->projects_workspaces_id)
                                            ->pluck('users_id')
                                            ->toArray();
                                    @endphp

                                    @forelse (App\Models\User::whereNot('role', 'admin')
                                        ->whereNotIn('id', $addedUsers) // Exclude already assigned users
                                        ->whereIn('id', $workspaceUsers) // Only include workspace participants
                                        ->orderBy('id', 'desc')
                                        ->get() as $user)
                                        
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @empty
                                        <option value="">No users available...</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class='form-group'>
                                {{-- <label for='name'>Tasks_id</label> --}}
                                <input type='text' class='form-control' hidden id='tasks_id' value="{{ $item->id }}" name='tasks_id' required>
                            </div>
                        
                            <div class='form-group'>
                                {{-- <label for='name'>Tasks_projects_id</label> --}}
                                <input type='text' class='form-control' hidden id='tasks_projects_id' value="{{ $item->projects_id }}" name='tasks_projects_id' required>
                            </div>
                        
                            <div class='form-group'>
                                {{-- <label for='name'>Tasks_projects_workspaces_id</label> --}}
                                <input type='text' class='form-control' hidden id='tasks_projects_workspaces_id' value="{{ $item->projects_workspaces_id }}" name='tasks_projects_workspaces_id' required>
                            </div>
                        
                            <div class='form-group mt-4'>
                                <label for='name'>Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="" selected disabled>Select a role</option>
                                    <option value="Network Engineer">Network Engineer</option>
                                    <option value="System Administrator">System Administrator</option>
                                    <option value="Technical Support Specialist">Technical Support Specialist</option>
                                    <option value="Field Technician">Field Technician</option>
                                    <option value="Web Developer">Web Developer</option>
                                    <option value="Programmer">Programmer</option>
                                    <option value="Database Administrator">Database Administrator</option>
                                    <option value="Cybersecurity Analyst">Cybersecurity Analyst</option>
                                    <option value="Customer Support Representative">Customer Support Representative</option>
                                    <option value="Sales Representative">Sales Representative</option>
                                    <option value="Marketing Specialist">Marketing Specialist</option>
                                    <option value="Project Manager">Project Manager</option>
                                    <option value="Billing Specialist">Billing Specialist</option>
                                </select>
                            </div>

                            <div class='form-group my-3'>
                                {{-- <label for='name'>Tasks_projects_workspaces_id</label> --}}
                                <input type='checkbox' id='isLeadAssignee' name='isLeadAssignee'> Set as Lead Assignee
                            </div>
                
                            <button type='submit' class='btn btn-primary mt-3'>Add Participant</button>
                        </form>
                    </div>
                </div>
            @endif
            
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class='card'>
                <div class='card-body'>
                    <h5>Comments</h5>
                    
                    <div class='chat-container'>

                        @forelse(App\Models\Comments::where('tasks_id', $item->id)->with('files')->get() as $comment)
                            @php $isMine = auth()->id() == $comment->users_id; @endphp
                            <img class="mb-2 {{ $isMine ? 'mine' : 'other' }}" 
                                src="{{ $comment->users->profile_photo_path ? url('/storage/' . $comment->users->profile_photo_path) : '/assets/profile_photo_placeholder.png' }}" 
                                height="40" width="40" style="border-radius: 50%;" alt="User Profile Photo">
                            
                            <small class="{{ $isMine ? 'mine-name' : 'other-name' }}">
                                {{ $comment->users->name ?? 'no data' }} - {{ $comment->created_at->diffForHumans() }}
                            </small>

                            <div class='chat-bubble {{ $isMine ? 'mine' : 'other' }}'>
                                <p>{{ Smark\Smark\HTML::renderHTML(Smark\Smark\HTML::withUrl($comment->comment)) }}</p>
                            </div>

                            {{-- Check if comment has images and display them --}}
                            @if($comment->hasImage)
                                <div class="comment-images {{ $isMine ? 'mine-name' : 'other-name' }}">
                                    @foreach(App\Models\CommentFiles::where('comments_id', $comment->id)->get() as $file)
                                    <a href="{{ asset('storage/files/' . $file->file) }}">
                                        <img src="{{ asset('storage/files/' . $file->file) }}" alt="Comment Image" class="comment-image mb-2" style="border-radius: 20px; width: 200px;">
                                    </a>
                                    @endforeach
                                </div>
                            @endif
                        @empty
                            <p class='no-messages'>No comments yet...</p>
                        @endforelse

                        <div id="latest-comment"></div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var latestComment = document.getElementById("latest-comment");
                            var parentDiv = latestComment?.closest(".chat-container");
                    
                            if (latestComment && parentDiv) {
                                parentDiv.scrollTo({ 
                                    top: latestComment.offsetTop - parentDiv.offsetTop, 
                                    behavior: "smooth" 
                                });
                            }
                        });
                    </script>
                </div>
            </div>

            <div class='card'>
                <div class='card-body'>
                    <!-- Hidden file input -->
                    {{-- <input type="file" id="fileInput" multiple accept="image/*"> --}}

                    <!-- Image Preview -->

                    

                    <form action='{{ route('comments.store') }}' method='POST' enctype="multipart/form-data">
                        @csrf

                        
                    <div id="image-preview"></div>
                        
                        <div class='form-group'>
                            <label for='name'>Add Comment</label>
                            <textarea name="comment" id="" cols="20" rows="2" class="form-control" required></textarea>
                        </div>

                        <input type="file" id="fileInput" name="files[]" hidden multiple accept="image/*">
                    
                        <div class='form-group'>
                            {{-- <label for='name'>Tasks_id</label> --}}
                            <input type='text' class='form-control' id='tasks_id' name='tasks_id' hidden required value="{{ $item->id }}">
                        </div>
                    
                        <div class='form-group'>
                            {{-- <label for='name'>Tasks_projects_id</label> --}}
                            <input type='text' class='form-control' id='tasks_projects_id' name='tasks_projects_id' hidden required value="{{ $item->projects->id ?? 'no data' }}">
                        </div>
                    
                        <div class='form-group'>
                            {{-- <label for='name'>Tasks_projects_workspaces_id</label> --}}
                            <input type='text' class='form-control' id='tasks_projects_workspaces_id' name='tasks_projects_workspaces_id' hidden required value="{{ $item->workspaces->id ?? 'no data' }}">
                        </div>
                    
                        <div class='form-group'>
                            {{-- <label for='name'>Users_id</label> --}}
                            <input type='text' class='form-control' id='users_id' name='users_id' hidden required value="{{ Auth::user()->id }}">
                        </div>

                        <!-- Custom upload icon -->
                        <div class="upload-icon" onclick="document.getElementById('fileInput').click();">
                            <i class="fas fa-upload"></i>
                        </div>
            
                        <button type='submit' class='btn btn-primary mt-3'>Send Comment</button>
                    </form>

                    <script>
                        document.getElementById("fileInput").addEventListener("change", function (event) {
                            let files = event.target.files;
                            let previewContainer = document.getElementById("image-preview");
                            previewContainer.innerHTML = ""; // Clear previous previews

                            Array.from(files).forEach((file, index) => {
                                let reader = new FileReader();
                                reader.onload = function (e) {
                                    let div = document.createElement("div");
                                    div.classList.add("preview-container");

                                    let img = document.createElement("img");
                                    img.src = e.target.result;

                                    let removeBtn = document.createElement("button");
                                    removeBtn.innerHTML = "×";
                                    removeBtn.classList.add("remove-btn");
                                    removeBtn.onclick = function () {
                                        div.remove();
                                    };

                                    div.appendChild(img);
                                    div.appendChild(removeBtn);
                                    previewContainer.appendChild(div);
                                };
                                reader.readAsDataURL(file);
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    {{-- <a href='{{ route('tasks.index') }}' class='btn btn-primary'>Back to List</a> --}}

    <script src='{{ url('assets/jquery/jquery.min.js') }}'></script>
    <script>
        $(document).ready(function () {
            const urlSegments = window.location.pathname.split('/');
            const taskId = urlSegments[urlSegments.length - 1];

            console.log("Task ID:", taskId); // Debugging

            function formatTime(seconds) {
                const hrs = Math.floor(seconds / 3600);
                const mins = Math.floor((seconds % 3600) / 60);
                const secs = Math.floor(seconds % 60);
                return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }

            function updateTimer(initialElapsedTime, lastUpdatedAt) {
                const lastUpdatedTime = new Date(lastUpdatedAt).getTime(); // Convert to timestamp
                const currentTime = new Date().getTime(); // Current timestamp

                let additionalElapsed = Math.floor((currentTime - lastUpdatedTime) / 1000); // Time since last update
                let elapsedSeconds = initialElapsedTime + additionalElapsed; // Adjusted elapsed time

                function tick() {
                    elapsedSeconds++; // Increment every second
                    $(".timer").text(formatTime(elapsedSeconds));
                }

                tick(); // Run immediately to avoid 1s delay
                setInterval(tick, 1000);
            }

            $.get('/get-tasktimelogs/' + taskId, function (res) {
                if (res.taskTimeLog.status === "running") {
                    console.log(res.taskTimeLog);
                    updateTimer(res.taskTimeLog.elapsed_time, res.taskTimeLog.updated_at);
                } else if (res.taskTimeLog.status === "paused") {
                    $(".timer").text(formatTime(res.taskTimeLog.elapsed_time)); // Show static elapsed time
                } else if (res.taskTimeLog.status === "stopped") {
                    $(".timer").text(formatTime(res.taskTimeLog.elapsed_time)); // Show static elapsed time
                }
            });
        });

    </script>
@endsection
