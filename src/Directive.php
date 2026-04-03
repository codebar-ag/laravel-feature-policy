<?php

namespace CodebarAg\LaravelFeaturePolicy;

use CodebarAg\LaravelFeaturePolicy\Exceptions\UnknownPermissionGroupException;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DefaultFeatureGroup;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\DirectiveContract;
use CodebarAg\LaravelFeaturePolicy\FeatureGroups\ProposedFeatureGroup;

abstract class Directive implements DirectiveContract
{
    /** @var list<string> */
    protected array $rules = [];

    final public const string ACCELEROMETER = 'accelerometer';

    final public const string AMBIENT_LIGHT_SENSOR = 'ambient-light-sensor';

    final public const string AUTOPLAY = 'autoplay';

    final public const string BATTERY = 'battery';

    final public const string BLUETOOTH = 'bluetooth';

    final public const string CAMERA = 'camera';

    final public const string CH_UA = 'ch-ua';

    final public const string CH_UA_ARCH = 'ch-ua-arch';

    final public const string CH_UA_BITNESS = 'ch-ua-bitness';

    final public const string CH_UA_FULL_VERSION = 'ch-ua-full-version';

    final public const string CH_UA_FULL_VERSION_LIST = 'ch-ua-full-version-list';

    final public const string CH_UA_MOBILE = 'ch-ua-mobile';

    final public const string CH_UA_MODEL = 'ch-ua-model';

    final public const string CH_UA_PLATFORM = 'ch-ua-platform';

    final public const string CH_UA_PLATFORM_VERSION = 'ch-ua-platform-version';

    final public const string CH_UA_WOW64 = 'ch-ua-wow64';

    final public const string CROSS_ORIGIN_ISOLATED = 'cross-origin-isolated';

    final public const string DISPLAY_CAPTURE = 'display-capture';

    /** @deprecated formerly in Chrome, behind a flag */
    final public const string DOCUMENT_DOMAIN = 'document-domain';

    final public const string ENCRYPTED_MEDIA = 'encrypted-media';

    final public const string EXECUTION_WHILE_NOT_RENDERED = 'execution-while-not-rendered';

    final public const string EXECUTION_WHILE_OUT_OF_VIEWPORT = 'execution-while-out-of-viewport';

    final public const string FLOC = 'interest-cohort';

    final public const string FULLSCREEN = 'fullscreen';

    final public const string GEOLOCATION = 'geolocation';

    final public const string GYROSCOPE = 'gyroscope';

    final public const string HID = 'hid';

    final public const string IDLE_DETECTION = 'idle-detection';

    final public const string KEYBOARD_MAP = 'keyboard-map';

    final public const string MAGNETOMETER = 'magnetometer';

    final public const string MICROPHONE = 'microphone';

    final public const string MIDI = 'midi';

    final public const string NAVIGATION_OVERRIDE = 'navigation-override';

    final public const string PAYMENT = 'payment';

    final public const string PICTURE_IN_PICTURE = 'picture-in-picture';

    final public const string PUBLICKEY_CREDENTIALS_GET = 'publickey-credentials-get';

    final public const string SCREEN_WAKE_LOCK = 'screen-wake-lock';

    final public const string SERIAL = 'serial';

    /** @deprecated unknown directive */
    final public const string SPEAKER = 'speaker';

    final public const string SYNC_XHR = 'sync-xhr';

    final public const string USB = 'usb';

    final public const string VR = 'vr'; // after Chrome 79 replaced by xr-spatial-tracking

    /** @deprecated known as 'screen-wake-rock' */
    final public const string WAKE_LOCK = 'wake-lock';

    final public const string WEB_SHARE = 'web-share';

    /** @deprecated unknown directive */
    final public const string XR = 'vr';

    /** @see Implemented in Chrome as vr prior to Chrome 79 */
    final public const string XR_SPATIAL_TRACKING = 'xr-spatial-tracking';

    public static function make(string $directive, string $type = DefaultFeatureGroup::class): DirectiveContract
    {
        return match ($type) {
            DefaultFeatureGroup::class => DefaultFeatureGroup::directive($directive),
            ProposedFeatureGroup::class => ProposedFeatureGroup::directive($directive),
            default => throw new UnknownPermissionGroupException($type),
        };
    }

    public function addRule(string $rule): static
    {
        if (in_array($rule, $this->rules, true)) {
            return $this;
        }

        $this->rules[] = $rule;

        return $this;
    }

    /** @return list<string> */
    public function rules(): array
    {
        return $this->rules;
    }

    public function note(): string
    {
        return '';
    }

    public function isDeprecated(): bool
    {
        return false;
    }
}
