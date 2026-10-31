<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Powiadomienia w dzwoneczku - tylko właściciel widzi swój strumień.
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Wiadomości 1:1 - kanał na parę użytkowników, posortowaną żeby oba końce nasłuchiwały tego samego kanału.
Broadcast::channel('conversation.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    return in_array($user->id, [(int) $userId1, (int) $userId2], true);
});
