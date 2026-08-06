<?php

namespace App\Services;

use Mpdf\Mpdf;

class ReportCardPdfService
{
    public function render(array $data): Mpdf
    {
        $mpdf = new Mpdf([
            'mode'   => 'utf-8',
            'format' => 'A4-P',
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML(view('pdf.report_card', $data)->render());

        return $mpdf;
    }
}
