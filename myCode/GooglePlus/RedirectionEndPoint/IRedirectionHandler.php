<?php

namespace GooglePlus\RedirectionEndPoint;

use Symfony\Component\HttpFoundation\Request as Request;

interface IRedirectionHandler
{
    public function handle(Request $request);
}