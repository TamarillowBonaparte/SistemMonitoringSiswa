<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return view('lokasi', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Location::create([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'status' => $request->status,
        ]);

        return redirect()->route('lokasi')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }


    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

        public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
            'status' => 'required',
        ]);

        $location = Location::findOrFail($id);
        $location->update($request->all());

        return redirect()->route('lokasi')->with('success', 'Data lokasi berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Data lokasi berhasil dihapus.');
    }

}
