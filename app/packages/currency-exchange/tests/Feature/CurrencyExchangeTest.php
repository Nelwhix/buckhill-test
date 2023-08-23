<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

it('returns error when run without query params', function () {
   $this->get("/exchange")
       ->assertStatus(Response::HTTP_BAD_REQUEST);
});

it('returns error when converting to unknown currency', function () {
    $this->get("/exchange?amount=100&to=NGN")
        ->assertStatus(Response::HTTP_BAD_REQUEST);
});

it('converts currency', function () {
    $this->get("/exchange?amount=100&to=GBP")
        ->assertStatus(Response::HTTP_OK);
});
