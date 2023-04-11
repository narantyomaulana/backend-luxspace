<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use App\Models\Cart;
use Midtrans\Config;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CheckoutRequest;

class FrontendController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::with(['galleries'])->latest()->get();
        return view('pages.frontend.index', compact('products'));
    }
    public function details(Request $request, $slug)
    {
        $product = Product::with(['galleries'])->where('slug', $slug)->firstOrFail(); //fungsi firstOrFail jika slug urlnya salah maka muncul 404
        $recommendations = Product::with(['galleries'])->inRandomOrder()->limit(4)->get(); //InrandomOrder Digunakan buat mengambil data random 
        return view('pages.frontend.details', compact('product', 'recommendations'));
    }
    public function cartAdd(Request $request, $id)
    {
        Cart::create([
            'users_id' => Auth::user()->id,
            'products_id' => $id
        ]);

        return redirect('cart');
    }

    public function cart(Request $request)
    {
        $carts = Cart::with(['product.galleries'])->where('users_id', Auth::user()->id)->get();
        return view('pages.frontend.cart', compact('carts'));
    }
    public function cartDelete(Request $request, $id)
    {
        $item = Cart::findOrFail($id);
        $item->delete();

        return redirect('cart');
    }
   
    public function checkout(CheckoutRequest $request)
    {
        // return $request->all(); // fungsi buat mengecek data kita masuk
        $data = $request->all();

        // Get Carts Data
        $carts = Cart::with(['product'])->where('users_id', Auth::user()->id)->get();

        // Add to Transaction Data
        $data['users_id'] = Auth::user()->id;
        $data['total_price'] = $carts->sum('product.price');

        //Create Transaction Data
        $transaction = Transaction::create($data);

        // Create Transaction Item
        foreach ($carts as $cart) {
            $item[] = TransactionItem::create([
                'transactions_id' => $transaction->id,
                'users_id' => $cart->users_id,
                'products_id' => $cart->products_id,
            ]);
        }

        // Delete cart after transaction
        Cart::where('users_id', Auth::user()->id)->delete(); //menghapus data cart ketika user yg sedang login atau sesudah checkout 

        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Setup Variable Midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => 'LX-' . $transaction->id,
                'gross_amount' => (int) $transaction->total_price 
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email
            ],
            'enabled_payments' => ['gopay','bank_transfer'],
            'vtweb' => []
        ];

        // Payment Process
        try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;

            $transaction->payment_url = $paymentUrl;
            $transaction->save();
            
            // Redirect to Snap Payment Page
            return redirect($paymentUrl);
          }
          catch (Exception $e) {
            return $e;
        }
    }

    public function success(Request $request)
    {
        return view('pages.frontend.success');
    }
}
