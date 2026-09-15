<?php

namespace Tests\Unit;

use App\Models\Helpers;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_temporary_passwords_have_the_requested_length(): void
    {
        $this->assertSame(10, strlen(Helpers::rand_passwd()));
        $this->assertSame(6, strlen(Helpers::rand_passwd(6)));
        $this->assertNotSame(Helpers::rand_passwd(), Helpers::rand_passwd());
    }

    public function test_tokens_are_hex_strings_of_the_requested_length(): void
    {
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', Helpers::token(32));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/', Helpers::token(40));
    }

    public function test_legacy_hashes_are_detected(): void
    {
        $this->assertTrue(Helpers::isLegacyHash(sha1(md5('secret'))));
        $this->assertFalse(Helpers::isLegacyHash('$2y$04$abcdefghijklmnopqrstuuabcdefghijklmnopqrstuvwxyz012345678'));
        $this->assertFalse(Helpers::isLegacyHash(null));
    }
}
