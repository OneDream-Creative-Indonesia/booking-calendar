<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeychainController extends Controller
{
    public function index()
    {
        return \App\Models\Keychain::all();
    }
}
