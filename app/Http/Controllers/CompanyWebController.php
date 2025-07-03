<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Rules\UniqueCompanyRule;

class CompanyWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        // Búsqueda por razón social
        if ($request->has('search')) {
            $query->where('razon_social', 'LIKE', '%' . $request->search . '%');
        }

        // Mostrar solo las del usuario actual (esto es opcional si ya tienes login por token)
        // $query->where('user_id', auth()->id());

        $companies = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => ['required', 'string', 'regex:/^(10|20)\d{9}$/', new UniqueCompanyRule()],
            'direccion' => 'required|string|max:255',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'required|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png',
            'cert' => 'required|file|mimes:pem,txt',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        $data['cert_path'] = $request->file('cert')->store('certs');
        $data['user_id'] = auth()->id() ?? 1; // Temporal si no usas auth

        $company = Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Empresa registrada correctamente.');
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);

        return view('companies.show', compact('company'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => ['required', 'string', 'regex:/^(10|20)\d{9}$/', new UniqueCompanyRule($company->id)],
            'direccion' => 'required|string|max:255',
            'sol_user' => 'required|string|max:255',
            'sol_pass' => 'required|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:255',
            'production' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png',
            'cert' => 'nullable|file|mimes:pem,txt',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos');
        }

        if ($request->hasFile('cert')) {
            $data['cert_path'] = $request->file('cert')->store('certs');
        }

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Empresa actualizada.');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        // También puedes eliminar los archivos si deseas
        if ($company->logo_path) Storage::delete($company->logo_path);
        if ($company->cert_path) Storage::delete($company->cert_path);

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Empresa eliminada.');
    }
}
