<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;

class AddressController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
{
    return $request->user()->addresses;
}

public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required_without:lat',
        'city' => 'required_without:lat',
        'address' => 'required_without:lat',
        'postal_code'=>'nullable',
        'phone'=>'nullable',
        'lat' => 'nullable|numeric',
        'lng' => 'nullable|numeric',
        'location_type' => 'nullable|in:manual,map',
    ]);
    if ($request->location_type === 'map') {
    if (!$data['title']) {
        $data['title'] = 'موقع من الخريطة';
    }
    }
    return $request->user()->addresses()->create($data);
}
public function update(Request $request, Address $address)
{
    $data = $request->validate([
        'title' => 'required_without:lat',
        'city' => 'required_without:lat',
        'address' => 'required_without:lat',
        'postal_code'=>'nullable',
        'phone'=>'nullable',
         'lat' => 'nullable|numeric',
        'lng' => 'nullable|numeric',
        'location_type' => 'nullable|in:manual,map',
    ]);
    if ($request->location_type === 'map') {
    if (!$data['title']) {
        $data['title'] = 'موقع من الخريطة';
    }
    }
    $this->authorize('update',$address);
    $address->update($data);

    return $address;
}
public function destroy(Address $address)
{
    $this->authorize('delete',$address);

    $address->delete();

    return response()->json(['message'=>'deleted']);
}
}
