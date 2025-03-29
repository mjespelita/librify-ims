
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
            <select name="status" class="form-control" id="">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class='form-group my-3 my-3'>
            <label for='name'>Select Priority</label> <br> <br>
            <input type='radio' id='priority' name='priority' value="low" checked> Low
            <input type='radio' id='priority' name='priority' value="high"> High
        </div>

        {{-- calendar --}}
                        
        <div class="calendar">
            <div class="calendar-header">
                <button type="button" class="btn btn-secondary" onclick="prevMonth()">&#10094;</button>
                <h2 id="month-year"></h2>
                <button type="button" class="btn btn-secondary" onclick="nextMonth()">&#10095;</button>
            </div>
            <div class="weekdays">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>
            <div class="days" id="calendar-days"></div>
        </div>
        
        <div class="clock-container">
            <label>Select Time:</label>
            <div class="time-selector">
                <select id="hourPicker"></select> :
                <select id="minutePicker"></select>
                <select id="ampmPicker">
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>
        </div>
        
        <div class='form-group'>
            <input type='datetime-local' class='form-control deadline' hidden id='deadline' name='deadline' required>
        </div>
        
        <script>
            let currentDate = new Date();
            let selectedDate = null;
            let today = new Date().toISOString().split("T")[0];
        
            function renderCalendar() {
                const monthYear = document.getElementById("month-year");
                const daysContainer = document.getElementById("calendar-days");
                daysContainer.innerHTML = "";
                
                let year = currentDate.getFullYear();
                let month = currentDate.getMonth();
                
                monthYear.textContent = new Intl.DateTimeFormat("en-US", { month: "long", year: "numeric" }).format(currentDate);
                
                let firstDay = new Date(year, month, 1).getDay();
                let lastDate = new Date(year, month + 1, 0).getDate();
                
                for (let i = 0; i < firstDay; i++) {
                    let emptyDiv = document.createElement("div");
                    daysContainer.appendChild(emptyDiv);
                }
                
                for (let day = 1; day <= lastDate; day++) {
                    let dayDiv = document.createElement("div");
                    dayDiv.textContent = day;
                    dayDiv.classList.add("day");
                    let dateStr = new Date(year, month, day).toISOString().split("T")[0];
                    dayDiv.dataset.date = dateStr;
                    
                    if (dateStr === today) {
                        dayDiv.classList.add("today");
                    }
                    
                    dayDiv.addEventListener("click", function() {
                        if (selectedDate) {
                            selectedDate.classList.remove("selected");
                        }
                        selectedDate = this;
                        this.classList.add("selected");
                        updateDeadlineInput();
                    });
                    
                    daysContainer.appendChild(dayDiv);
                }
            }
        
            function prevMonth() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            }
        
            function nextMonth() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            }
        
            function populateTimeSelectors() {
                const hourPicker = document.getElementById("hourPicker");
                const minutePicker = document.getElementById("minutePicker");
                
                for (let i = 1; i <= 12; i++) {
                    let option = document.createElement("option");
                    option.value = i.toString().padStart(2, '0');
                    option.textContent = i.toString();
                    hourPicker.appendChild(option);
                }
                
                for (let i = 0; i < 60; i += 5) {
                    let option = document.createElement("option");
                    option.value = i.toString().padStart(2, '0');
                    option.textContent = i.toString().padStart(2, '0');
                    minutePicker.appendChild(option);
                }
            }
        
            function updateDeadlineInput() {
                if (selectedDate) {
                    let selectedHour = document.getElementById("hourPicker").value;
                    let selectedMinute = document.getElementById("minutePicker").value;
                    let ampm = document.getElementById("ampmPicker").value;
                    
                    let hour = parseInt(selectedHour, 10);
                    if (ampm === "PM" && hour < 12) hour += 12;
                    if (ampm === "AM" && hour === 12) hour = 0;
                    
                    document.getElementById("deadline").value = `${selectedDate.dataset.date}T${hour.toString().padStart(2, '0')}:${selectedMinute}`;
                }
            }
        
            document.getElementById("hourPicker").addEventListener("change", updateDeadlineInput);
            document.getElementById("minutePicker").addEventListener("change", updateDeadlineInput);
            document.getElementById("ampmPicker").addEventListener("change", updateDeadlineInput);
            
            populateTimeSelectors();
            renderCalendar();
        </script>

        {{-- end calendar --}}
    
        <div class='form-group'>
            {{-- <label for='name'>Projects_id</label> --}}
            <input type='text' class='form-control' hidden id='projects_id' name='projects_id' value='{{ $item->projects_id }}' required>
        </div>
    
        <div class='form-group'>
            {{-- <label for='name'>Projects_workspaces_id</label> --}}
            <input type='text' class='form-control' hidden id='projects_workspaces_id' name='projects_workspaces_id' value='{{ $item->projects_workspaces_id }}' required>
        </div>
    
                <button type='submit' class='btn btn-primary mt-3'>Update</button>
            </form>
        </div>
    </div>

@endsection
