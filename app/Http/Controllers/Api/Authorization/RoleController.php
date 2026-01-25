<?php

namespace App\Http\Controllers\Api\Authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Exception;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return response()->json(['success' => true, 'message' => 'List roles', 'data' => $roles], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data roles', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name',
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            $role = Role::create(['name' => $request->name]);
            return response()->json(['success' => true, 'message' => 'Role dibuat', 'data' => $role], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat role', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'Detail role', 'data' => $role], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan', 'error' => $e->getMessage()], $status);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name,' . $role->id,
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            $role->name = $request->name;
            $role->save();

            return response()->json(['success' => true, 'message' => 'Role diperbarui', 'data' => $role], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui role', 'error' => $e->getMessage()], $status);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(['success' => true, 'message' => 'Role dihapus'], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json(['success' => false, 'message' => 'Gagal menghapus role', 'error' => $e->getMessage()], $status);
        }
    }
}
