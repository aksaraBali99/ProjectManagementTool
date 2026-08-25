<?php

use App\Services\Import\ImportIdCodec;

test('encode formats a prefix and id into a zero-padded code', function () {
    expect(ImportIdCodec::encode('CO', 1))->toBe('CO-0001');
    expect(ImportIdCodec::encode('DP', 42))->toBe('DP-0042');
    expect(ImportIdCodec::encode('PRJ', 10000))->toBe('PRJ-10000');
});

test('decode round-trips a value encoded with the same prefix', function () {
    expect(ImportIdCodec::decode('CO', 'CO-0001'))->toBe(1);
    expect(ImportIdCodec::decode('DP', 'DP-0042'))->toBe(42);
});

test('decode returns null for a blank value', function () {
    expect(ImportIdCodec::decode('CO', ''))->toBeNull();
    expect(ImportIdCodec::decode('CO', null))->toBeNull();
    expect(ImportIdCodec::decode('CO', '   '))->toBeNull();
});

test('decode returns null for a malformed or wrong-prefix value', function () {
    expect(ImportIdCodec::decode('CO', 'DP-0001'))->toBeNull();
    expect(ImportIdCodec::decode('CO', 'not-an-id'))->toBeNull();
    expect(ImportIdCodec::decode('CO', 'CO-abcd'))->toBeNull();
});
