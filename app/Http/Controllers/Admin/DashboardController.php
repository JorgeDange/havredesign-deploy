<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use App\Models\ProjectRequest;
use App\Models\Service;
use App\Models\Solution;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoje = now()->toDateString();

        return view('admin.dashboard', [
            'contagens' => [
                'servicos' => Service::count(),
                'portfolio' => PortfolioItem::count(),
                'solucoes' => Solution::count(),
                'pedidosNovos' => ProjectRequest::where('status', 'NEW')->count(),
                'agendamentosPendentes' => Appointment::where('status', 'PENDING')
                    ->whereDate('appt_date', '>=', $hoje)->count(),
                'mensagensNovas' => ContactMessage::where('status', 'new')->count(),
                'testemunhosOcultos' => Testimonial::where('status', 'hidden')->count(),
                'utilizadores' => User::count(),
            ],
            'pedidosRecentes' => ProjectRequest::latest()->limit(5)->get(),
            'agendamentosProximos' => Appointment::whereDate('appt_date', '>=', $hoje)
                ->orderBy('appt_date')->orderBy('appt_time')->limit(6)->get(),
            'mensagensRecentes' => ContactMessage::latest()->limit(5)->get(),
        ]);
    }
}
