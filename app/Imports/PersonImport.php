<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\ContactByGroup;
use App\Models\GroupSend;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PersonImport implements ToModel, WithHeadingRow
{
    private $headerMap = [];
    private $groupId; // Añadido para almacenar el ID del grupo

    // Constructor para recibir el ID del grupo
    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function headingRow(): int
    {
        return 0; // Indica que la primera fila es la fila de encabezado
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Definir los encabezados esperados
            $expectedColumns = [
                'DOCUMENTO' => 'document',
                'NOMBRES' => 'names',
                'TELEFONO' => 'telephone',
                'DIRECCION' => 'address',
                'CONCEPTO' => 'concept',
                'MONTO' => 'amount',
                'FECHA_REFERENCIA' => 'date_reference',
            ];

            // Si el mapeo de encabezados está vacío, estamos en la fila de encabezado
            if (empty($this->headerMap)) {
                foreach ($row as $key => $value) {
                    if (!empty($value)) {
                        $normalizedKey = strtoupper(str_replace(' ', '', $value)); // Convertir a mayúsculas
                        if (array_key_exists($normalizedKey, $expectedColumns)) {
                            $this->headerMap[$expectedColumns[$normalizedKey]] = $key;
                        }
                    }
                }
                return null; // Retornar null porque esta fila no contiene datos de contacto
            }

            // Crear un array con los datos normalizados
            $normalizedRow = [];
            foreach ($this->headerMap as $columnName => $key) {
                $normalizedRow[$columnName] = isset($row[$key]) ? trim($row[$key]) : null;
            }

            // Limpiar el número de teléfono
            $phoneFields = [
                'telefono',
                'telephone',
                'telefono1',
                'telefono2',
                'telefono3',
                'telefono4',
            ];
            $cleanedPhoneNumber = $normalizedRow['telephone'];
            if (!$this->isValidPhoneNumber($cleanedPhoneNumber)) {
                $cleanedPhoneNumber = $this->cleanPhoneNumber($normalizedRow, $phoneFields);
            }

            // Convertir la fecha de Excel a formato Y-m-d (año-mes-día)
            $dateReference = $normalizedRow['date_reference'];

            if (empty($dateReference)) {
                $dateReference = null; // Asigna null si está vacío
            } elseif (is_numeric($dateReference)) {
                $dateReference = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateReference)->format('Y-m-d');
            } else {
                // En caso de que la fecha no sea un número, mantenerla tal como está
                $dateReference = date('Y-m-d', strtotime($dateReference));
            }
            

            $currentGroup = GroupSend::find($this->groupId);
            if (!$currentGroup) {
                Log::error('no se encoentró el grupo');
            }

            // Verificar si el teléfono ya está en el grupo
            $existingContact = Contact::whereHas('groupSend',
             function ($query) use ($currentGroup) {
                $query->where('groupSend_id', $currentGroup->id);
            })->where('telephone', $cleanedPhoneNumber)->first();

            if ($existingContact != null) {
                // Si el teléfono ya existe en el grupo, actualizar el contacto
                $existingContact->update([
                    'documentNumber' => $normalizedRow['document'],
                    'names' => $normalizedRow['names'],
                    'address' => $normalizedRow['address'],
                    'concept' => $normalizedRow['concept'],
                    'amount' => $normalizedRow['amount'],
                    'dateReference' => $dateReference,
                    'state' => 1,
                    'groupSend_id' => $currentGroup->id, // Asegurar que el grupo es actualizado
                ]);
                $existingContact->updateDetailContactData();
                ContactByGroup::updateOrCreate(
                    ['contact_id' => $existingContact->id, 'groupSend_id' => $currentGroup->id],
                    ['state' => 1]
                );

                return $existingContact; // Retornar el contacto actualizado
            } else {
                // Si el teléfono no existe en el grupo, crear un nuevo contacto y asociarlo al grupo
                try {
                    $newContact = Contact::create([
                        'telephone' => $cleanedPhoneNumber,
                        'documentNumber' => $normalizedRow['document'],
                        'names' => $normalizedRow['names'],
                        'address' => $normalizedRow['address'],
                        'concept' => $normalizedRow['concept'],
                        'amount' => $normalizedRow['amount'],
                        'dateReference' => $dateReference,
                        'groupSend_id' => $currentGroup->id,
                        'state' => 1,
                    ]);
                    $contactByGroup = ContactByGroup::create([
                        'state' => 1,
                        'groupSend_id' => $currentGroup->id,
                        'contact_id' => $newContact->id,
                    ]);

                    // Registrar la creación del nuevo contacto
                    Log::info('Nuevo contacto creado:', $newContact->toArray());

                } catch (Exception $e) {
                    // Registrar el error y lanzar una excepción
                    Log::error('Error al crear el contacto: ' . $e->getMessage());

                }

                // Asociar el contacto al grupo
                // $newContact->groups()->attach($currentGroup->id);

                return $newContact; // Retornar el contacto recién creado
            }

        } catch (Exception $e) {
            // Registrar el error y lanzar una excepción
            Log::error('IMPORTACION: ' . $e->getMessage());
            throw new HttpException(500, 'Error processing the Excel file: ' . $e->getMessage());
        }
    }

    private function cleanPhoneNumber(array $row, array $fields): ?string
    {
        // Variable para almacenar el primer número válido encontrado
        $firstValidNumber = null;

        foreach ($fields as $field) {
            if (isset($row[$field]) && !empty($row[$field])) {
                // Eliminar caracteres no numéricos y espacios
                $cleanedString = preg_replace('/[^0-9]/', '', $row[$field]);

                // Buscar todos los números de 9 dígitos
                preg_match_all('/\d{9}/', $cleanedString, $matches);

                // Verificar si se encontró algún número de 9 dígitos
                if (!empty($matches[0])) {
                    $firstValidNumber = $matches[0][0]; // Guardar el primer número de 9 dígitos encontrado
                    break; // Salir del bucle una vez que se encuentra un número válido
                }
            }
        }

        return $firstValidNumber; // Retornar el primer número válido o null si no se encontró ninguno
    }

    public function isValidPhoneNumber($string)
    {
        $length = strlen($string);

        $isOnlyDigits = ctype_digit($string);

        return $length === 9 && $isOnlyDigits;
    }

}
