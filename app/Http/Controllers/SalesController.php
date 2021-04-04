<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
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
        if (!$request->has('sales_date')) {
            $res['success'] = false;
            $res['message'] = 'Add sales failed! sales date is required';
            return response($res, 400);
        }

        if (!$request->has('customer_id')) {
            $res['success'] = false;
            $res['message'] = 'Add sales failed! customer  is required';
            return response($res, 400);
        }

        if (!$request->has('list_cart')) {
            $res['success'] = false;
            $res['message'] = 'Add sales failed! list cart  is required';
            return response($res, 400);
        }

        $rdate = $request->sales_date;
        $disc = $request->discount;
        $cid = $request->customer_id;
        $data = $request->list_cart;

        DB::beginTransaction();
        try {
            $id = DB::table("t_sales")->insertGetId([
                'sales_date' => $rdate,
                'customer_id' => $cid,
                'discount' => $disc,
            ]);

            foreach ($data as $bb) {
                $salesid = $id;
                $itemid = $bb['item_id'];
                $qty = $bb['item_qty'];
                $price = $bb['item_price'];
                DB::table("t_sales_detail")->insert([
                    'sales_id' => $salesid,
                    'item_id' => $itemid,
                    'item_qty' => $qty,
                    'item_price' => $price,
                ]);
            }

            DB::commit();
            $res['success'] = true;
            $res['sales_id'] = $id;
            $res['message'] = "Success save sales";
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
        $salesid = $request->sales_id;
        DB::beginTransaction();
        try {
            $sales_detail = DB::table("t_sales_detail as t")->join("m_item as i", "t.item_id", "=", "i.item_id")->where("t.sales_id", $salesid)->select(DB::raw("i.*,i.item_price * i.item_qty as subtotal,i.item_name,i.item_package"))->get();

            $sales = DB::table("t_sales as t")->join("m_customer as c", "t.customer_id", "=", "c.customer_id")->select(DB::raw("t.*,c.customer_name"))->first();
            DB::commit();
            $res['success'] = true;
            $res['sales_detail'] = $sales_detail;
            $res['sales'] = $sales;
            $res['message'] = "Get sales by sales id";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function delete(Request $request)
    {
        if (!$request->has('sales_id')) {
            $res['success'] = false;
            $res['message'] = 'Delete sales failed! ID is required';
            return response($res, 400);
        }

        $id = $request->sales_id;
        DB::beginTransaction();
        try {
            DB::delete("DELETE FROM t_sales WHERE sales_id = ?", [$id]);
            DB::commit();
            $res['success'] = true;
            $res['message'] = "Delete sales success";
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
            $list_sales = DB::table("t_sales as t")->join("m_customer as c", "t.customer_id", "=", "c.customer_id")->join(DB::raw("(SELECT sales_id,  IFNULL(SUM(item_price*item_qty),0) AS subtotal FROM t_sales_detail GROUP BY sales_id) as sales_dt"), "t.sales_id", "=", "sales_dt.sales_id")->select(DB::raw("t.*, c.customer_name,(IFNULL(sales_dt.subtotal,0) * (1-t.discount/100)) as total"))->orderBy("t.sales_id")->orderBy("t.sales_date")->get();

            $res['success'] = true;
            $res['data'] = $list_sales;
            $res['message'] = "get list sales success";
            return response($res, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

}
