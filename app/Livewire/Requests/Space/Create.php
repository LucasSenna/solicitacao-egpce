<?php

namespace App\Livewire\Requests\Space;

use App\Infra\Others\Instituition;
use App\Infra\Spaces as SpacesInfra;
use App\Models\SpaceRequest;
use App\Notifications\NewSpaceRequestNotification;
use App\Notifications\SpaceRequestReceivedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class Create extends Component
{
    use WithFileUploads;

    public bool $termsAccepted = false;

    public string $event_title = '';
    public string $objective = '';
    public string $target_audience = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $time_slot = '';
    public ?int $participants_quantity = null;

    public string $institution_name = '';
    public string $institution_other = '';

    public string $responsible_name = '';
    public string $responsible_role = '';
    public string $responsible_email = '';
    public string $responsible_phone = '';

    public ?string $general_notes = null;

    public array $selectedSpaces = [];
    public array $spacesOptions = [];
    public $responsibility_term;
    public array $institutions = [];

    public function mount(): void
    {
        $list = Instituition::get();
        $this->institutions = array_combine($list, $list) ?: [];
        ksort($this->institutions);

        $this->spacesOptions = SpacesInfra::items();
        usort($this->spacesOptions, fn ($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
    }

    protected function rules(): array
    {
        $keysString = implode(',', SpacesInfra::keys());

        return [
            'termsAccepted' => ['accepted'],
            'event_title' => ['required', 'string', 'max:255'],
            'objective' => ['required', 'string'],
            'target_audience' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'time_slot' => ['required', 'in:manha,tarde,manha_tarde'],
            'participants_quantity' => ['required', 'integer', 'min:1'],
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_other' => ['required_if:institution_name,outro', 'nullable', 'string', 'min:3', 'max:255'],
            'responsible_name' => ['required', 'string', 'max:255'],
            'responsible_role' => ['required', 'string', 'max:255'],
            'responsible_email' => ['required', 'email', 'max:255'],
            'responsible_phone' => ['required', 'string', 'max:30'],
            'general_notes' => ['nullable', 'string'],
            'selectedSpaces' => ['required', 'array', 'min:1'],
            'selectedSpaces.*' => ['string', 'in:' . $keysString],
            'responsibility_term' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    protected function messages(): array
    {
        return [
            'termsAccepted.accepted' => 'Você precisa marcar “Li e concordo” para enviar a solicitação.',
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'Informe um e-mail válido.',
            'max' => 'O campo :attribute deve ter no máximo :max.',
            'min' => 'O campo :attribute deve ter no mínimo :min.',
            'in' => 'O campo :attribute possui um valor inválido.',
            'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior ao período inicial.',
            'required_if' => 'O campo :attribute é obrigatório.',
            'responsibility_term.file' => 'O arquivo do termo é inválido.',
            'responsibility_term.max' => 'O termo deve ter no máximo 5MB.',
            'responsibility_term.mimes' => 'O termo deve ser PDF, JPG, PNG, DOC ou DOCX.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'termsAccepted' => 'termo de aceite',
            'event_title' => 'título da formação',
            'objective' => 'objetivos da formação',
            'target_audience' => 'público participante',
            'start_date' => 'período inicial',
            'end_date' => 'período final',
            'time_slot' => 'horário',
            'participants_quantity' => 'quantidade de participantes',
            'institution_name' => 'nome do órgão/secretaria',
            'institution_other' => 'nome do órgão/secretaria',
            'responsible_name' => 'responsável pela solicitação',
            'responsible_role' => 'cargo',
            'responsible_email' => 'email',
            'responsible_phone' => 'telefone',
            'selectedSpaces' => 'espaços',
            'selectedSpaces.*' => 'espaços',
            'responsibility_term' => 'termo de responsabilidade',
        ];
    }

    public function updatedInstitutionName($value): void
    {
        if ($value !== 'outro') {
            $this->institution_other = '';
        }
    }

    public function submit(): void
    {
        $this->validate();

        $finalInstitutionName = $this->institution_name === 'outro'
            ? trim($this->institution_other)
            : ($this->institutions[$this->institution_name] ?? $this->institution_name);

        $ext = strtolower($this->responsibility_term->getClientOriginalExtension() ?: 'pdf');
        $filename = 'termo-responsabilidade-' . now()->format('YmdHis') . '-' . uniqid() . '.' . $ext;

        $path = $this->responsibility_term->storeAs(
            'space-requests/terms',
            $filename,
            'public'
        );

        $snapshot = array_values(array_filter(
            SpacesInfra::items(),
            fn ($space) => in_array($space['key'], $this->selectedSpaces, true)
        ));

        $request = SpaceRequest::create([
            'event_title' => $this->event_title,
            'objective' => $this->objective,
            'target_audience' => $this->target_audience,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'time_slot' => $this->time_slot,
            'participants_quantity' => $this->participants_quantity,
            'institution_name' => $finalInstitutionName,
            'responsible_name' => $this->responsible_name,
            'responsible_role' => $this->responsible_role,
            'responsible_email' => $this->responsible_email,
            'responsible_phone' => $this->responsible_phone,
            'general_notes' => $this->general_notes,
            'selected_spaces' => $this->selectedSpaces,
            'selected_spaces_snapshot' => $snapshot,
            'accepted_terms_at' => now(),
            'responsibility_term_path' => $path,
            'status' => 'pendente',
        ]);

        $adminRecipients = $this->resolveSpaceNotificationRecipients();

        try {
            Notification::route('mail', $adminRecipients)
                ->notify(new NewSpaceRequestNotification($request));
        } catch (Throwable $e) {
            Log::error('Falha ao enviar e-mail administrativo de solicitação de cessão de espaço.', [
                'space_request_id' => $request->id,
                'notify_emails' => $adminRecipients,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Notification::route('mail', $request->responsible_email)
                ->notify(new SpaceRequestReceivedNotification($request));
        } catch (Throwable $e) {
            Log::error('Falha ao enviar e-mail de confirmação ao solicitante de cessão de espaço.', [
                'space_request_id' => $request->id,
                'responsible_email' => $request->responsible_email,
                'error' => $e->getMessage(),
            ]);
        }

        $this->dispatch('space-request-success');

        $this->reset([
            'event_title',
            'objective',
            'target_audience',
            'start_date',
            'end_date',
            'time_slot',
            'participants_quantity',
            'institution_name',
            'institution_other',
            'responsible_name',
            'responsible_role',
            'responsible_email',
            'responsible_phone',
            'general_notes',
            'selectedSpaces',
            'responsibility_term',
        ]);

        $this->termsAccepted = true;
    }

    public function render()
    {
        return view('livewire.requests.space.create', [
            'institutions' => $this->institutions,
            'spacesOptions' => $this->spacesOptions,
        ])->layout('components.layouts.app');
    }

    private function resolveSpaceNotificationRecipients(): array
    {
        $configuredRecipients = config('requests.space_notify_emails', []);

        if (! is_array($configuredRecipients)) {
            $configuredRecipients = [$configuredRecipients];
        }

        $filteredRecipients = array_values(array_unique(array_filter(
            $configuredRecipients,
            fn ($email): bool => is_string($email) && filled($email)
        )));

        if (! empty($filteredRecipients)) {
            return $filteredRecipients;
        }

        $fallback = config('requests.notify_email');

        return filled($fallback) ? [$fallback] : [];
    }
}
