<?php

namespace App\Http\Controllers\restapi\admin;

use App\Enums\CategoryStatus;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminCategoryApi extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/categories",
     *     tags={"Admin"},
     *     summary="Get list of categories",
     *     description="Get list of categories",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized user"
     *     ),
     * )
     */
    public function list(Request $request)
    {
        $categories = Categories::where('status', '!=', CategoryStatus::DELETED)
            ->orderBy('id', 'desc')
            ->get();
        $data = returnMessage(1, $categories, 'Success!');
        return response()->json($data, 200);
    }

    /**
     * Get detail of a category
     *
     * @OA\Get(
     *     path="/api/admin/categories/{id}",
     *     tags={"Admin"},
     *     summary="Get detail of a category",
     *     description="Get detail of a category",
     *     @OA\Parameter(
     *         description="Category ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized user"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Category not found"
     *     )
     * )
     */
    public function detail(Request $request, $id)
    {
        $category = Categories::find($id);
        if (!$category || $category->status == CategoryStatus::DELETED) {
            $data = returnMessage(-1, null, 'Category not found!');
            return response()->json($data, 404);
        }

        $data = returnMessage(1, $category, 'Success!');
        return response()->json($data, 200);
    }

    /**
     * Create a category
     *
     * @OA\Post(
     *     path="/admin/categories/create",
     *     tags={"Admin"},
     *     summary="Create category",
     *     description="Create category",
     *     @OA\Parameter(
     *         description="Name",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Parent id",
     *         in="query",
     *         name="parent_id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Status",
     *         in="query",
     *         name="status",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             enum={1,2,3}
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Thumbnail",
     *         in="query",
     *         name="thumbnail",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized user"
     *     ),
     * )
     */
    public function create(Request $request)
    {
        try {
            $category = new Categories();

            $name = $request->input('name');

            $category->name = $name;

            $parent_id = $request->input('parent_id');

            if ($parent_id && $parent_id != "") {
                $category->parent_id = $parent_id;
            } else {
                $category->parent_id = null;
            }

            if ($request->hasFile('thumbnail')) {
                $item = $request->file('thumbnail');
                $itemPath = $item->store('category', 'public');
                $thumbnail = asset('storage/' . $itemPath);
                $category->thumbnail = $thumbnail;
            } else {
                $data = returnMessage(-1, null, 'Error, Please upload thumbnail!');
                return response()->json($data, 400);
            }

            $category->status = $request->input('status');
            $category->created_by = Auth::user()->id;

            $success = $category->save();

            if ($success) {
                $data = returnMessage(1, $category, 'Success, Create category successful!');
                return response()->json($data, 200);
            }

            $data = returnMessage(-1, null, 'Error, Create error!');
            return response()->json($data, 400);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, null, $exception->getMessage());
            return response()->json($data, 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/admin/categories/{id}",
     *     tags={"Admin"},
     *     summary="Update category",
     *     description="Update category",
     *     @OA\Parameter(
     *         description="Category ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Category name"
     *             ),
     *             @OA\Property(
     *                 property="parent_id",
     *                 type="integer",
     *                 description="Parent category ID"
     *             ),
     *             @OA\Property(
     *                 property="thumbnail",
     *                 type="string",
     *                 format="binary",
     *                 description="Thumbnail"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 description="Category status"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized user"
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Categories::find($id);

            $name = $request->input('name');

            $category->name = $name;

            $parent_id = $request->input('parent_id');

            if ($parent_id && $parent_id !== "") {
                $category->parent_id = $parent_id;
            }

            if ($request->hasFile('thumbnail')) {
                $item = $request->file('thumbnail');
                $itemPath = $item->store('category', 'public');
                $thumbnail = asset('storage/' . $itemPath);
                $category->thumbnail = $thumbnail;
            }

            $category->status = $request->input('status');
            $category->updated_by = Auth::user()->id;
            $category->updated_at = Carbon::now()->addHours(7); /* GMT +7*/

            $success = $category->save();

            if ($success) {
                $data = returnMessage(2, null, 'Success, Update category successful!');
                return response()->json($data, 200);
            }

            $data = returnMessage(-1, null, 'Error, Update error!');
            return response()->json($data, 400);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, null, $exception->getMessage());
            return response()->json($data, 400);
        }
    }


    /**
     * Delete a category
     *
     * @OA\Delete(
     *     path="/admin/categories/{id}",
     *     tags={"Admin"},
     *     summary="Delete a category",
     *     description="Delete a category",
     *     @OA\Parameter(
     *         description="Category ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized user"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $category = Categories::find($id);

            $category->status = CategoryStatus::DELETED;
            $category->deleted_by = Auth::user()->id;
            $category->deleted_at = Carbon::now()->addHours(7); /* GMT +7*/

            $success = $category->save();

            if ($success) {
                $data = returnMessage(1, $category, 'Success, Delete category successful!');
                return response()->json($data, 200);
            }

            $data = returnMessage(-1, null, 'Delete, Create error!');
            return response()->json($data, 400);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, null, $exception->getMessage());
            return response()->json($data, 400);
        }
    }
}
