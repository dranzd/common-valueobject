<?php

declare(strict_types=1);

namespace Dranzd\Common\Domain\ValueObject\Identity;

/**
 * Defines the type of actor that initiated a domain action.
 *
 * An actor can be a user, system process, external integration, or unknown entity.
 */
enum ActorType: string
{
    /**
     * A human user performing an action.
     */
    case User = 'User';

    /**
     * An automated system process or background job.
     */
    case System = 'System';

    /**
     * An external API client or integration partner.
     */
    case Integration = 'Integration';

    /**
     * An unknown or unidentified actor (fallback).
     */
    case Unknown = 'Unknown';
}
