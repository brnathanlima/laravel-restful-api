<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponser;

    public function allowedAdminAction()
    {
        if (Gate::denies('admin-action')) {
            throw new AuthorizationException('This action is unauthorized');
        }
    }
}
