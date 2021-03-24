<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notification\NotificationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return NotificationResource::collection(auth()->user()->notifications);
    }

    /**
     * @return Response
     */
    public function readAllNotifications(): Response
    {
        auth()->user()->notifications->markAsRead();
        return response()->noContent();
    }

    /**
     * @return Response
     */
    public function destroy(): Response
    {
        auth()->user()->notifications()->delete();
        return response()->noContent();
    }
}
