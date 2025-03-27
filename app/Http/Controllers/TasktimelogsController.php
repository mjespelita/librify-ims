<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Tasktimelogs};
use App\Http\Requests\StoreTasktimelogsRequest;
use App\Http\Requests\UpdateTasktimelogsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasktimelogsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tasktimelogs.tasktimelogs', [
            'tasktimelogs' => Tasktimelogs::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('tasktimelogs.trash-tasktimelogs', [
            'tasktimelogs' => Tasktimelogs::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($tasktimelogsId)
    {
        /* Log ************************************************** */
        $oldName = Tasktimelogs::where('id', $tasktimelogsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Tasktimelogs "'.$oldName.'".']);
        /******************************************************** */

        Tasktimelogs::where('id', $tasktimelogsId)->update(['isTrash' => '0']);

        return redirect('/tasktimelogs');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasktimelogs.create-tasktimelogs');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreTasktimelogsRequest $request)
    // {
    //     Tasktimelogs::create(['start_time' => $request->start_time,'pause_time' => $request->pause_time,'stop_time' => $request->stop_time,'total_time' => $request->total_time,'users_id' => $request->users_id,'tasks_id' => $request->tasks_id,'tasks_projects_id' => $request->tasks_projects_id,'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id]);

    //     /* Log ************************************************** */
    //     // Logs::create(['log' => Auth::user()->name.' created a new Tasktimelogs '.'"'.$request->name.'"']);
    //     /******************************************************** */

    //     return back()->with('success', 'Tasktimelogs Added Successfully!');
    // }

    public function store(StoreTasktimelogsRequest $request)
    {
        $taskTimeLog = Tasktimelogs::create([
            'start_time' => now(), // Set the start time to current timestamp
            'pause_time' => null,  // Reset pause time
            'stop_time' => null,   // Reset stop time
            'elapsed_time' => 0,   // Start with 0 elapsed time
            'users_id' => $request->users_id,
            'tasks_id' => $request->tasks_id,
            'tasks_projects_id' => $request->tasks_projects_id,
            'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id,
            'isTrash' => 0, // Default to not trashed
            'status' => 'running' // Set status to running
        ]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' started a new Tasktimelog for Task ID: '.$request->tasks_id]);
        /******************************************************** */

        return back()->with('success', 'Task Timer Started Successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Tasktimelogs $tasktimelogs, $tasktimelogsId)
    {
        return view('tasktimelogs.show-tasktimelogs', [
            'item' => Tasktimelogs::where('id', $tasktimelogsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tasktimelogs $tasktimelogs, $tasktimelogsId)
    {
        return view('tasktimelogs.edit-tasktimelogs', [
            'item' => Tasktimelogs::where('id', $tasktimelogsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $taskTimeLog = Tasktimelogs::where('tasks_id', $id)
        ->whereNull('stop_time')
        ->whereNull('pause_time')
        ->first();

        if (!$taskTimeLog) {
            return back()->with('error', 'No active timer to pause.');
        }   

        $latestElapseTime = $taskTimeLog->elapsed_time;

        $startTime = Carbon::parse($taskTimeLog->start_time); // Assuming this is a Carbon instance
        $now = Carbon::now();

        $diffInSeconds = $startTime->diffInSeconds($now);

        $minus = $diffInSeconds - $latestElapseTime;

        $final = $latestElapseTime + $minus;

        $taskTimeLog->update([
            'pause_time'   => now(),
            'elapsed_time' => $final, // Save total elapsed time
            'status'       => 'paused', // Update status
        ]);

        return back()->with('success', 'Timer Paused!');
    }

    public function resume($id)
    {
        $taskTimeLog = Tasktimelogs::where('tasks_id', $id)
        ->whereNotNull('pause_time')
        ->whereNull('stop_time')
        ->first();

        if (!$taskTimeLog) {
            return back()->with('error', 'No paused timer to resume.');
        }

        $taskTimeLog->update([
            'pause_time' => null,   // Clear pause time
            'status'     => 'running', // Update status
        ]);

        return back()->with('success', 'Timer Resumed!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Tasktimelogs $tasktimelogs, $tasktimelogsId)
    {
        return view('tasktimelogs.delete-tasktimelogs', [
            'item' => Tasktimelogs::where('id', $tasktimelogsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tasktimelogs $tasktimelogs, $tasktimelogsId)
    {

        /* Log ************************************************** */
        $oldName = Tasktimelogs::where('id', $tasktimelogsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Tasktimelogs "'.$oldName.'".']);
        /******************************************************** */

        Tasktimelogs::where('id', $tasktimelogsId)->update(['isTrash' => '1']);

        return redirect('/tasktimelogs');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tasktimelogs::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Tasktimelogs "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Tasktimelogs::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tasktimelogs::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Tasktimelogs "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Tasktimelogs::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Tasktimelogs::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Tasktimelogs "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Tasktimelogs::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}