<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $projects = $user->projects()->with('owner')->get()
            ->merge($user->ownedProjects()->with('owner')->get())
            ->unique('id');

        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->owner_id = Auth::id();
        $project->save();

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json($project->load('owner', 'members', 'boards'));


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project->update($request->only(['name', 'description']));

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }

    public function addMember(Request $request, Project $project)
    {
        $this->authorize('addMember', $project);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member,admin'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($project->members->contains($user)){
            return response()->json(['message' => 'User is already a member of this project'], 422);
        }

        $project->members()->attach($user, ['role' => $request->role]);

        return response()->json(['messsage' => 'Member added successfully']);
    }

    public function removeMember(Request $request, Project $project)
    {
        $this->authorize('removeMember', $project);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if ($request->user_id == $project->owner_id) {
            return response()->json(['message' => 'Cannot remove the project owner'], 422);
        }

        $project->members()->detach($request->user_id);

        return response()->json(['message' => 'Member removed successfully']);
    }
}
