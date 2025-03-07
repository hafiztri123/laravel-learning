<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Board $board)
    {
        $this->authorization('view', $board);

        return response()->json($board->columns);



    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Board $board)
    {
        $this->authorization('update', $board);

        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer'
        ]);

        $column = new Column();
        $column->name = $request->name;
        $column->order = $request->order;
        $column->board_id = $board->id;

        $column->save();
        return response()->json($column, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Column $column)
    {
        $this->authorization('view', $column);
        return response()->json($column);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Column $column)
    {
        $this->authorization('update', $column);

        $request->validate([
            'name' => 'sometimes|required|string|max:255'
        ]);

        $column->update($request->only(['name']));

        return response()->json($column);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Column $column)
    {
        $this->authorization('delete', $column);

        $column->delete();

        return response()->json(null, 204);

    }

    public function reorder(Request $request)
    {
        $request->validate([
            'columns' => 'required|array',
            'columns.*.id' => 'required|exists:column,id',
            'columns.*.order' => 'required|integer|min:0'
        ]);

        $columnsData = $request->columns;

        DB::beginTransaction();

        try {
            foreach($columnsData as $columnData) {
                $column = Column::findOrFail($columnData['id']);
                $this->authorize('update', $column);
                $column->update(['order' => $columnData['order']]);

            }

            DB::commit();

            return response()->json(['message' => 'Columns reordered successfully']);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error reordering columns'], 500);
        }

    }
}
