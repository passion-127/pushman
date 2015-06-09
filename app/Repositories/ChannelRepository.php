<?php

namespace Pushman\Repositories;

use Pushman\Channel;
use Pushman\Exceptions\InvalidChannelException;
use Pushman\Site;

class ChannelRepository
{
    public static function buildPublic(Site $site)
    {
        $channel = Channel::where('site_id', $site->id)->where('name', 'public')->first();

        if ($channel) {
            return $channel;
        }

        return self::build('public', 'no', env('PUSHMAN_MAX', 200), $site);
    }

    public static function getPublic(Site $site)
    {
        return Channel::where('site_id', $site->id)->where('name', 'public')->first();
    }

    public static function build($name, $refreshes, $max_connections, Site $site)
    {
        $channel = new Channel();
        $channel->site_id = $site->id;
        $channel->name = $name;
        $channel->refreshes = $refreshes;
        $channel->max_connections = $max_connections;
        $channel->generateToken();
        $channel->save();

        return $channel;
    }

    public static function validateName($name, Site $site)
    {
        $channel = $site->channels()->where('name', $name)->first();

        if (is_null($channel)) {
            return $name;
        }

        throw new InvalidChannelException('This channel already exists.');
    }

    public static function validateMaxConnections($max_connections)
    {
        $max_connections = (int) $max_connections;

        if (user() && user()->isAdmin()) {
            return true;
        }

        $defined_max = env('PUSHMAN_MAX', 3);

        if ($max_connections > $defined_max or $max_connections === 0) {
            return false;
        }

        return true;
    }
}
