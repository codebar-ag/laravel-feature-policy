<?php

use CodebarAg\LaravelFeaturePolicy\FeatureGroups\ProposedFeatureGroup;

beforeEach(function () {
    config(['feature-policy.directives.proposal' => true]);
});

it('resolves each proposed directive', function (string $directive) {
    $resolved = ProposedFeatureGroup::directive($directive);

    expect($resolved->name())->toBe($directive);
})->with([
    ProposedFeatureGroup::CLIPBOARD_READ,
    ProposedFeatureGroup::CLIPBOARD_WRITE,
    ProposedFeatureGroup::GAMEPAD,
    ProposedFeatureGroup::SHARED_AUTOFILL,
    ProposedFeatureGroup::SPEAKER_SELECTION,
])->group('unit', 'feature-policy');
