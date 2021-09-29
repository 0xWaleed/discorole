<?php

namespace DiscoRole;

class PermissionConstants
{
    const CREATE_INSTANT_INVITE      = 0x0000000001; // (1 << 0)
    const KICK_MEMBERS               = 0x0000000002; // (1 << 1)
    const BAN_MEMBERS                = 0x0000000004; // (1 << 2)
    const ADMINISTRATOR              = 0x0000000008; // (1 << 3)
    const MANAGE_CHANNELS            = 0x0000000010; // (1 << 4)
    const MANAGE_GUILD               = 0x0000000020; // (1 << 5)
    const ADD_REACTIONS              = 0x0000000040; // (1 << 6)
    const VIEW_AUDIT_LOG             = 0x0000000080; // (1 << 7)
    const PRIORITY_SPEAKER           = 0x0000000100; // (1 << 8)
    const STREAM                     = 0x0000000200; // (1 << 9)
    const VIEW_CHANNEL               = 0x0000000400; // (1 << 10)
    const SEND_MESSAGES              = 0x0000000800; // (1 << 11)
    const SEND_TTS_MESSAGES          = 0x0000001000; // (1 << 12)
    const MANAGE_MESSAGES            = 0x0000002000; // (1 << 13)
    const EMBED_LINKS                = 0x0000004000; // (1 << 14)
    const ATTACH_FILES               = 0x0000008000; // (1 << 15)
    const READ_MESSAGE_HISTORY       = 0x0000010000; // (1 << 16)
    const MENTION_EVERYONE           = 0x0000020000; // (1 << 17)
    const USE_EXTERNAL_EMOJIS        = 0x0000040000; // (1 << 18)
    const VIEW_GUILD_INSIGHTS        = 0x0000080000; // (1 << 19)
    const CONNECT                    = 0x0000100000; // (1 << 20)
    const SPEAK                      = 0x0000200000; // (1 << 21)
    const MUTE_MEMBERS               = 0x0000400000; // (1 << 22)
    const DEAFEN_MEMBERS             = 0x0000800000; // (1 << 23)
    const MOVE_MEMBERS               = 0x0001000000; // (1 << 24)
    const USE_VAD                    = 0x0002000000; // (1 << 25)
    const CHANGE_NICKNAME            = 0x0004000000; // (1 << 26)
    const MANAGE_NICKNAMES           = 0x0008000000; // (1 << 27)
    const MANAGE_ROLES               = 0x0010000000; // (1 << 28)
    const MANAGE_WEBHOOKS            = 0x0020000000; // (1 << 29)
    const MANAGE_EMOJIS_AND_STICKERS = 0x0040000000; // (1 << 30)
    const USE_APPLICATION_COMMANDS   = 0x0080000000; // (1 << 31)
    const REQUEST_TO_SPEAK           = 0x0100000000; // (1 << 32)
    const MANAGE_THREADS             = 0x0400000000; // (1 << 34)
    const CREATE_PUBLIC_THREADS      = 0x0800000000; // (1 << 35)
    const CREATE_PRIVATE_THREADS     = 0x1000000000; // (1 << 36)
    const USE_EXTERNAL_STICKERS      = 0x2000000000; // (1 << 37)
    const SEND_MESSAGES_IN_THREADS   = 0x4000000000; // (1 << 38)
    const START_EMBEDDED_ACTIVITIES  = 0x8000000000; // (1 << 39)


    const PERMISSION_LABELS = [
        self::ADMINISTRATOR => 'Administrate',
        self::KICK_MEMBERS  => 'Kick',
        self::BAN_MEMBERS   => 'Ban',
        self::MANAGE_GUILD  => 'Manage'
    ];
}