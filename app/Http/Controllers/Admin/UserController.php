<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected function index()
    {
        $clients = User::get();
        return view('admin.client.index', compact('clients'));
    }
}