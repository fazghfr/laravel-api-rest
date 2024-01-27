<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{

    private function isAuthorized(Item $item)
    {
        //user attempt pasti gak null, karena ada middleware.
        // -> check
        $user_attempt = auth('sanctum')->user()->email;
        $user_authorized = User::find($item->user_id)->email;

        if($user_attempt == $user_authorized)
        {
            return true;
        }

        return false;
    }
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
            'item_qty' => $request->item_qty,
            'user_id'  => Auth::user()->id
        ]);

        return new JsonResponse([
            'data' => $newItem,
            'message' => 'success'
        ], 200);
    }

    /*
        Display items by user id
    */
    public function user_items(User $user)
    {
        $items = Item::where('user_id', $user->id)->get();

        return new JsonResponse([
            'data' => $items,
            'message' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        if(!$this->isAuthorized($item))
        {
            return  new JsonResponse([
                'message' => 'unauthorized'
            ], 401);
        }
        return  new JsonResponse([
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {

        if(!$this->isAuthorized($item))
        {
            return  new JsonResponse([
                'message' => 'unauthorized'
            ], 401);
        }
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
        if(!$this->isAuthorized($item))
        {
            return  new JsonResponse([
                'message' => 'unauthorized'
            ], 401);
        }

        $item->delete();

        return new JsonResponse([
            'message' => 'Object Deleted',
        ]);
    }
}
