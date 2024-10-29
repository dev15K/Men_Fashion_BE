<?php

namespace App\Http\Controllers\restapi\admin;

use App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Coupons;
use Illuminate\Http\Request;

class AdminCouponApi extends Api
{
    public function list()
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }


    public function search(Request $request)
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function detail($id)
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function delete($id)
    {
        try {
            $data = returnMessage(1, '', 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    private function save(Request $request, Coupons $coupon)
    {

    }
}
