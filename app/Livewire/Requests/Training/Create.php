<?php

namespace App\Livewire\Requests\Training;

use App\Infra\Others\City;
use App\Infra\Others\Instituition;
use App\Infra\Others\TrainingType;
use App\Models\TrainingRequest;
use App\Notifications\NewTrainingRequestNotification;
use App\Notifications\TrainingRequestReceivedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class Create extends Component
{
    use WithFileUploads;

    public bool $termsAccepted = false;

    public string $request_scope = TrainingRequest::SCOPE_STATE;

    public ?string $institution_name = null;
    public string $holder_name = '';
    public string $holder_role = '';

    public string $requester_name = '';
    public string $requester_role = '';
    public string $requester_phone = '';
    public string $requester_email = '';

    public ?string $event_type = null;
    public ?int $participants_count = null;

    public ?string $training_type = null;
    public string $class_type = 'ABERTA'; // ABERTA | EXCLUSIVA
    public string $target_audience = '';
    public ?string $leaders_participation = null; // SIM | NAO

    public string $objectives = '';
    public string $content_expectation = '';

    public $request_letter;

    public ?string $successProtocol = null;

    public function mount(?string $scope = null): void
    {
        $this->request_scope = $this->normalizeScope($scope);
    }

    protected function rules(): array
    {
        return [
            'termsAccepted' => ['accepted'],
            'request_scope' => ['required', Rule::in([TrainingRequest::SCOPE_STATE, TrainingRequest::SCOPE_MUNICIPALITY])],

            'institution_name' => ['required', 'string', 'max:255'],
            'holder_name' => ['required', 'string', 'max:255'],
            'holder_role' => ['required', 'string', 'max:255'],

            'requester_name' => ['required', 'string', 'max:255'],
            'requester_role' => ['required', 'string', 'max:255'],
            'requester_phone' => ['required', 'string', 'max:30'],
            'requester_email' => ['required', 'email', 'max:255'],

            'event_type' => ['required', 'in:CURSO,PALESTRA,SEMINARIO,OFICINA,CONGRESSO,WORKSHOP,ENCONTRO,RODA DE CONVERSA'],
            'participants_count' => ['required', 'integer', 'min:1', 'max:100000'],

            'training_type' => ['required', 'string', 'max:255'],
            'class_type' => ['required', 'in:ABERTA,EXCLUSIVA'],
            'target_audience' => ['required', 'string', 'min:5'],

            'leaders_participation' => ['required', 'in:SIM,NAO'],

            'objectives' => ['required', 'string', 'min:5'],
            'content_expectation' => ['required', 'string', 'min:5'],

            'request_letter' => [
                'required',
                'file',
                'max:5120',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'termsAccepted.accepted' => 'Você precisa marcar “Li e concordo” para enviar a solicitação.',

            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'Informe um e-mail válido.',
            'max' => 'O campo :attribute deve ter no máximo :max.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'in' => 'O campo :attribute possui um valor inválido.',

            'request_letter.file' => 'O arquivo do ofício é inválido.',
            'request_letter.max' => 'O ofício deve ter no máximo 5MB.',
            'request_letter.mimes' => 'O ofício deve ser PDF, JPG, PNG, DOC ou DOCX.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'termsAccepted' => 'termo de aceite',
            'request_scope' => 'tipo da solicitação',

            'institution_name' => $this->isMunicipalityScope() ? 'nome do município' : 'nome do órgão/secretaria',
            'holder_name' => 'nome do titular da instituição',
            'holder_role' => 'cargo do titular da instituição',

            'requester_name' => 'responsável pela solicitação',
            'requester_role' => 'cargo do responsável',
            'requester_phone' => 'telefone',
            'requester_email' => 'e-mail',

            'event_type' => 'tipo de evento',
            'participants_count' => 'quantidade de participantes',

            'training_type' => 'tipo de curso/formação',
            'class_type' => 'turma',
            'target_audience' => 'público participante',
            'leaders_participation' => 'participação de líderes na formação',

            'objectives' => 'objetivos da formação',
            'content_expectation' => 'expectativa de conteúdo',

            'request_letter' => 'ofício de solicitação',
        ];
    }

    public function submit(): void
    {
        $this->request_scope = $this->normalizeScope($this->request_scope);

        $this->validate();

        $protocol = $this->generateProtocol();

        $ext = strtolower($this->request_letter->getClientOriginalExtension() ?: 'pdf');
        $filename = $protocol . '-oficio.' . $ext;

        $path = $this->request_letter->storeAs(
            'training-requests/' . $protocol,
            $filename,
            'public'
        );

        $leaders = $this->leaders_participation === 'SIM';

        $request = TrainingRequest::create([
            'protocol' => $protocol,
            'request_scope' => $this->request_scope,
            'institution_name' => $this->institution_name,
            'holder_name' => $this->holder_name,
            'holder_role' => $this->holder_role,
            'requester_name' => $this->requester_name,
            'requester_role' => $this->requester_role,
            'requester_phone' => $this->requester_phone,
            'requester_email' => $this->requester_email,

            'event_type' => $this->event_type,
            'participants_count' => $this->participants_count,

            'training_type' => $this->training_type,
            'class_type' => $this->class_type,
            'target_audience' => $this->target_audience,
            'leaders_participation' => $leaders,
            'objectives' => $this->objectives,
            'content_expectation' => $this->content_expectation,
            'request_letter_path' => $path,
            'status' => 'nao_iniciado',
            'terms_accepted' => true,
        ]);

        $adminRecipients = $this->resolveTrainingNotificationRecipients($request->request_scope);

        try {
            Notification::route('mail', $adminRecipients)
                ->notify(new NewTrainingRequestNotification($request));
        } catch (Throwable $e) {
            Log::error('Falha ao enviar e-mail administrativo de solicitação de curso.', [
                'training_request_id' => $request->id,
                'protocol' => $request->protocol,
                'request_scope' => $request->request_scope,
                'notify_emails' => $adminRecipients,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Notification::route('mail', $request->requester_email)
                ->notify(new TrainingRequestReceivedNotification($request));
        } catch (Throwable $e) {
            Log::error('Falha ao enviar e-mail de confirmação ao solicitante de curso.', [
                'training_request_id' => $request->id,
                'protocol' => $request->protocol,
                'requester_email' => $request->requester_email,
                'error' => $e->getMessage(),
            ]);
        }

        $this->successProtocol = $protocol;

        $this->dispatch('training-request-success', protocol: $protocol);

        $this->reset([
            'institution_name',
            'holder_name',
            'holder_role',
            'requester_name',
            'requester_role',
            'requester_phone',
            'requester_email',
            'event_type',
            'participants_count',
            'training_type',
            'class_type',
            'target_audience',
            'leaders_participation',
            'objectives',
            'content_expectation',
            'request_letter',
        ]);

        $this->termsAccepted = true;
    }

    public function render()
    {
        return view('livewire.requests.training.create', [
            'institutions' => $this->resolveInstitutionOptions(),
            'trainingTypes' => TrainingType::options(),
            'eventTypes' => [
                'CURSO' => 'Curso',
                'PALESTRA' => 'Palestra',
                'SEMINARIO' => 'Seminário',
                'OFICINA' => 'Oficina',
                'CONGRESSO' => 'Congresso',
                'WORKSHOP' => 'Workshop',
                'ENCONTRO' => 'Encontro',
                'RODA DE CONVERSA' => 'Roda de Conversa',
            ],
            'requestScopeLabel' => TrainingRequest::scopeLabel($this->request_scope),
            'institutionFieldLabel' => $this->getInstitutionFieldLabel(),
            'institutionPlaceholder' => $this->getInstitutionPlaceholder(),
            'institutionContextLabel' => $this->isMunicipalityScope() ? 'município solicitante' : 'órgão solicitante',
            'orientationIntro' => $this->isMunicipalityScope()
                ? 'A Escola de Gestão Pública do Estado do Ceará disponibiliza este formulário para formalizar demandas de formação dos municípios cearenses.'
                : 'A Escola de Gestão Pública do Estado do Ceará disponibiliza este formulário para formalizar demandas de formação dos órgãos estaduais, conforme a Portaria EGP nº 057/2022.',
        ])->layout('components.layouts.app');
    }

    private function generateProtocol(): string
    {
        $year = now()->format('Y');
        $prefix = $year;

        return DB::transaction(function () use ($prefix) {
            $last = TrainingRequest::query()
                ->where('protocol', 'like', $prefix . '%')
                ->orderByDesc('protocol')
                ->lockForUpdate()
                ->first();

            if (! $last) {
                return $prefix . '0001';
            }

            $seq = (int) substr($last->protocol, 4);
            $next = $seq + 1;

            return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        });
    }

    private function normalizeScope(?string $scope): string
    {
        return $scope === TrainingRequest::SCOPE_MUNICIPALITY
            ? TrainingRequest::SCOPE_MUNICIPALITY
            : TrainingRequest::SCOPE_STATE;
    }

    private function isMunicipalityScope(): bool
    {
        return $this->request_scope === TrainingRequest::SCOPE_MUNICIPALITY;
    }

    private function resolveInstitutionOptions(): array
    {
        return $this->isMunicipalityScope()
            ? City::options()
            : Instituition::options();
    }

    private function getInstitutionFieldLabel(): string
    {
        return $this->isMunicipalityScope()
            ? 'Nome do município *'
            : 'Nome do órgão/secretaria *';
    }

    private function getInstitutionPlaceholder(): string
    {
        return $this->isMunicipalityScope()
            ? 'Selecione seu município'
            : 'Selecione seu órgão';
    }

    private function resolveTrainingNotificationRecipients(string $scope): array
    {
        $configuredRecipients = config('requests.training_notify_emails.' . $scope, []);

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
