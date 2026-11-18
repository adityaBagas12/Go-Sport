<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_submit_contact_message(): void
    {
        $response = $this->post(route('pages.contact.send'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'subject' => 'Tanya Stok Sepatu',
            'message' => 'Apakah sepatu badminton ukuran 42 masih tersedia?',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'subject' => 'Tanya Stok Sepatu',
            'is_read' => false,
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('pages.contact.send'), []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_non_admin_cannot_access_admin_messages(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get(route('admin.messages.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_and_read_contact_messages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = ContactMessage::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'subject' => 'Pertanyaan Kemitraan',
            'message' => 'Halo kami dari instansi ingin memesan jersey dalam jumlah banyak.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.messages.index'));
        $response->assertOk();
        $response->assertSee('Siti Rahma');
        $response->assertSee('Pertanyaan Kemitraan');

        $detailResponse = $this->actingAs($admin)->get(route('admin.messages.show', $message));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Halo kami dari instansi');

        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_admin_can_delete_contact_message(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = ContactMessage::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'subject' => 'Uji Coba',
            'message' => 'Pesan uji coba.',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.messages.destroy', $message));
        $response->assertRedirect(route('admin.messages.index'));
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_admin_can_reply_to_message_directly_on_website(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $message = ContactMessage::create([
            'user_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'subject' => 'Tanya Raket',
            'message' => 'Apakah raket Yonex include senar?',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.messages.reply', $message), [
            'reply' => 'Halo kak, betul sudah termasuk senar dan pemasangan gratis.',
        ]);

        $response->assertSessionHas('success');

        $message->refresh();
        $this->assertTrue($message->isReplied());
        $this->assertEquals('Halo kak, betul sudah termasuk senar dan pemasangan gratis.', $message->admin_reply);
        $this->assertEquals($admin->id, $message->replied_by);
        $this->assertNotNull($message->replied_at);
    }

    public function test_customer_can_see_their_messages_and_admin_replies(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $message = ContactMessage::create([
            'user_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'subject' => 'Status Retur Jersey',
            'message' => 'Apakah jersey ukuran M saya sudah bisa ditukar?',
            'admin_reply' => 'Sudah kami setujui kak, paket pengganti segera dikirim.',
            'replied_at' => now(),
            'replied_by' => $admin->id,
        ]);

        $listResponse = $this->actingAs($customer)->get(route('account.messages'));
        $listResponse->assertOk();
        $listResponse->assertSee('Status Retur Jersey');
        $listResponse->assertSee('Jawaban Resmi Admin GoSport');

        $detailResponse = $this->actingAs($customer)->get(route('account.messages.show', $message));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Sudah kami setujui kak, paket pengganti segera dikirim.');

        $this->assertNotNull($message->fresh()->customer_read_at);
    }

    public function test_customer_cannot_view_other_customers_message(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);

        $message = ContactMessage::create([
            'user_id' => $customer1->id,
            'name' => $customer1->name,
            'email' => $customer1->email,
            'subject' => 'Pesan Rahasia',
            'message' => 'Isi pesan rahasia.',
        ]);

        $response = $this->actingAs($customer2)->get(route('account.messages.show', $message));
        $response->assertForbidden();
    }
}
