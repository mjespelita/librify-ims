<?php

namespace App\Http\Controllers;

use App\Models\{Logs, Comments};
use App\Http\Requests\StoreCommentsRequest;
use App\Http\Requests\UpdateCommentsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('comments.comments', [
            'comments' => Comments::where('isTrash', '0')->paginate(10)
        ]);
    }

    public function trash()
    {
        return view('comments.trash-comments', [
            'comments' => Comments::where('isTrash', '1')->paginate(10)
        ]);
    }

    public function restore($commentsId)
    {
        /* Log ************************************************** */
        $oldName = Comments::where('id', $commentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Comments "'.$oldName.'".']);
        /******************************************************** */

        Comments::where('id', $commentsId)->update(['isTrash' => '0']);

        return redirect('/comments');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('comments.create-comments');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentsRequest $request)
    {
        Comments::create(['comment' => $request->comment,'tasks_id' => $request->tasks_id,'tasks_projects_id' => $request->tasks_projects_id,'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id,'users_id' => $request->users_id]);

        /* Log ************************************************** */
        // Logs::create(['log' => Auth::user()->name.' created a new Comments '.'"'.$request->name.'"']);
        /******************************************************** */

        return back()->with('success', 'Comments Added Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comments $comments, $commentsId)
    {
        return view('comments.show-comments', [
            'item' => Comments::where('id', $commentsId)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comments $comments, $commentsId)
    {
        return view('comments.edit-comments', [
            'item' => Comments::where('id', $commentsId)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentsRequest $request, Comments $comments, $commentsId)
    {
        /* Log ************************************************** */
        $oldName = Comments::where('id', $commentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' updated a Comments from "'.$oldName.'" to "'.$request->name.'".']);
        /******************************************************** */

        Comments::where('id', $commentsId)->update(['comment' => $request->comment,'tasks_id' => $request->tasks_id,'tasks_projects_id' => $request->tasks_projects_id,'tasks_projects_workspaces_id' => $request->tasks_projects_workspaces_id,'users_id' => $request->users_id]);

        return back()->with('success', 'Comments Updated Successfully!');
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Comments $comments, $commentsId)
    {
        return view('comments.delete-comments', [
            'item' => Comments::where('id', $commentsId)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comments $comments, $commentsId)
    {

        /* Log ************************************************** */
        $oldName = Comments::where('id', $commentsId)->value('name');
        // Logs::create(['log' => Auth::user()->name.' deleted a Comments "'.$oldName.'".']);
        /******************************************************** */

        Comments::where('id', $commentsId)->update(['isTrash' => '1']);

        return redirect('/comments');
    }

    public function bulkDelete(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Comments::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' deleted a Comments "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Comments::find($value);
            $deletable->delete();
        }
        return response()->json("Deleted");
    }

    public function bulkMoveToTrash(Request $request) {

        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Comments::where('id', $value)->value('name');
            // Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') deleted a Comments "'.$oldName.'".']);
            /******************************************************** */

            $deletable = Comments::find($value);
            $deletable->update(['isTrash' => '1']);
        }
        return response()->json("Deleted");
    }

    public function bulkRestore(Request $request)
    {
        foreach ($request->ids as $value) {

            /* Log ************************************************** */
            $oldName = Comments::where('id', $value)->value('name');
            Logs::create(['log' => Auth::user()->name.' ('.Auth::user()->role.') restored a Comments "'.$oldName.'".']);
            /******************************************************** */

            $restorable = Comments::find($value);
            $restorable->update(['isTrash' => '0']);
        }
        return response()->json("Restored");
    }
}