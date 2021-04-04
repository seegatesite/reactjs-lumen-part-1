<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class itemController extends Controller
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
        if (!$request->has('item_name') || $request->item_name == '') {
            $res['success'] = false;
            $res['message'] = 'add item failed! item name is required';
            return response($res, 400);
        }

        if (!$request->has('item_package') || $request->item_name == '') {
            $request->item_package = '';
        }

        if (!$request->has('item_price') || $request->item_price == '') {
            $request->item_price = 0;
        }

        if (!$request->has('item_stock') || $request->item_stock == '') {
            $request->item_stock = 0;
        }

        $item_name = $request->item_name;
        $item_price = $request->item_price;
        $item_package = $request->item_package;
        $item_stock = $request->item_stock;
        DB::beginTransaction();
        try {
            DB::table("m_item")->insert([
                'item_name' => $item_name,
                'item_stock' => $item_stock,
                'item_price' => $item_price,
                'item_package' => $item_package,
            ]);

            DB::commit();
            $res['success'] = true;
            $res['message'] = "Add item success";
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
        if (!$request->has('item_id') || !$request->has('item_name') || $request->item_id == '' || $request->item_name == '') {
            $res['success'] = false;
            $res['message'] = 'Edit item failed! ID and name is required';
            return response($res, 400);
        }
        if (!$request->has('item_package') || $request->item_name == '') {
            $request->item_package = '';
        }

        if (!$request->has('item_price') || $request->item_price == '') {
            $request->item_price = 0;
        }

        if (!$request->has('item_stock') || $request->item_stock == '') {
            $request->item_stock = 0;
        }

        $item_id = $request->item_id;
        $item_name = $request->item_name;
        $item_price = $request->item_price;
         $item_package = $request->item_package;
        $item_stock = $request->item_stock;

        DB::beginTransaction();
        try {
            DB::table("m_item")->where('item_id', $item_id)->update([
                'item_name' => $item_name,
                'item_stock' => $item_stock,
                'item_price' => $item_price,
                'item_package' => $item_package,
 
            ]);
            DB::commit();
            $res['success'] = true;
            $res['message'] = "Edit item success";
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
        $id = $request->item_id;
        try {
            $data = DB::table("m_item")->where("item_id", $id)->first();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "get item success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function delete(Request $request)
    {
        if (!$request->has('item_id')) {
            $res['success'] = false;
            $res['message'] = 'Delete item failed! ID is required';
            return response($res, 400);
        }

        $id = $request->item_id;
        DB::beginTransaction();
        try {
            DB::delete("DELETE FROM m_item WHERE item_id = ?", [$id]);
            DB::commit();
            $res['success'] = true;
            $res['message'] = "Delete item success";
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
            $data = DB::table("m_item")->orderBy("item_name", "asc")->get();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "get list item success";
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
            $data = DB::table("m_item")->whereRaw("item_name LIKE ?", [$search])->get();
            $res['success'] = true;
            $res['data'] = $data;
            $res['message'] = "search item success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }
}
