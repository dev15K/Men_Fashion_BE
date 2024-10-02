<?php

if (!function_exists('returnMessage')) {
    function returnMessage($type, $data, $message)
    {
        if ($type == -1) {
            $data = [
                'type' => 'error',
                'status' => 'error',
                'message' => $message,
                'data' => $data,
            ];
        } else {
            $data = [
                'type' => 'success',
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ];
        }

        return $data;
    }
}
