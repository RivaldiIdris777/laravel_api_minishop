<?php

namespace App\Http\Controllers\Api\Authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Exception;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json([
                'success' => true,
                'message' => 'List permissions',
                'data' => $permissions
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permission = Permission::create(['name' => $request->name]);
            return response()->json([
                'success' => true,
                'message' => 'Permission dibuat',
                'data' => $permission
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail permission',
                'data' => $permission
            ], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Permission tidak ditemukan',
                'error' => $e->getMessage()
            ], $status);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $permission = Permission::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name,' . $permission->id,
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permission->name = $request->name;
            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission diperbarui',
                'data' => $permission
            ], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permission',
                'error' => $e->getMessage()
            ], $status);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();
            return response()->json([
                'success' => true,
                'message' => 'Permission dihapus'
            ], 200);
        } catch (Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permission',
                'error' => $e->getMessage()
            ], $status);
        }
    }
}
