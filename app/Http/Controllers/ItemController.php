<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();
        return new JsonResponse([
            'data' => $items,
            'message'=> 'success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        if(!$request->item_name || !$request->item_price || !$request->item_qty){
            return new JsonResponse([
                'message' => 'Bad Request'
            ], 400);
        }

        $newItem = Item::create([
            'item_name' => $request->item_name,
            'item_price' => $request->item_price,
            'item_qty' => $request->item_qty
        ]);

        return new JsonResponse([
            'data' => $newItem,
            'message' => 'success'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return  new JsonResponse([
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // note
        // sebenernya ini gaperlu secara fungsionalitas
        // tapi biar clear kalau requestnya gak sesuai, user ke notify
        //
        if(!$item->item_name && !$item->item_price &&!$item->item_qty) {
            return new JsonResponse([
                'message' => 'Bad Request',
            ], 400);
        }

        // checking if there is a request on a spesific column
        // this will ignore unnecessary request
        $item->item_name = ($request->item_name) ? $request->item_name : $item->item_name;
        $item->item_price = ($request->item_price) ? $request->item_price : $item->item_price;
        $item->item_qty = ($request->item_qty) ? $request->item_qty : $item->item_qty;

        $item->save();

        return new JsonResponse([
            'message' => 'Data Saved',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return new JsonResponse([
            'message' => 'Object Deleted',
        ]);
    }
}
