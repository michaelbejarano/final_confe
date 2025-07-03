<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\InvoiceController;
use App\Models\Factura;

class FacturaTestController extends Controller
{
    public function index()
    {
        return view('facturas.test')->with('response', session('response'));
    }
 public function create()
    {
        // 👉 Aquí carga la vista NUEVA para el formulario amigable
        return view('facturas.create');
    }

public function send(Request $request)
{
    $token = session('jwt_token');

    if (!$token) {
        return redirect()->route('login.form')->withErrors(['Debes iniciar sesión primero.']);
    }

    $jsonData = $request->except('_token');
    $jsonData['details'] = $request->input('details', []);

    // 👇 Calcular campos faltantes:
    foreach ($jsonData['details'] as &$item) {
        $item['mtoValorVenta'] = $item['cantidad'] * $item['mtoValorUnitario'];
        $item['mtoBaseIgv'] = $item['mtoValorVenta'];
        $item['porcentajeIgv'] = 18;
        $item['igv'] = $item['mtoBaseIgv'] * 0.18;
        $item['totalImpuestos'] = $item['igv'] + ($item['icbper'] ?? 0);
        $item['mtoPrecioUnitario'] = $item['mtoValorUnitario'] * 1.18;
    }

    try {
        $apiController = new \App\Http\Controllers\Api\InvoiceController();
        $apiRequest = new Request($jsonData);

        $apiRequest->setUserResolver(function () {
            return \App\Models\User::find(1);
        });

        $response = $apiController->send($apiRequest);

        $data = is_array($response)
            ? $response
            : (method_exists($response, 'getData') ? $response->getData(true) : []);


Factura::create([
    'tipo_doc' => $data['tipoDoc'],
    'serie' => $data['serie'],
    'correlativo' => $data['correlativo'],
    'hash' => $response['hash'],
    'estado_sunat' => $response['sunatResponse']['success'] ? 'Aceptada' : 'Rechazada',
    'cliente_ruc' => $data['client']['numDoc'],
    'cliente_razon_social' => $data['client']['rznSocial'],
    'total' => 120.30, // 👈 Calcula tu total real
    'fecha_emision' => now(),
    'xml_path' => $xmlPath, // guarda la ruta del XML firmado
    'cdr_path' => $cdrPath, // guarda la ruta del CDR ZIP
    'pdf_path' => $pdfPath, // si generas PDF
]);


        return redirect()->route('facturas.test')->with('response', $data);

    } catch (\Throwable $e) {
        return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
    }
}





    //condigo antiguo
//  public function send(Request $request)
// {
//     $token = session('jwt_token');

//     if (!$token) {
//         return redirect()->route('login.form')->withErrors(['Debes iniciar sesión primero.']);
//     }

//     // ⚡ Armamos el JSON a mano usando los campos del formulario
//     $jsonData = [
//         'tipoDoc' => $request->input('tipoDoc'),
//         'tipoOperacion' => $request->input('tipoOperacion'),
//         'serie' => $request->input('serie'),
//         'correlativo' => $request->input('correlativo'),
//         'fechaEmision' => $request->input('fechaEmision'),
//         'formaPago' => [
//             'moneda' => $request->input('moneda'),
//             'tipo' => $request->input('tipoPago')
//         ],
//         'tipoMoneda' => $request->input('moneda'),
//         'company' => [
//             'ruc' => '20100070970',
//             'razonSocial' => 'Compañía 1',
//             'nombreComercial' => 'Compañía 1',
//             'address' => [
//                 'ubigeo' => '040114',
//                 'departamento' => 'AREQUIPA',
//                 'provincia' => 'AREQUIPA',
//                 'distrito' => 'SOCABAYA',
//                 'urbanizacion' => '-',
//                 'direccion' => 'Arequipa Socabaya',
//                 'codLocal' => '0000'
//             ]
//         ],
//         'client' => [
//             'tipoDoc' => $request->input('tipoDocCliente'),
//             'numDoc' => $request->input('numDocCliente'),
//             'rznSocial' => $request->input('rznSocialCliente')
//         ],
//         'details' => []
//     ];

//     foreach ($request->input('descripcion') as $i => $desc) {
//         $jsonData['details'][] = [
//             'tipAfeIgv' => 10,
//             'codProducto' => 'P' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
//             'unidad' => 'NIU',
//             'descripcion' => $desc,
//             'cantidad' => $request->input('cantidad')[$i],
//             'mtoValorUnitario' => $request->input('precio_unitario')[$i],
//             'mtoValorVenta' => $request->input('cantidad')[$i] * $request->input('precio_unitario')[$i],
//             'mtoBaseIgv' => $request->input('cantidad')[$i] * $request->input('precio_unitario')[$i],
//             'porcentajeIgv' => 18,
//             'igv' => round($request->input('cantidad')[$i] * $request->input('precio_unitario')[$i] * 0.18, 2),
//             'totalImpuestos' => round($request->input('cantidad')[$i] * $request->input('precio_unitario')[$i] * 0.18, 2),
//             'mtoPrecioUnitario' => round($request->input('precio_unitario')[$i] * 1.18, 2),
//         ];
//     }

//     try {
//         $apiController = new InvoiceController();
//         $apiRequest = new Request($jsonData);
//         $apiRequest->setUserResolver(fn () => \App\Models\User::find(1));
//         $response = $apiController->send($apiRequest);
//         $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : []);
//         return redirect()->route('facturas.test')->with('response', $data);
//     } catch (\Throwable $e) {
//         return back()->withErrors(['error' => 'Error al procesar la factura: ' . $e->getMessage()]);
//     }
// }


}
