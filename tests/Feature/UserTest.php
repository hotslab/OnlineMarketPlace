<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use App\Jobs\ProcessEmailVerification;
use App\Jobs\ProcessPasswordResetEmail;
use App\Models\User;
use App\Models\PasswordReset;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_login_with_incorrect_credentials_and_error_message_shown()
    {
        $user = User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'testemail@example.com',
            'password' => Hash::make('testpassword1234'),
            'email_verified_at' => now()
        ]);
        $response = $this->from(route('login.view'))->post(route('login.post'), [
            'email' => 'invalid@email.com',
            'password' => 'invalid-password',
        ]);
        $response->assertValid(['password', 'email']);
        $response->assertRedirect(route('login.view'));
        $response->assertSessionHas('failure', 'Incorrect credentials used. Please try again.');
        $this->assertGuest();
    }

    public function test_user_logged_in_successfully_and_redirected_to_products_page()
    {
        $user = User::create([
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'testemail@example.com',
            'password' => Hash::make('testpassword1234'),
            'email_verified_at' => now()
        ]);
        $response = $this->from(route('login.view'))->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'testpassword1234',
        ]);
        $response->assertValid(['password', 'email']);
        $response->assertRedirect(route('products.view'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_registered_and_redirected_to_login_page_and_email_verification_sent()
    {
        Bus::fake();
        $response = $this->from(route('register.view'))->post(route('register.post'), [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'testemail@example.com',
            'password' => 'testpassword1234',
            'password_confirmation' => 'testpassword1234'
        ]);
        $response->assertValid(['name', 'surname', 'email', 'password']);
        $response->assertRedirect(route('login.view'));
        $response->assertSessionHas(
            'success', 
            'Registration was successful. We have sent you an activation link to testemail@example.com, please check in all your folders for the link.'
        );
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'testemail@example.com'
        ]);
        Bus::assertDispatchedAfterResponse(ProcessEmailVerification::class);
        $this->assertGuest();
    }

    public function test_user_is_unverified_and_is_then_automaticaly_logged_out_and_redirected_to_login_page_with_error_message_and_email_verification_sent()
    {
        Bus::fake();
        $user = User::factory()->create();
        $user->email_verified_at = null;
        $user->save();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $response = $this->get('profile');
        $response->assertRedirect('login');
        $response->assertSessionHas('warning', 'You need to confirm your account. We have resent you an activation link, please check your email in all your folders.');
        Bus::assertDispatched(ProcessEmailVerification::class);
        $this->assertGuest();
    }

    public function test_user_submits_email_field_to_reset_password_and_otp_is_sent_to_email_address()
    {
        Bus::fake();
        $user = User::factory()->create();
        $this->assertGuest();
        $response = $this->from(route('password.forgot'))->post(route('password.reset'), ['email' => $user->email]);
        $response->assertValid(['email']);
        $response->assertRedirect(route('password.otp', ['id' => $user->id]));
        Bus::assertDispatchedAfterResponse(ProcessPasswordResetEmail::class);
        $this->assertGuest();
    }

    public function test_user_submits_otp_and_new_pasword_fields_to_reset_password_is_redirected_to_login_page()
    {
        $user = User::factory()->create();
        $token = Str::random(4);
        $paswwordReset = PasswordReset::create([ 'email' => $user->email, 'token' => $token ]);
        $this->assertGuest();
        $response = $this->from(route('password.otp', ['id' => $user->id]))->post(route('password.confirm'), [
            'otp' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'user_id' => $user->id
        ]);
        $response->assertValid(['otp', 'password']);
        $response->assertRedirect(route('login.view'));
        $response->assertSessionHas('success', 'The password for '.$user->email.' was successfully reset.');
        $this->assertGuest();
    }

    public function test_user_updates_profile_details_and_redirects_to_profile_page_with_success_message()
    {
        $user = User::factory()->create();
        $this->assertGuest();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $response = $this->from(route('profile.view'))->post(route('profile.update'), [
            'name' => 'John',
            'surname' => 'Wick',
            'email' => 'baba-yaga@continental.com',
        ]);
        $response->assertValid(['name', 'surname', 'email']);
        $response->assertRedirect(route('profile.view'));
        $response->assertSessionHas('success', 'Profile details updated successfully.');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_changes_password_on_profile_page_and_redirects_to_profile_page_with_success_message()
    {
        $user = User::factory()->create();
        $this->assertGuest();
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
        $response = $this->from(route('profile.view'))->post(route('profile.passwordreset'), [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $response->assertValid(['password']);
        $response->assertRedirect(route('profile.view'));
        $response->assertSessionHas('success', 'Password reset successfully.');
        $this->assertAuthenticatedAs($user);
    }
}
