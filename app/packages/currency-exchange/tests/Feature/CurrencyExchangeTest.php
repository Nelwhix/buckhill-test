<?php declare(strict_types=1);

it('converts from euro to usd', function () {
   $response = $this->get("/exchange");

   dd($response->json());
});
