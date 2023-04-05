<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {   
            $query = Transaction::query();

            return DataTables::of($query)
                ->addColumn('action', function($item){ // untuk edit
                    return '
                        <a class="inline-block border border-cyan-600 bg-cyan-700 text-gray rounded-md px-2 py-1 m-1 transition duration-500 ease select-none hover:bg-cyan-800 focus:outline-none focus:shadow-outline" 
                            href="' . route('dashboard.transaction.show', $item->id) . '">
                            Show
                        </a>
                        <a href="'. route('dashboard.transaction.edit', $item->id) .'" class="inline-block border border-gray-500 bg-gray-500 text-white rounded-md px-2 py-1 m-1 transition duration-500 ease select-none hover:bg-gray-800 focus:outline-none focus:shadow-outline">
                            Edit
                        </a>
                    ';
                })
                ->editColumn('total_price', function($item){
                return number_format($item->total_price);
            })
            ->rawColumns(['action']) //digunakan agar a href bisa digunakan
            ->make();
        }
        return view('pages.dashboard.transaction.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        if(request()->ajax())
        {   
            $query = TransactionItem::with(['product'])->where('transactions_id', $transaction->id); // with buat relasi antar table

            return DataTables::of($query)
                ->editColumn('product.price', function($item){
                return number_format($item->product->price);
            })
            ->rawColumns(['action']) //digunakan agar a href bisa digunakan
            ->make();
        }
        return view('pages.dashboard.transaction.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
