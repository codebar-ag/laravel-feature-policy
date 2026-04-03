<?php

namespace CodebarAg\LaravelFeaturePolicy;

abstract class Value
{
    final public const string ALL = '*';

    final public const string SELF = 'self';

    final public const string NONE = '()';
}
