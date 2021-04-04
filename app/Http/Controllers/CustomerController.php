<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function add(Request $request)
    {
        if (!$request->has('customer_name') || $request->customer_name == '') {
            $res['success'] = false;
            $res['message'] = 'Add customer failed! name is required';
            return response($res, 400);
        }

        $name = $request->customer_name;
        $address = $request->customer_address;
        $phone = $request->customer_phone;
        DB::beginTransaction();
        try {
            DB::table("m_customer")->insert([
                'customer_name' => $name,
                'customer_address' => $address,
                'customer_phone' => $phone,
            ]);

            DB::commit();
            $res['success'] = true;
            $res['message'] = "Add customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function edit(Request $request)
    {
        if (!$request->has('customer_id') || !$request->has('customer_name') || $request->customer_id == '' || $request->customer_name == '') {
            $res['success'] = false;
            $res['message'] = 'Edit customer failed! ID and name is required';
            return response($res, 400);
        }

        $id = $request->customer_id;
        $name = $request->customer_name;
        $address = $request->customer_address;
        $phone = $request->customer_phone;

        DB::beginTransaction();
        try {
            DB::table("m_customer")->where('customer_id', $id)->update([
                'customer_name' => $name,
                'customer_address' => $address,
                'customer_phone' => $phone,

            ]);
            DB::commit();
            $res['success'] = true;
            $res['message'] = "Edit customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function get(Request $request)
    {
        $id = $request->customer_id;
        try {
            $data = DB::table("m_customer")->where("customer_id", $id)->first();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "get customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function delete(Request $request)
    {
        if (!$request->has('customer_id')) {
            $res['success'] = false;
            $res['message'] = 'Delete customer failed! ID is required';
            return response($res, 400);
        }

        $id = $request->customer_id;
        DB::beginTransaction();
        try {
            $_reg = DB::delete("DELETE FROM m_customer WHERE customer_id = ?", [$id]);
            DB::commit();
            $res['success'] = true;
            $res['message'] = "Delete customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    function list(Request $request) {
        try {
            $data = DB::table("m_customer")->orderBy("customer_name", "asc")->get();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "get list customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }
    public function search(Request $request)
    {
        $search = "%" . $request->search_keyword . "%";
        try {
            $data = DB::table("m_customer")->whereRaw("customer_name LIKE ?", [$search])->get();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "search customer success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }
}
