<?php

namespace Tests\Feature;

use App\Actions\Booking\BatchNotifyToScoreBookings;
use App\Actions\Booking\NotifyToScoreBooking;
use App\Actions\Booking\StoreBookingScoreAction;
use App\DTOs\StoreBookingScoreDTO;
use App\Events\NotificationDeleted;
use App\Jobs\BatchNotifyToScoreBookingsJob;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\BookingScoredNotification;
use App\Notifications\ScoreYourBookingNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingScoringNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notify_to_score_booking_action_sends_notification_and_saves_id(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $category = Category::query()->create([
            'name' => 'Sports',
            'icon' => 'Ionicons/trophy-outline',
            'color' => '#111111',
        ]);

        $venue = Venue::query()->create([
            'name' => 'Venue 1',
            'address' => 'Address 1',
            'category_id' => $category->id,
            'media' => '',
        ]);

        $resource = Resource::query()->create([
            'venue_id' => $venue->id,
            'name' => 'Court 1',
            'type' => 'court',
            'capacity' => 4,
        ]);

        $activity = Activity::query()->create([
            'name' => 'Padel',
            'duration_minutes' => 60,
            'slot_interval_minutes' => 30,
            'venue_id' => $venue->id,
        ]);

        $booking = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->subHours(2),
            'end_at' => Carbon::now()->subHour(),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        $uuid = (string) Str::uuid();
        Str::createUuidsUsing(fn () => $uuid);

        \App\Models\Notification::query()->forceCreate([
            'id' => $uuid,
            'type' => ScoreYourBookingNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [],
        ]);

        $action = app(NotifyToScoreBooking::class);
        $action->handle($booking);

        Str::createUuidsNormally();

        $booking->refresh();

        $this->assertEquals($uuid, $booking->scoring_notification_id);

        Notification::assertSentTo(
            $user,
            ScoreYourBookingNotification::class,
            function ($notification) use ($booking) {
                return $notification->id === $booking->scoring_notification_id;
            }
        );
    }

    public function test_batch_notify_to_score_bookings_action(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $category = Category::query()->create([
            'name' => 'Sports',
            'icon' => 'Ionicons/trophy-outline',
            'color' => '#111111',
        ]);

        $venue = Venue::query()->create([
            'name' => 'Venue 1',
            'address' => 'Address 1',
            'category_id' => $category->id,
            'media' => '',
        ]);

        $resource = Resource::query()->create([
            'venue_id' => $venue->id,
            'name' => 'Court 1',
            'type' => 'court',
            'capacity' => 4,
        ]);

        $activity = Activity::query()->create([
            'name' => 'Padel',
            'duration_minutes' => 60,
            'slot_interval_minutes' => 30,
            'venue_id' => $venue->id,
        ]);

        $endedConfirmedNoNotif = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->subHours(3),
            'end_at' => Carbon::now()->subHours(2),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        $notEndedConfirmed = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->addHour(),
            'end_at' => Carbon::now()->addHours(2),
            'units' => 1,
            'status' => 'confirmed',
        ]);

        $endedCancelled = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->subHours(3),
            'end_at' => Carbon::now()->subHours(2),
            'units' => 1,
            'status' => 'cancelled',
        ]);

        $notif = \App\Models\Notification::query()->forceCreate([
            'type' => ScoreYourBookingNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [],
        ]);

        $endedConfirmedHasNotif = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->subHours(5),
            'end_at' => Carbon::now()->subHours(4),
            'units' => 1,
            'status' => 'confirmed',
            'scoring_notification_id' => $notif->id,
        ]);

        $uuid = (string) Str::uuid();
        Str::createUuidsUsing(fn () => $uuid);

        \App\Models\Notification::query()->forceCreate([
            'id' => $uuid,
            'type' => ScoreYourBookingNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [],
        ]);

        $action = app(BatchNotifyToScoreBookings::class);
        $action->handle();

        Str::createUuidsNormally();

        $endedConfirmedNoNotif->refresh();
        $notEndedConfirmed->refresh();
        $endedCancelled->refresh();
        $endedConfirmedHasNotif->refresh();

        $this->assertEquals($uuid, $endedConfirmedNoNotif->scoring_notification_id);
        $this->assertNull($notEndedConfirmed->scoring_notification_id);
        $this->assertNull($endedCancelled->scoring_notification_id);
        $this->assertEquals($notif->id, $endedConfirmedHasNotif->scoring_notification_id);

        Notification::assertSentTo($user, ScoreYourBookingNotification::class);
    }

    public function test_batch_notify_to_score_bookings_job_dispatches_successfully(): void
    {
        Queue::fake();

        BatchNotifyToScoreBookingsJob::dispatch();

        Queue::assertPushed(BatchNotifyToScoreBookingsJob::class);
    }

    public function test_bookings_batch_scoring_artisan_command_dispatches_job(): void
    {
        Queue::fake();

        $this->artisan('bookings:batch-scoring')->assertExitCode(0);

        Queue::assertPushed(BatchNotifyToScoreBookingsJob::class);
    }

    public function test_storing_booking_score_deletes_scoring_notification(): void
    {
        Event::fake([
            NotificationDeleted::class,
        ]);
        Notification::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::query()->create([
            'name' => 'Sports',
            'icon' => 'Ionicons/trophy-outline',
            'color' => '#111111',
        ]);

        $venue = Venue::query()->create([
            'name' => 'Venue 1',
            'address' => 'Address 1',
            'category_id' => $category->id,
            'media' => '',
        ]);

        $resource = Resource::query()->create([
            'venue_id' => $venue->id,
            'name' => 'Court 1',
            'type' => 'court',
            'capacity' => 4,
        ]);

        $activity = Activity::query()->create([
            'name' => 'Padel',
            'duration_minutes' => 60,
            'slot_interval_minutes' => 30,
            'venue_id' => $venue->id,
        ]);

        $notification = \App\Models\Notification::query()->create([
            'type' => ScoreYourBookingNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [],
        ]);

        $booking = Booking::query()->create([
            'payment' => true,
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'activity_id' => $activity->id,
            'start_at' => Carbon::now()->subHours(2),
            'end_at' => Carbon::now()->subHour(),
            'units' => 1,
            'status' => 'confirmed',
            'scoring_notification_id' => $notification->id,
        ]);

        $dto = new StoreBookingScoreDTO([
            [
                'user_id' => $user->id,
                'booking_guest_id' => null,
                'score' => 10,
            ],
        ]);

        $action = app(StoreBookingScoreAction::class);
        $action->handle($booking->id, $dto);

        $booking->refresh();

        $this->assertNull($booking->scoring_notification_id);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);

        Event::assertDispatched(NotificationDeleted::class);
        Notification::assertSentTo($user, BookingScoredNotification::class);
    }
}
