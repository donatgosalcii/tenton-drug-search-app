<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoredDrugController extends Controller
{

    public function index()
    {
        $drugs = Drug::orderBy('created_at', 'desc')->paginate(15);
        return view('stored-drugs.index', compact('drugs'));
    }

    public function create()
    {
        return view('stored-drugs.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ndc_code' => 'required|string|unique:drugs,ndc_code|max:255',
            'brand_name' => 'nullable|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'labeler_name' => 'nullable|string|max:255',
            'product_type' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('stored-drugs.create')
                ->withErrors($validator)
                ->withInput();
        }

        Drug::create($request->all());

        return redirect()->route('stored-drugs.index')->with('success', 'Ilaçi u shtua me sukses!');
    }

    public function destroy(Drug $drug)
    {
        $drug->delete();
        return redirect()->route('stored-drugs.index')->with('success', 'Ilaçi u fshi me sukses!');
    }
}
