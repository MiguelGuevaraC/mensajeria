<!DOCTYPE html>
<html>

<head>
    <title>Exportación PDF</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Servicio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <style>
        * {
            margin: 10;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            text-align: center;
        }

        body {
            padding-top: 5px;
            padding-bottom: 5px;

        }

        td,
        th {
            padding: 2px;
        }

        .headerImage {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .footerImage {
            position: absolute;
            bottom: 0;
            left: 0;
            width: auto;
            height: auto;
            max-width: 100%;
        }

        .content {
            margin-top: 30px;
            padding-left: 30px;
            padding-right: 30px;
        }

        .contentImage {
            width: 100%;
            text-align: right;
        }

        .logoImage {
            width: auto;
            height: 60px;
        }

        .titlePresupuesto {
            font-size: 18px;
            font-weight: bolder;
            text-align: center;
            margin-top: 1px;
            /*margin-bottom: 20px;*/
            color: #100046;
            ;
        }



        table {

            width: 95%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .tableInfo {

            margin-top: 1px;

        }

        .tablePeople {
            margin-top: 5px;
            font-size: 1px;
            border: 1px solid black;
        }

        .tablePeople td,
        .tablePeople th {
            border: 1px solid black;
        }

        .tablePeople th {
            background-color: black;
            color: white;
            text-align: left;

        }

        .tableDetail {
            margin-top: 5px;
            border-collapse: collapse;
        }

        .p10 {
            padding: 10px;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }
        .font-8 {
            font-size: 8px;
        }
        .font-10 {
            font-size: 10px;
        }
        .font-12 {
            font-size: 12px;
        }

        .font-14 {
            font-size: 14px;
        }

        .font-16 {
            font-size: 16px;
        }

        .margin20 {
            margin-top: 10px;
        }

        .bolder {
            font-weight: bolder;
        }

        .tablePeople td.left {
            padding: 2px;
        }

        .tablePeople td.right {
            padding: 2px;
        }

        .tableDetail th {
            background-color: #100046;
            color: white;
            padding: 10px;
            font-weight: bolder;
            border: 1px solid #ddd;
        }

        .tableDetail td,
 {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;

        }

        .id {

            text-align: center;
        }

        .description {
            width: 50%;
        }


        .w50 {
            width: 50%;
        }

        .w40 {
            width: 40%;
        }

        .w20 {
            width: 20%;
        }


        .borderTop {
            border-top: 1px solid #3b3b3b;
        }

        .text-sm {
            font-size: 9px;
        }

        .w40 {
            width: 40%;
        }

        .w25 {
            width: 25%;
        }

        .w10 {
            width: 10%;
        }

        .w30 {
            width: 30%;
        }
        .justify{
            text-align: justify;
        }
    </style>
</head>

<body>

    <table class="tableInfo">
        <tr>
            <td class="">
                <div class="titlePresupuesto">REPORTE DE MENSAJERÍA ({{ $dateStart }} | {{ $dateEnd }})</div>
            </td>
        </tr>

    </table>
    <table class="tableDetail font-12">
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Contacto</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Fecha Referencia</th>
                <th>Fecha Envío</th>
                <th>User</th>
                <th>Estado</th>
                <th>Mensaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $moviment)
                <tr>
                    <td class="font-8">{{ $moviment->contact->group->name ?? 'N/A' }}</td> <!-- Grupo -->
                    <td class="font-8">
                        {{ implode(' | ', array_filter([$moviment->namesPerson, $moviment->documentNumber, $moviment->telephone, $moviment->address])) }}
                    </td>
                    <!-- Contacto -->
                    <td class="font-8">{{ $moviment->concept ?? '-' }}</td> <!-- Concepto -->
                    <td class="font-8">{{ $moviment->amount ?? '' }}</td> <!-- Monto -->
                    <td class="font-8">{{ $moviment->contact->concept ?? '-' }}</td> <!-- FechaReferencia -->
                    <td class="font-8">{{ $moviment->created_at ? \Carbon\Carbon::parse($moviment->created_at)->format('Y-m-d H:i:s') : '-' }}</td> <!-- FechaEnvio -->
                    <td class="font-8">{{ $moviment->user->username ?? '-' }}</td> <!-- Estado -->
                    <td class="font-8">{{ $moviment->status ?? '-' }}</td> <!-- Estado -->
                    <td class="font-8 justify">{{ $moviment->messageSend ?? '-' }}</td> <!-- Mensaje -->
                </tr>
            @endforeach
        </tbody>
    </table>
    




</body>

</html>
