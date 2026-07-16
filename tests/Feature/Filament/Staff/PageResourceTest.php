<?php

use App\Enums\PageStatus;
use App\Filament\Staff\Resources\Pages\Pages\CreatePage;
use App\Filament\Staff\Resources\Pages\Pages\EditPage;
use App\Filament\Staff\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->staff = User::factory()->staff()->create();

    actingAs($this->staff);
    Filament::setCurrentPanel('staff');
    Filament::bootCurrentPanel();
});

test('the resource pages load', function () {
    $page = Page::factory()->create();

    Livewire::test(ListPages::class)->assertOk();
    Livewire::test(CreatePage::class)->assertOk();
    Livewire::test(EditPage::class, ['record' => $page->id])->assertOk();
});

test('creating a page derives a unique slug and defaults to draft', function () {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Mentions légales',
            'slug' => 'mentions-legales',
            'status' => PageStatus::Draft->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Page::class, [
        'title' => 'Mentions légales',
        'slug' => 'mentions-legales',
        'status' => PageStatus::Draft->value,
    ]);
});

test('the slug must be unique', function () {
    Page::factory()->create(['slug' => 'a-propos']);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'À propos',
            'slug' => 'a-propos',
            'status' => PageStatus::Draft->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['slug']);
});

test('publishing a draft page sets status and timestamp', function () {
    $page = Page::factory()->create(['status' => PageStatus::Draft]);

    Livewire::test(ListPages::class)
        ->callTableAction('publish', $page);

    $page->refresh();
    expect($page->status)->toBe(PageStatus::Published)
        ->and($page->published_at)->not->toBeNull();
});
