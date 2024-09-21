<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\WhatsappSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    public function index()
    {
        $user = Auth::user();

        $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
        $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

        return view('Modulos.Dashboard.index', compact('user', 'groupMenu', 'groupMenuLeft'));
    }

    public function dataDashboard(Request $request)
    {
        $fechaInicio = $request->input('fechaStart', now()->startOfYear()->format('Y-m-d'));
        $fechaFin = $request->input('fechaEnd', now()->format('Y-m-d'));
    
        // Verificar que la fecha de fin no sea anterior a la fecha de inicio
        if ($fechaFin < $fechaInicio) {
            return response()->json(['error' => 'La fecha de fin no puede ser anterior a la fecha de inicio.'], 400);
        }
    
        $user = Auth::user();
        // Obtener el costo unitario
        $costoUnitario = $user->company->costSend ?? 0;
    
        // Filtrar datos por el rango de fechas
        $mensajes = WhatsappSend::where('created_at', '>=', $fechaInicio)
            ->where('created_at', '<=', $fechaFin)
            ->orderBy('created_at', 'asc')
            ->get();
    
        // Agrupar los mensajes por fecha
        $mensajesPorFecha = $mensajes->groupBy(fn($item) => $item->created_at->format('Y-m-d'));
    
        // Calcular el costo total a partir del campo costSend en WhatsappSend
        $costoTotal = $mensajes->sum('costSend');
    
        // Calcular los datos agrupados
        $data = [
            'totalMensajes' => $mensajes->count(),
            'costoUnitario' => $costoUnitario, // Costo unitario
            'costoTotal' => $costoTotal, // Costo total sumando costSend de cada mensaje
            'mensajesPorFecha' => $mensajesPorFecha->map->count(), // Conteo de mensajes por fecha
            'costosPorFecha' => $mensajesPorFecha->map(fn($group) => $group->sum('costSend')), // Costo total por fecha
        ];
    
        return response()->json($data);
    }
    

}
