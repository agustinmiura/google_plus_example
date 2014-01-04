<?php

namespace GooglePlus\TokenRevoker;

interface ITokenRevoker
{
    public function revokeToken($token);
}