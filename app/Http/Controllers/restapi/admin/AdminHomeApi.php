<?php

namespace App\Http\Controllers\restapi\admin;

use App\Enums\OrderStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Revenues;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminHomeApi extends Controller
{
    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }

    public function dashboard(Request $request)
    {
        try {
            $type = $request->input('type');
            $size = $request->input('size');
            $sort = $request->input('sort');
            $keyword = $request->input('keyword');

            $data = [
                'order_action' => $this->calculateOrder($type, $size, $sort, $keyword),
                'member_action' => $this->calculateMembers($type, $size, $sort, $keyword),
                'revenue_action' => $this->calculateRevenue($type, $size, $sort, $keyword),
            ];

            $res = returnMessage(1, $data, 'Success');
            return response($res, 200);
        } catch (\Exception $exception) {
            Log::error('Dashboard Error: ' . $exception->getMessage());
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    private function calculateMembers($type, $size, $sort, $keyword)
    {
        $members = User::where('status', '!=', UserStatus::DELETED);

        $totalMembers = $members->count();
        $currentMember = $members->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->count();
        $prevMembers = $members->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('created_at', '<=', Carbon::now()->subMonth()->endOfMonth())
            ->count();

        $is_increase = $currentMember >= $prevMembers;

        if ($prevMembers === 0 && $currentMember > 0) {
            $percentChange = $currentMember * 100;
        } elseif ($prevMembers > 0) {
            $percentChange = round(abs(($currentMember - $prevMembers) / $prevMembers) * 100, 2);
        } else {
            $percentChange = 0;
        }

        return [
            'total_member' => $totalMembers,
            'current_member' => $currentMember,
            'prev_member' => $prevMembers,
            'is_increase' => $is_increase,
            'percent_change' => $percentChange,
        ];
    }

    private function calculateOrder($type, $size, $sort, $keyword)
    {
        $orders = Orders::where('status', '!=', OrderStatus::CANCELED)
            ->where('status', '!=', OrderStatus::DELETED);

        $totalOrder = $orders->count();
        $currentOrder = $orders->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->count();

        $prevOrder = $orders->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('created_at', '<=', Carbon::now()->subMonth()->endOfMonth())
            ->count();

        $is_increase = $currentOrder >= $prevOrder;

        if ($prevOrder === 0 && $currentOrder > 0) {
            $percentChange = $currentOrder * 100;
        } elseif ($prevOrder > 0) {
            $percentChange = round(abs(($currentOrder - $prevOrder) / $prevOrder) * 100, 2);
        } else {
            $percentChange = 0;
        }

        return [
            'total_order' => $totalOrder,
            'current_order' => $currentOrder,
            'prev_order' => $prevOrder,
            'is_increase' => $is_increase,
            'percent_change' => $percentChange,
        ];
    }

//    private function calculateMembers($type, $size, $sort, $keyword)
//    {
//        $time = Carbon::now();
//        return $this->calculateStatistics($time, User::class, 'status', UserStatus::DELETED, 'created_at', $type, $size, $sort, $keyword);
//    }
//
//    private function calculateOrder($type, $size, $sort, $keyword)
//    {
//        $time = Carbon::now();
//        return $this->calculateStatistics($time, Orders::class, 'status', OrderStatus::CANCELED, 'created_at', $type, $size, $sort, $keyword);
//    }

    private function calculateStatistics($time, $modelClass, $statusField, $deletedStatus, $dateField, $type, $size, $sort, $keyword)
    {
        $currentDate = $time;

        $membersQuery = $modelClass::where($statusField, '!=', $deletedStatus);

        $totalCount = $membersQuery->count();

        $currentCount = $membersQuery->where($dateField, '>=', $currentDate->startOfMonth())
            ->where($dateField, '<=', $currentDate->endOfMonth());

        $s = $time->month;
        $e = $currentDate->endOfMonth();

        $prevCount = $membersQuery->where($dateField, '>=', $currentDate->subMonth()->startOfMonth())
            ->where($dateField, '<=', $currentDate->subMonth()->endOfMonth());

        $prevCount = $prevCount->count();
        $currentCount = $currentCount->count();
        $isIncrease = $currentCount >= $prevCount;

        if ($prevCount === 0 && $currentCount > 0) {
            $percentChange = 100;
        } elseif ($prevCount > 0) {
            $percentChange = round(abs(($currentCount - $prevCount) / $prevCount) * 100, 2);
        } else {
            $percentChange = 0;
        }

        return [
            'total' => $totalCount,
            'current' => $currentCount,

            'size' => $size,
            'sort' => $sort,
            'keyword' => $keyword,

            's' => $s,
            'e' => $e,

            'previous' => $prevCount,
            'is_increase' => $isIncrease,
            'percent_change' => $percentChange,
        ];
    }

    private function calculateRevenue($type, $size, $sort, $keyword)
    {
        $totalRevenue = Revenues::sum('total');
        $currentMonth = Carbon::now()->month;
        $currentTotalRevenue = Revenues::where('month', $currentMonth)->sum('total');
        $prevTotalRevenue = Revenues::where('month', $currentMonth - 1)->sum('total');

        $is_increase = $currentTotalRevenue >= $prevTotalRevenue;
        $percent = $prevTotalRevenue > 0 ? (($currentTotalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'current_total_revenue' => $currentTotalRevenue,
            'prev_total_revenue' => $prevTotalRevenue,
            'is_increase' => $is_increase,
            'percent_change' => round($percent, 2),
        ];
    }

}
