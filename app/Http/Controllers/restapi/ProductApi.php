<?php

namespace App\Http\Controllers\restapi;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductApi extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get list of products",
     *     description="Get list of products",
     *     tags={"Product"},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user"
     *     )
     * )
     */
    public function list()
    {
        $products = Products::where('status', ProductStatus::ACTIVE)
            ->orderByDesc('id')
            ->get();

        $data = returnMessage(1, $products, 'Success!');
        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get detail of a product",
     *     description="Get detail of a product",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         description="Product ID",
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
     *         response=401,
     *         description="Unauthorized user"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function detail($id)
    {
        $product = Products::find($id);
        if (!$product || $product->status != ProductStatus::ACTIVE) {
            $data = returnMessage(-1, null, 'Product not found!');
            return response()->json($data, 404);
        }

        $products = Products::where('status', ProductStatus::ACTIVE)
            ->where('id', '!=', $id)
            ->orderByDesc('id')
            ->get();

        $data = [
            'product' => $product,
            'other_products' => $products
        ];

        $res = returnMessage(1, $data, 'Success!');
        return response()->json($res, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/search",
     *     summary="Search products",
     *     description="Search products",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         description="Category ID",
     *         in="query",
     *         name="category_id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Minimum price",
     *         in="query",
     *         name="min_price",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Maximum price",
     *         in="query",
     *         name="max_price",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Keyword",
     *         in="query",
     *         name="keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized user"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $category_id = $request->input('category_id') ?? '';
        $min_price = $request->input('min_price') ?? '';
        $max_price = $request->input('max_price') ?? '';
        $keyword = $request->input('keyword') ?? '';

        $products = Products::where('status', ProductStatus::ACTIVE)
            ->when($category_id, function ($query) use ($category_id) {
                if (!empty($category_id)) {
                    $query->where('category_id', $category_id);
                }
            })
            ->when($min_price, function ($query) use ($min_price) {
                if (!empty($min_price)) {
                    $query->where('price', '>=', $min_price);
                }
            })
            ->when($max_price, function ($query) use ($max_price) {
                if (!empty($max_price)) {
                    $query->where('price', '<=', $max_price);
                }
            })
            ->when($keyword, function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
            })
            ->when($keyword, function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->where('description', 'like', '%' . $keyword . '%');
                }
            })
            ->orderByDesc('id')
            ->get();

        $data = returnMessage(1, $products, 'Success!');
        return response()->json($data, 200);
    }
}
