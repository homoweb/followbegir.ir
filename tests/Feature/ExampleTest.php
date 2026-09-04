<?php

test('returns a successful response', function () {
    $response = $this->get(route('main.home'));

    $response->assertOk();
});
