<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\SitePages\Pages\EditSitePage;
use App\Models\ContactMessage;
use App\Models\Profile;
use App\Models\SitePage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class SectionContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function consultation(): array
    {
        return ['name' => 'Visitante', 'email' => 'visitante@example.test', 'subject' => 'Taller de arte', 'message' => 'Quisiera conversar sobre una propuesta.', 'consent' => '1', 'website' => ''];
    }

    public function test_existing_pages_load_new_fields_without_erasing_saved_copy(): void
    {
        $this->admin();
        $page = SitePage::where('key', 'teaching')->firstOrFail();
        $page->update(['content' => ['heading' => 'Un título personalizado']]);
        Livewire::test(EditSitePage::class, ['record' => $page->id])->assertFormSet(['content.heading' => 'Un título personalizado', 'content.pillar_1_heading' => 'Observar'])->call('save')->assertHasNoFormErrors();
        $this->get('/docencia')->assertInertia(fn (Assert $view) => $view->where('site.teaching.heading', 'Un título personalizado')->where('site.teaching.pillar_1_heading', 'Observar'));
    }

    public function test_profile_editor_publishes_real_trajectory_and_personal_teaching_statement(): void
    {
        $this->admin();
        $profile = Profile::create(['name' => 'Romina', 'intro' => 'Presentación', 'bio' => 'Biografía', 'role' => 'Profesora', 'brand_subtitle' => 'ARTES VISUALES', 'footer_text' => 'Arte y encuentro']);
        Livewire::test(EditProfile::class, ['record' => $profile->id])->fillForm([
            'teaching_statement' => 'Una presentación docente personalizada.',
            'trajectory' => [['period' => '2024', 'category' => 'Formación', 'title' => 'Actividad de prueba', 'description' => 'Entrada ficticia únicamente para la prueba.']],
        ])->call('save')->assertHasNoFormErrors();
        $this->get('/sobre-mi')->assertInertia(fn (Assert $view) => $view->where('profile.trajectory.0.title', 'Actividad de prueba'));
        $this->get('/docencia')->assertInertia(fn (Assert $view) => $view->where('profile.teaching_statement', 'Una presentación docente personalizada.'));
    }

    public function test_public_contact_stores_consultation_privately_and_ignores_extra_fields(): void
    {
        $this->from('/contacto')->post('/contacto', [...$this->consultation(), 'status' => 'answered', 'is_admin' => true])->assertRedirect('/contacto')->assertSessionHas('contact_received', true);
        $this->assertDatabaseHas('contact_messages', ['email' => 'visitante@example.test', 'status' => 'new', 'subject' => 'Taller de arte']);
        $this->get('/contacto')->assertInertia(fn (Assert $view) => $view->where('contactReceived', true)->missing('messages'));
        $this->get('/admin/contact-messages')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/contact-messages')->assertForbidden();
    }

    public function test_contact_rejects_incomplete_and_invalid_input_without_storing_it(): void
    {
        $this->post('/contacto', [])->assertSessionHasErrors(['name', 'email', 'subject', 'message', 'consent']);
        $this->post('/contacto', [...$this->consultation(), 'email' => 'incorrecto', 'message' => 'Hola', 'consent' => '0'])->assertSessionHasErrors([
            'email' => 'Ingresá un correo válido.',
            'message' => 'Contanos un poco más: el mensaje debe tener al menos 10 caracteres.',
            'consent' => 'Necesitamos tu autorización para guardar y gestionar esta consulta.',
        ]);
        $this->post('/contacto', [...$this->consultation(), 'website' => 'https://spam.example'])->assertSessionHasErrors(['website']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_limits_repeated_submissions(): void
    {
        for ($index = 0; $index < 5; $index++) {
            $this->post('/contacto', $this->consultation())->assertRedirect('/contacto');
        }
        $this->post('/contacto', $this->consultation())->assertTooManyRequests();
        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_admin_can_follow_up_without_modifying_original_message(): void
    {
        $this->admin();
        $message = ContactMessage::factory()->create(['message' => '<script>alert("prueba")</script> Texto de consulta.']);
        $this->get('/admin/contact-messages')->assertOk();
        $this->get('/admin/contact-messages/'.$message->id.'/edit')->assertOk()->assertDontSee('<script>alert("prueba")</script>', false);
        Livewire::test(EditContactMessage::class, ['record' => $message->id])->fillForm(['status' => 'read', 'message' => 'Cambio no permitido'])->call('save')->assertHasNoFormErrors();
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'read', 'message' => '<script>alert("prueba")</script> Texto de consulta.']);
    }
}
