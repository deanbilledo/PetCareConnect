<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AnalyticsExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Prepare data for export
        $rows = new Collection();
        
        // Add shop information
        $rows->push([
            'Shop Analytics Report',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        $rows->push([
            'Shop Name:',
            $this->data['shop_name'],
            '',
            'Shop Type:',
            $this->data['shop_type'],
            '',
            'Generated At:',
            $this->data['generated_at'],
            ''
        ]);
        
        $rows->push(['', '', '', '', '', '', '', '', '']);
        
        // Add summary section
        $rows->push([
            'Summary',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        $rows->push([
            'Total Appointments:',
            $this->data['summary']['total_appointments'],
            '',
            'Total Revenue:',
            $this->data['summary']['total_revenue'],
            '',
            'Total Customers:',
            $this->data['summary']['total_customers'],
            ''
        ]);
        
        $rows->push([
            'Active Services:',
            $this->data['summary']['active_services'],
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        $rows->push(['', '', '', '', '', '', '', '', '']);
        
        // Add monthly revenue
        $rows->push([
            'Monthly Revenue',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        $rows->push([
            'Month',
            'Amount (PHP)',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        foreach ($this->data['monthly_revenue'] as $monthData) {
            $rows->push([
                $monthData['month'],
                number_format($monthData['total'], 2),
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]);
        }
        
        $rows->push(['', '', '', '', '', '', '', '', '']);
        
        // Add paid appointments
        $rows->push([
            'Paid Appointments',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        
        // Skip to avoid duplicating headers
        
        // Add all appointments
        foreach ($this->data['paid_appointments'] as $appointment) {
            $rows->push([
                $appointment['customer'],
                $appointment['email'],
                $appointment['pet'],
                $appointment['service'],
                $appointment['employee'],
                $appointment['date'],
                $appointment['time'],
                $appointment['amount'],
                $appointment['paid_at']
            ]);
        }
        
        return $rows;
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Customer',
            'Email',
            'Pet',
            'Service',
            'Employee',
            'Date',
            'Time',
            'Amount',
            'Paid At'
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Shop Analytics Report';
    }
    
    /**
     * Apply styles to worksheet
     *
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the title and headers
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:I2')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A8:I8')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A9:B9')->getFont()->setBold(true);
        $sheet->getStyle('A12:I12')->getFont()->setBold(true)->setSize(12);
        
        return [
            // Style the headings row with bold text
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true, 'size' => 12]],
            8 => ['font' => ['bold' => true, 'size' => 12]],
            12 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
} 