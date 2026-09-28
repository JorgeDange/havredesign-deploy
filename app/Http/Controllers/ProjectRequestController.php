<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequestRequest;
use App\Mail\ProjectAckMail;
use App\Mail\ProjectReceivedMail;
use App\Models\Attachment;
use App\Models\ProjectRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectRequestController extends Controller
{
    public function store(StoreProjectRequestRequest $request): RedirectResponse
    {
        $project = ProjectRequest::create([
            'user_id' => auth()->id(),
            'user_name' => trim($request->string('user_name')->toString()),
            'user_email' => trim($request->string('user_email')->toString()),
            'user_phone' => trim($request->string('user_phone')->toString()),
            'address' => null,
            'location' => $request->filled('location') ? trim($request->string('location')->toString()) : null,
            'area_approx' => $request->filled('area_approx') ? trim($request->string('area_approx')->toString()) : null,
            'project_stage' => $request->filled('project_stage') ? $request->string('project_stage')->toString() : null,
            'service_id' => $request->filled('service_id') ? $request->string('service_id')->toString() : null,
            'solution_id' => $request->filled('solution_id') ? $request->string('solution_id')->toString() : null,
            'segment' => $request->filled('segment') ? $request->string('segment')->toString() : null,
            'preferred_channel' => $request->string('preferred_channel')->toString(),
            'preferred_time' => $request->filled('preferred_time') ? $request->string('preferred_time')->toString() : null,
            'project_type' => trim($request->string('project_type')->toString()),
            'budget' => $request->filled('budget') ? $request->string('budget')->toString() : null,
            'timeline' => $request->filled('timeline') ? $request->string('timeline')->toString() : null,
            'description' => $request->string('description')->toString(),
            'status' => 'NEW',
            'privacy_consented_at' => now(),
            'privacy_version' => (string) Setting::get('privacy.version', 'v1'),
            'ip_address' => $request->ip(),
        ]);

        $this->storeAttachments($request, $project);

        try {
            Mail::to(config('mail.admin.address'))->send(new ProjectReceivedMail($project));
        } catch (\Throwable $e) {
            Log::error('project.received_mail_failed', ['id' => $project->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($project->user_email)->send(new ProjectAckMail($project));
        } catch (\Throwable $e) {
            Log::error('project.ack_mail_failed', ['id' => $project->id, 'error' => $e->getMessage()]);
        }

        $resumo = [
            'ref' => Str::upper(Str::substr($project->id, 0, 8)),
            'project_type' => $project->project_type,
            'service' => $project->service?->title,
            'solution' => $project->solution?->name,
            'location' => $project->location,
            'budget' => $project->budget,
            'timeline' => $project->timeline,
            'user_name' => $project->user_name,
            'user_email' => $project->user_email,
        ];

        return redirect()
            ->route('solicitar-projeto')
            ->with('success', 'Solicitação enviada com sucesso! Vamos analisar o seu pedido e entraremos em contacto.')
            ->with('project_submitted', $resumo);
    }

    /**
     * Anexos: disco privado (storage/app/private), nome aleatório (backend.md 4.7).
     */
    private function storeAttachments(StoreProjectRequestRequest $request, ProjectRequest $project): void
    {
        foreach ($request->file('projectFiles', []) as $file) {
            $nome = Str::random(40).'.'.$file->extension();

            // disco 'local' => storage/app/private (nunca em public/).
            $file->storeAs('requests/'.$project->id, $nome, 'local');

            Attachment::create([
                'request_id' => $project->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => 'requests/'.$project->id.'/'.$nome,
                'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                'size_bytes' => $file->getSize() ?: 0,
            ]);
        }

        if ($request->hasFile('projectFiles') && ! Storage::disk('local')->exists('requests/'.$project->id)) {
            Log::warning('project.attachments_dir_missing', ['id' => $project->id]);
        }
    }
}
