<?php

namespace App\Filament\Pages;

use App\Services\WorkHoursReport;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\Response;

class GenerateRecap extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static ?string $navigationLabel = 'Générer récap';

    protected static ?string $title = 'Générer un récapitulatif';

    protected string $view = 'filament.pages.generate-recap';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from' => now()->startOfMonth(),
            'to' => now()->endOfMonth(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Période')
                    ->description('Compile les heures travaillées par employé sur la période choisie.')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('from')
                            ->label('Du')
                            ->required(),
                        DatePicker::make('to')
                            ->label('Au')
                            ->afterOrEqual('from')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Générer le PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->action('generate'),
        ];
    }

    public function generate(): Response
    {
        $data = $this->form->getState();

        $company = Filament::getTenant();
        $from = Carbon::parse($data['from'])->startOfDay();
        $to = Carbon::parse($data['to'])->endOfDay();

        $report = new WorkHoursReport($company, $from, $to);

        $pdf = Pdf::loadView('recap.pdf', [
            'company' => $company,
            'from' => $from,
            'to' => $to,
            'rows' => $report->perEmployee()->all(),
            'totalMinutes' => $report->totalMinutes(),
            'generatedAt' => now(),
        ]);

        return $pdf->download('recap-'.$from->format('Y-m-d').'.pdf');
    }
}
