<?php

namespace Core;

interface Middleware
{
    /**
     * Handle the middleware logic.
     * Call http_response_code() + exit on failure.
     */
    public function handle(): void;
}
