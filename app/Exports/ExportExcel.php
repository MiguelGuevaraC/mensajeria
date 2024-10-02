<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportExcel implements FromCollection, WithStyles
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {  
        // Agregar los encabezados de la columna como la primera fila
        array_unshift($this->data, [
            'Grupo', 
            'Contacto', 
            'Concepto',
            'Monto', 
            'FechaReferencia',
            'FechaEnvio', 
            'Estado', 
            'Mensaje', // Asegúrate de que 'Mensaje' sea uno de los nuevos campos que necesitas
        ]);
    
        // Retornar los datos como una colección
        return collect($this->data);
    }
    

    public function headings(): array
    {
        return [

        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Establecer el título de la hoja
        $sheet->setTitle($this->startDate . ' | ' . $this->endDate);

        // Estilos para la tabla de resumen de ingresos
        $lastRow = $sheet->getHighestRow();
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:I' . $lastRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }

}
