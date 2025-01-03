<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Document;
use App\Http\Resources\DocumentCollection;

class MobileController extends Controller
{
    private function getCurrentCompany()
    {
        return auth()->user()->company;
    }

    public function documents(Request $request)
    {
        $company = $this->getCurrentCompany();
        $records = Document::where('identification_number', $company->identification_number)
            ->orderBy('date_issue', 'desc')
            ->filter($request->search)
            ->paginate(20);

        $records->getCollection()->transform(function($row) {
            // dd($row);
            return [
                'id' => $row->id,
                'prefix' => $row->prefix,
                'number' => $row->number,
                'client' => $row->client,
                'currency' => $row->currency,
                'date' => $row->date_issue->format('Y-m-d'),
                'time' => $row->date_issue->format('H:i:s'),
                'sale' => $row->sale,
                'total_discount' => $row->total_discount,
                'total_tax' => $row->total_tax,
                'subtotal' => $row->subtotal,
                'total' => $row->total,
                'xml' => $row->xml,
                'pdf' => $row->pdf,
            ];
        });
        return $records;
    }
}
