<?php

namespace App\Http\Controllers;

use App\Models\PhotoGrid;
use Illuminate\Http\Request;

class PhotoGridController extends Controller
{
    public function index()
    {
        return $photoGrids = PhotoGrid::all();
    }
}
