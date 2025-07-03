<?php

namespace App\Services;

use DateTime;
use Greenter\See;
use Greenter\Report\PdfReport;
use Greenter\Model\Sale\Legend;
use Greenter\Report\HtmlReport;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Support\Facades\Storage;

use App\Models\Company as ModelsCompany;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Report\Resolver\DefaultTemplateResolver;

    class SunatService{
        public function getSee($company) {
            $certificate = Storage::get($company->cert_path);

            $see = new See();
            $see->setCertificate($certificate);
            $see->setService($company->production ?SunatEndpoints::FE_BETA : SunatEndpoints::FE_BETA);
            $see->setClaveSOL($company->ruc, $company->sol_user, $company->sol_pass);

            return $see;
        
        }

        public function getInvoice($data){
            return (new Invoice)
            ->setUblVersion($data['ublVersion'] ?? '2.1')
            ->setTipoOperacion($data['tipoOperacion'] ?? null) // Venta-Catalog. 51
            ->setTipoDoc($data['tipoDoc'] ?? null) // Factura - Catalog. 01 
            ->setSerie($data['serie'] ?? null )
            ->setCorrelativo($data['correlativo'] ?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($data['tipoMoneda'] ?? null) // Sol -Catalog. 02
            ->setCompany($this->getCompany($data['company']))
            ->setClient($this->getClient($data['client']))

                //Mto Operaciones
                ->setMtoOperGravadas($data['mtoOperGravadas'] ?? null)
                ->setMtoOperExoneradas($data['mtoOperExoneradas'] ?? null)
                ->setMtoOperInafectas($data['mtoOperInafectas'] ?? null)
                ->setMtoOperExportacion($data['mtoOperExportacion'] ?? null)
                ->setMtoOperGratuitas($data['mtoOperGratuitas'] ?? null)

                //IMPUESTOS
            ->setMtoIGV($data['mtoIGV'])
                ->setMtoIGVGratuitas($data['mtoIGVGratuitas'])
                ->setIcbper($data['icbper'])
                ->setTotalImpuestos($data['totalImpuestos'])



            //TOTALES
            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])

            //PRODUCTOS
            ->setDetails($this->getDetails($data['details']))

                //LEYENDAS
            ->setLegends($this->getLegends($data['legends']));      
            #aumente eso
            return $invoice;
    
        }

        public function getCompany($company){
            return (new Company())
                ->setRuc($company['ruc'] ?? null)
                ->setRazonSocial($company['razonSocial'] ?? null)
                ->setNombreComercial($company['nombreComercial'] ?? null)
                ->setAddress($this->getAddress($company['address'] )?? null);
        }

        public function getClient($client){
            return (new Client())
                ->setTipoDoc($client['tipoDoc'] ?? null)
                ->setNumDoc($client['numDoc'] ?? null)
                ->setRznSocial($client['rznSocial'] ?? null);
        }
                
        public function getAddress($address){
            return (new Address())
                ->setUbigueo($address['ubigeo'] ?? null)
                ->setDepartamento($address['departamento']  ?? null)
                ->setProvincia($address['provincia'] ?? null)
                ->setDistrito($address['distrito'] ?? null)
                ->setUrbanizacion($address['urbanizacion'] ?? null)
                ->setDireccion($address['direccion'] ?? null)
                ->setCodLocal($address['codLocal'] ?? null); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.
        }

        public function getDetails($details) {
            $green_details = [];

                foreach ($details as $detail) {
                    $green_details[] = (new SaleDetail())
                    ->setCodProducto($detail['codProducto'] ?? null)
                    ->setUnidad($detail['unidad'] ?? null) // Unidad - Catalog. 03
                    ->setCantidad($detail['cantidad'] ?? null)
                    ->setMtoValorUnitario($detail['mtoValorUnitario'] ?? null)
                    ->setDescripcion($detail['descripcion'] ?? null)
                    ->setMtoBaseIgv($detail['mtoBaseIgv'] ?? null)
                    ->setPorcentajeIgv($detail['porcentajeIgv'] ?? null) // 18%
                    ->setIgv($detail['igv'] ?? null)
                    ->setFactorIcbper($detail['factorIcbper'] ?? null) // 0.3%
                    ->setIcbper($detail['icbper'] ?? null)
                    ->setTipAfeIgv($detail['tipAfeIgv'] ?? null) // Gravado Op. Onerosa - Catalog. 07
                    ->setTotalImpuestos($detail['totalImpuestos'] ?? null) // Suma de impuestos en el detalle
                    ->setMtoValorVenta($detail['mtoValorVenta'] ?? null)
                    ->setMtoPrecioUnitario($detail['mtoPrecioUnitario'] ?? null);
                }

                return $green_details;
        }
                
        public function getLegends($legends){
            $green_legends =[];

                #foreach ($variable as $key => $value) {
                foreach ($legends as $legend) {

                        $green_legends[] = (new Legend())
                        ->setCode($legend['code'] ?? null)
                        ->setValue($legend['value'] ?? null);
                }
                return $green_legends;
        }

        public function sunatResponse($result){
            $response['success'] = $result->isSuccess();

            // Verificamos que la conexión con SUNAT fue exitosa.
            if (!$response['success']) {

                // Mostrar error al conectarse a SUNAT.
                $response['error'] = [
                    'code' => $result->getError()->getCode(),
                    'message' => $result->getError()->getMessage()
                ];
                
                return $response;
            }
            $response['cdrZip'] = base64_encode($result->getCdrZip());

            $cdr = $result->getCdrResponse();

            $response['cdrResponse'] = [
                'code' => (int)$cdr->getCode(),
                'description' => $cdr->getDescription(),
                'notes' => $cdr->getNotes()
            ];

            return $response;
        }

        public function getHtmlReport($invoice){
            $report = new HtmlReport();

            $resolver = new DefaultTemplateResolver();
            $report->setTemplate($resolver->getTemplate($invoice));

            $ruc= $invoice->getCompany()->getRuc();
            $company = ModelsCompany::where('ruc', $ruc)->first();

            //params arreglo asociaitvo que va alamcenar a otros arreglos
            $params = [
                'system' => [
                    'logo' => Storage::get($company->logo_path),
                    'hash' => 'qqnr2dN4p/hmaEa/CJuVGo7dv5g' //valor de resumen
                ],

                'user'=> [
                    'header' => 'Telf:<b>(01) 123375 </br>',
                    'extras' => [
                        //LEYENDAS ADICIONALES
                        ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                        ['name' => 'vendor', 'value' => 'GITHUB SELLER'],

                    ],
                    'footer' => '<p>Nro. Resolucion: <b>32322323</b> </p>'
                ]
            ];
            return $report->render($invoice,$params );
        }

        public function generatedPdfReport($invoice) {
            #$htmlReport = new getHtmlReport();
            $htmlReport = $this->getHtmlReport($invoice);


            $resolver = new DefaultTemplateResolver();
            $htmlReport-> setTemplate($resolver-> getTemplate($invoice));

            $ruc = $invoice->getCompany()->getRuc();
            $company = ModelsCompany::where('ruc', $ruc)->first();

            $params = [
                'system' => [
                    'logo' => Storage::get($company->logo_path),
                    'hash' => 'qqnr2dN4p/hmaEa/CJuVGo7dv5g' //valor de resumen
                ],

                'user'=> [
                    'header' => 'Telf:<b>(01) 123375 </br>',
                    'extras' => [
                        //LEYENDAS ADICIONALES
                        ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                        ['name' => 'vendor', 'value' => 'GITHUB SELLER'],

                    ],
                    'footer' => '<p>Nro. Resolucion: <b>32322323</b> </p>'
                ]
            ];

            #$pdf = $report->render($invoice, $params);
            $pdf = $htmlReport;


            Storage::put('invoices/' . $invoice->getName() . '.pdf' , $pdf);
            


        }
    }
?>