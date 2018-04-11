<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GoogleMaps;

class MainController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'direccion'=>'required|string'
        ]);

        $response = GoogleMaps::load('geocoding')
            ->setParam ([
                'address'    => $request->get('direccion'),
                'components' => [
                    'country' => 'MX',
                ]
            ])
            ->setEndpoint('json')
            ->get();

        $data = json_decode($response);


        if($data->status != 'OK'){
            $data = [
                'status' => 'ERROR, VERIFICA LA DIRECCIÓN',
                'results'=> []
            ];
        }

        $data = collect($data)->toJson();

        return response($data,200)->header('Content-Type', 'application/json');


    }
}
