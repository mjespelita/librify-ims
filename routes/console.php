<?php

use App\Models\Taskassignments;
use App\Models\Tasks;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('sched', function () {

    $scheduledTasks = Tasks::where('isScheduled', 1)->get();


    foreach ($scheduledTasks as $key => $scheduledTask) {

        $newScheduledTask = Tasks::create([
            'name' => $scheduledTask['name'],
            'status' => 'pending',
            'projects_id' => $scheduledTask['projects_id'],
            'projects_workspaces_id' => $scheduledTask['projects_workspaces_id'],
            'deadline' => $scheduledTask['deadline'],
            'priority' => $scheduledTask['priority'],
            'isScheduled' => 0,
        ]);

        $assigneesOfScheduledTasks = Taskassignments::where('tasks_id', $scheduledTask['id'])->get();

        foreach ($assigneesOfScheduledTasks as $key => $assigneesOfScheduledTask) {
            
            Taskassignments::create([
                'tasks_id' => $newScheduledTask->id,
                'tasks_projects_id' => $assigneesOfScheduledTask['tasks_projects_id'],
                'tasks_projects_workspaces_id' => $assigneesOfScheduledTask['tasks_projects_workspaces_id'],
                'users_id' => $assigneesOfScheduledTask['users_id'],
                'role' => $assigneesOfScheduledTask['role'],
                'isLeadAssignee' => $assigneesOfScheduledTask['isLeadAssignee']
            ]);

        }
    }

})->purpose('Scheduled Tasks')->dailyAt('08:00');
