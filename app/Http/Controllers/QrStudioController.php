<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use App\Models\Kategori;
use App\Models\KopConfig;

class QrStudioController extends Controller
{
    public function index(Request $request)
    {
        $kategoriId = $request->query('kategori_id');
        $search = $request->query('search');

        $query = Inventaris::with(['kategori', 'lokasi']);

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama_barang', 'asc')->get();
        $kategoriList = Kategori::all();
        $kopConfig = KopConfig::first();

        return view('qr.index', compact('items', 'kategoriList', 'kopConfig'));
    }

    public function print(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        $format = $request->input('format', 'grid-3x8'); // grid-1x1, grid-2x4, grid-3x8

        $items = Inventaris::whereIn('id', $selectedIds)->with(['kategori', 'lokasi'])->get();
        $kopConfig = KopConfig::first();

        return view('qr.print', compact('items', 'format', 'kopConfig'));
    }
}
