<?php

namespace App\Http\Controllers\API;

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
        'title'=>'required',
        'city'=>'required',
        'address'=>'required',
        'postal_code'=>'nullable',
        'phone'=>'nullable'
    ]);

    return $request->user()->addresses()->create($data);
}
public function update(Request $request, Address $address)
{
    $data = $request->validate([
        'title'=>'required',
        'city'=>'required',
        'address'=>'required',
        'postal_code'=>'nullable',
        'phone'=>'nullable'
    ]);
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
