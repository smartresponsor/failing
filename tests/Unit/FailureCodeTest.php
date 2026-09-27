<?php

declare(strict_types=1);

namespace App\Failing\Tests\Unit;

use App\Failing\ValueObject\FailureCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FailureCodeTest extends TestCase
{
    /** @return iterable<string, array{string}> */
    public static function validConsumerCodes(): iterable
    {
        yield 'hyphen vocabulary' => ['payment-not-found'];
        yield 'underscore vocabulary' => ['crud_not_found'];
        yield 'dotted vocabulary' => ['billing.invoice_not_found'];
    }

    #[DataProvider('validConsumerCodes')]
    public function testConsumerOwnedVocabularyFormsAreAccepted(string $code): void
    {
        self::assertSame($code, (string) new FailureCode($code));
    }

    public function testUnsafeHumanFacingCodeIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FailureCode('Payment Not Found!');
    }
}
