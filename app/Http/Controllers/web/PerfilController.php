<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('ensureTokenIsValid');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $typeUser = $user->typeUser;

        $accesses = $typeUser->getAccess($typeUser->id);

        $currentRoute = $request->path();
        $currentRouteParts = explode('/', $currentRoute);
        $lastPart = end($currentRouteParts);

        if (in_array($lastPart, $accesses)) {
            $groupMenu = GroupMenu::getFilteredGroupMenusSuperior($user->typeofUser_id);
            $groupMenuLeft = GroupMenu::getFilteredGroupMenus($user->typeofUser_id);

            return view('Modulos.Perfil.index', compact('user', 'groupMenu', 'groupMenuLeft'));
        } else {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
