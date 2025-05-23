<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DrugSearchController extends Controller
{
    public function showSearchForm()
    {
        return view('drug-search');
    }

    private function fetchAndProcessDrugData(string $inputNdcCodesString): array
    {
        $ndcCodes = array_unique(array_filter(array_map('trim', explode(',', $inputNdcCodesString))));
        if (empty($ndcCodes)) {
            return [];
        }

        $results = [];
        $codesToSearchInApi = [];

        foreach ($ndcCodes as $ndcCode) {
            try {
                $drugFromDb = Drug::where('ndc_code', $ndcCode)->first();
                if ($drugFromDb) {
                    $results[$ndcCode] = [
                        'ndc_code' => $drugFromDb->ndc_code,
                        'brand_name' => $drugFromDb->brand_name,
                        'generic_name' => $drugFromDb->generic_name,
                        'labeler_name' => $drugFromDb->labeler_name,
                        'product_type' => $drugFromDb->product_type,
                        'source' => 'Database',
                    ];
                } else {
                    $codesToSearchInApi[] = $ndcCode;
                }
            } catch (\Exception $e) {
                Log::error('Error querying database for NDC: ' . $ndcCode, ['exception' => $e->getMessage()]);
                $codesToSearchInApi[] = $ndcCode;
            }
        }

        if (!empty($codesToSearchInApi)) {
            $searchQuery = collect($codesToSearchInApi)
                ->map(fn($code) => 'product_ndc:"' . $code . '"')
                ->implode(' OR ');

            $openFdaUrl = 'https://api.fda.gov/drug/ndc.json';
            $apiParams = ['search' => $searchQuery, 'limit' => count($codesToSearchInApi) * 2];

            try {
                $response = Http::timeout(30)->get($openFdaUrl, $apiParams);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if (isset($apiData['results']) && is_array($apiData['results']) && !empty($apiData['results'])) {
                        foreach ($apiData['results'] as $item) {
                            $foundNdc = $item['product_ndc'] ?? null;
                            if ($foundNdc && in_array($foundNdc, $codesToSearchInApi) && !isset($results[$foundNdc])) {
                                $brandName = $item['openfda']['brand_name'][0] ?? ($item['brand_name'] ?? null);
                                if (is_array($brandName)) $brandName = $brandName[0] ?? null;
                                $genericName = $item['openfda']['generic_name'][0] ?? ($item['generic_name'] ?? null);
                                if (is_array($genericName)) $genericName = $genericName[0] ?? null;
                                $labelerName = $item['openfda']['manufacturer_name'][0] ?? ($item['labeler_name'] ?? null);
                                if (is_array($labelerName)) $labelerName = $labelerName[0] ?? null;
                                $productType = $item['openfda']['product_type'][0] ?? ($item['product_type'] ?? null);
                                if (is_array($productType)) $productType = $productType[0] ?? null;

                                $drugData = [
                                    'ndc_code' => $foundNdc,
                                    'brand_name' => $brandName,
                                    'generic_name' => $genericName,
                                    'labeler_name' => $labelerName,
                                    'product_type' => $productType,
                                ];
                                Drug::create($drugData);
                                $results[$foundNdc] = array_merge($drugData, ['source' => 'OpenFDA']);
                                $codesToSearchInApi = array_filter($codesToSearchInApi, fn($c) => $c !== $foundNdc);
                            }
                        }
                    } else {
                        Log::info('OpenFDA API successful but no results found or empty results array.', [
                            'status' => $response->status(), 'body' => $apiData, 'url' => $openFdaUrl, 'params' => $apiParams
                        ]);
                    }
                } else if ($response->status() == 404 && isset($response->json()['error']['code']) && $response->json()['error']['code'] === 'NOT_FOUND') {
                    Log::info('OpenFDA API reported no matches for the search query.', [
                        'status' => $response->status(), 'body' => $response->json(), 'url' => $openFdaUrl, 'params' => $apiParams
                    ]);
                } else {
                    Log::error('OpenFDA API request failed with an unexpected status.', [
                        'status' => $response->status(), 'body' => $response->body(), 'url' => $openFdaUrl, 'params' => $apiParams
                    ]);
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('OpenFDA API connection error: ' . $e->getMessage(), [
                    'url' => $openFdaUrl, 'params' => $apiParams, 'trace' => $e->getTraceAsString()
                ]);
            } catch (\Exception $e) {
                Log::error('An unexpected error occurred during OpenFDA search: ' . $e->getMessage(), [
                    'url' => $openFdaUrl, 'params' => $apiParams, 'trace' => $e->getTraceAsString()
                ]);
            }
        }

        foreach ($codesToSearchInApi as $notFoundCode) {
            if (!isset($results[$notFoundCode])) {
                $results[$notFoundCode] = [
                    'ndc_code' => $notFoundCode,
                    'brand_name' => '-',
                    'generic_name' => '-',
                    'labeler_name' => '-',
                    'product_type' => '-',
                    'source' => 'Nuk u Gjet',
                ];
            }
        }

        $orderedResults = [];
        foreach ($ndcCodes as $originalCode) {
            if (isset($results[$originalCode])) {
                $orderedResults[] = $results[$originalCode];
            }
        }
        return $orderedResults;
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ndc_codes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('drug.search.form')
                ->withErrors($validator)
                ->withInput();
        }
        $inputNdcCodesString = $request->input('ndc_codes');
        $orderedResults = $this->fetchAndProcessDrugData($inputNdcCodesString);

        return view('drug-search', [
            'results' => $orderedResults,
            'ndc_codes_submitted' => $inputNdcCodesString
        ]);
    }

    public function exportCsv(Request $request)
    {
        $inputNdcCodesString = $request->input('ndc_codes');

        if (empty($inputNdcCodesString)) {
            return redirect()->route('drug.search.form')->with('error', 'Nuk ka kode NDC për të eksportuar.');
        }

        $orderedResults = $this->fetchAndProcessDrugData($inputNdcCodesString);

        if (empty($orderedResults)) {
            return redirect()->route('drug.search.form')->with('error', 'Nuk u gjetën rezultate për të eksportuar bazuar në kodet e dhëna.');
        }

        $fileName = "rezultatet_kerkim_ilaçeve_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['KODI NDC', 'EMRI PRODUKTIT', 'PRODHUESI', 'LLOJI I PRODUKTIT', 'BURIMI'];

        $callback = function() use($orderedResults, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orderedResults as $drug) {
                fputcsv($file, [
                    $drug['ndc_code'],
                    $drug['brand_name'] ?? ($drug['generic_name'] ?? '-'),
                    $drug['labeler_name'] ?? '-',
                    $drug['product_type'] ?? '-',
                    $drug['source'] ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
