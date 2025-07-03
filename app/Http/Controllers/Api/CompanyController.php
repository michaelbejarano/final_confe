<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
///cambie eto 
use App\Rules\UniqueCompanyRule;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::get();

        return response()->json($companies, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => ['required','string', 'regex:/^(10|20)\d{9}$/',
        
             // Aquí aseguramos que el ruc sea único en la tabla "companies" para el mismo usuario
        \Illuminate\Validation\Rule::unique('companies')->where(function ($query) {
            return $query->where('user_id', auth()->id());
        }),

        ],
            'direccion' => 'required|string|max:255',
            'logo' => 'nullable|file|image',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'required|string|max:255',
            'cert' => 'required|file|mimes:pem,txt',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'nullable|boolean',

        ]);
        //base de datos solo guarda ubicacion cadena no archivo
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }
        //archivos guardaremos en el servidor pero estmos de manera local asi que en carpetas
        $data['cert_path'] = $request->file('cert')->store('certs');

        $data['user_id'] = auth()->id();

        $company = Company::create($data);

        //si todo sale bien que me retorne la respuesta
        return response()->json([
            'message'=> 'Empresa creada con exito',
            'company'=> $company
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show( $company)
    {
        $company = Company::where('ruc', $company)
            ->where('user_id',auth()->id())
            ->firstOrFail();

        return response()->json($company,200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $company = Company::where('ruc',$company)
            ->where('user_id',auth()->id())
            ->firstOrFail();
        
            $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => ['required','string', 'regex:/^(10|20)\d{9}$/',],
            'direccion' => 'required|string|max:255',
            'logo' => 'nullable|file|image',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'required|string|max:255',
            'cert' => 'required|file|mimes:pem,txt',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'nullable|boolean',

        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        if ($request->hasFile('cert')) {
            $data['cert_path'] = $request->file('cert')->store('certs');
        }

        $company->update($data);

        return response()->json([
            'message' => 'Empresa actualizada con exito',
            'com' => $company
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $company)
    {
        $company = Company::where('ruc',$company)
            ->where('user_id',auth()->id())
            ->firstOrFail();

        $company->delete();

        return response()->json([
            'message' => 'Empresa borrada con exito',
            
        ], 200);
    }
}
