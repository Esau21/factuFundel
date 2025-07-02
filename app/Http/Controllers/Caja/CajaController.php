<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CajaController extends Controller
{
    public function index()
    {
        return view('cajas.index');
    }

    public function getIndexCaja(Request $request)
    {
        if ($request->ajax()) {
            $data = Caja::getindexData();

            return DataTables::of($data);
        }
    }
}
