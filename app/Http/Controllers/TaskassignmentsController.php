<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Taskassignments};
use App\Http\Requests\StoreTaskassignmentsRequest;
use App\Http\Requests\UpdateTaskassignmentsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskassignmentsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('taskassignments.taskassignments', [
            'taskassignments' => Taskassignments::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('taskassignments.trash-taskassignments', [
            'taskassignments' => Taskassignments::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($taskassignmentsId)
    {
        /* Log ************************************************** */
        $oldName = Taskassignments::where('id', $taskassignmentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Taskassignments "'.$oldName.'".']);
        /******************************************************** */

        Taskassignments::where('id', $taskassignmentsId)->update(['isTrash' => '0']);

        return redirect('/taskassignments');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taskassignments.create-taskassignments');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskassignmentsRequest $request)
    {
        Taskassignments::create(['tasks_id' => $request->tasks_id,'tasks_projects_id' => $request->tasks_projects_id,'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id,'users_id' => $request->users_id,'role' => $request->role]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Taskassignments '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Taskassignments Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Taskassignments $taskassignments, $taskassignmentsId)
    {
        return view('taskassignments.show-taskassignments', [
            'item' => Taskassignments::where('id', $taskassignmentsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Taskassignments $taskassignments, $taskassignmentsId)
    {
        return view('taskassignments.edit-taskassignments', [
            'item' => Taskassignments::where('id', $taskassignmentsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskassignmentsRequest $request, Taskassignments $taskassignments, $taskassignmentsId)
    {
        /* Log ************************************************** */
        $oldName = Taskassignments::where('id', $taskassignmentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Taskassignments from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Taskassignments::where('id', $taskassignmentsId)->update(['tasks_id' => $request->tasks_id,'tasks_projects_id' => $request->tasks_projects_id,'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id,'users_id' => $request->users_id,'role' => $request->role]);

        return back()->with('success', 'Taskassignments Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Taskassignments $taskassignments, $taskassignmentsId)
    {
        return view('taskassignments.delete-taskassignments', [
            'item' => Taskassignments::where('id', $taskassignmentsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Taskassignments $taskassignments, $taskassignmentsId)
    {

        /* Log ************************************************** */
        $oldName = Taskassignments::where('id', $taskassignmentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Taskassignments "'.$oldName.'".']);
        /******************************************************** */

        Taskassignments::where('id', $taskassignmentsId)->delete();

        return back()->with('success', 'Assignee Removed Successfully!');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Taskassignments::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Taskassignments "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Taskassignments::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Taskassignments::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Taskassignments "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Taskassignments::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Taskassignments::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Taskassignments "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Taskassignments::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}