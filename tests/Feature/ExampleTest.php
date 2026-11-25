<?php

/**
 * Smoke test: the home page loads.
 *
 * Currently FAILS because routes/web.php has no "/" route. Fixing it is
 * Milestone 1, Task 1.2.
 */
test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('the home page links to every live module', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee(route('roomcheck.index'))
        ->assertSee(route('events.index'));
});
