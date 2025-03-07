<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Task $task)
    {
        $this->authorize('view', $task);

        $attachment = $task->attachments()->with('user')->get();

        return response()->json($attachment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('attachments', $filename, 'private');

        $attachment = new Attachment([
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'size' => $size,
            'user_id' => auth()->id()
        ]);

        $task->attachments()->save($attachment);

        return response()->json($attachment->load('user'), 201);



    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment->task);

        Storage::disk('private')->delete('attachments/' . $attachment->filename);

        $attachment->delete();

        return response()->json(null, 204);
    }

    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment->task);

        $path = 'attachments/' . $attachment->filename;

        if (!Storage::disk('private')->exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('private')->download(
            $path,
            $attachment->original_filename,
            ['Content-Type' => $attachment->mime_type]
        );
    }
}
