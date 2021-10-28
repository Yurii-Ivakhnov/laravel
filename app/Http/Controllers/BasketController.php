<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function basket(): Renderable
    {
        $orderId = session('orderId');
        if (!is_null($orderId)) {
            $order = Order::findOrFail($orderId);
//            dd(is_null($order->product));
//            if (is_null($order->product)) {
//                dd(is_null($order->product));
//                return redirect()->route('products.index');
//            }
//            dd($order->products);
            return view('user.basket.show', compact('order'));
        }
        return redirect()->route('products.index');
    }

    public function basketPlace(): Renderable
    {
        $orderId = session('orderId');
        if (is_null($orderId)){
            return redirect()->route('products.index');
        }
        $order = Order::find($orderId);
        return view('user.basket.order', compact('order'));
    }

    public function basketConfirm(Request $request)
    {
        $orderId = session('orderId');
        if (is_null($orderId)){
            return redirect()->route('products.index');
        }
        $order = Order::find($orderId);
        $success = $order-> saveOrder($request->name, $request->phone);

        if ($success) {
            session()->flash('success', 'Ваш заказ принят в обработку!');
        } else {
            session()->flash('warning', 'Случилась ошибка');
        }

        return redirect()->route('products.index');
    }

    public function basketAdd($productId)
    {
        $orderId = session('orderId');
        if (is_null($orderId)) {
            $order = Order::create();
            session(['orderId' => $order->id]);
        } else {
            $order = Order::find($orderId);
        }

        if ($order->products->contains($productId)) {
            $pivotRow = $order->products()->where('product_id', $productId)->first()->pivot;
            $pivotRow->count++;
            $pivotRow->update();
        } else {
            $order->products()->attach($productId);
        }
        if (Auth::check()) {
            $order->user_id = Auth::id();
            $order->save();
        }

        $product = Product::find($productId);

        session()->flash('success','Добавлен товар: '.$product->name);

        return redirect()->route('user.basket.show');
    }

    public function basketRemove($productId)
    {
        $orderId = session('orderId');
        if (is_null($orderId)) {
            return redirect()->route('user.basket.show');
        }

        $order = Order::find($orderId);
        if ($order->products->contains($productId)) {
            $pivotRow = $order->products()->where('product_id', $productId)->first()->pivot;
            if ($pivotRow->count < 2) {
                $order->products()->detach($productId);
            } else {
                $pivotRow->count--;
                $pivotRow->update();
            }
            $product = Product::find($productId);
            session()->flash('warning', 'Удален товар: '. $product->name);

            return redirect()->route('user.basket.show');
        }
    }
}