<?php

namespace App\Livewire;

use App\Enums\CredentialType;
use App\Enums\PunchDirection;
use App\Exceptions\UnknownCredentialException;
use App\Models\PunchDevice;
use App\Services\PunchService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kiosk')]
class Kiosk extends Component
{
    public PunchDevice $device;

    public string $type = 'nfc';

    public string $identifier = '';

    /** @var array{ok: bool, message: string}|null */
    public ?array $feedback = null;

    public function mount(PunchDevice $device): void
    {
        abort_unless($device->is_active, 404);

        $this->device = $device;
    }

    public function punch(PunchService $service): void
    {
        $this->validate([
            'type' => ['required', 'in:nfc,qr,pin'],
            'identifier' => ['required', 'string'],
        ]);

        try {
            $punch = $service->record($this->device, CredentialType::from($this->type), trim($this->identifier));

            $name = trim($punch->employee->first_name.' '.$punch->employee->last_name);
            $verb = $punch->direction === PunchDirection::In ? 'Entrée' : 'Sortie';

            $this->feedback = [
                'ok' => true,
                'message' => "Bonjour {$name} — {$verb} enregistrée à ".$punch->punched_at->format('H:i'),
            ];
        } catch (UnknownCredentialException $e) {
            $this->feedback = ['ok' => false, 'message' => $e->getMessage()];
        }

        $this->reset('identifier');
    }

    public function render()
    {
        return view('livewire.kiosk');
    }
}
