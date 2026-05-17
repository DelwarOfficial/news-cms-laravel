<?php

namespace App\Services\Rbac;

class PermissionService
{
    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_ADMIN = 'Admin';
    public const ROLE_EDITOR = 'Editor';
    public const ROLE_AUTHOR_REPORTER = 'Author/Reporter';
    public const ROLE_CONTRIBUTOR = 'Contributor';
    public const ROLE_TRANSLATOR = 'Translator';

    public const PERMISSIONS = [
        'posts.create',
        'posts.edit.own',
        'posts.edit.any',
        'posts.submit_review',
        'posts.delete.own',
        'posts.delete.any',
        'posts.publish',
        'categories.manage',
        'tags.manage',
        'media.manage',
        'menus.manage',
        'users.manage',
        'users.create',
        'roles.manage',
        'roles.create',
        'settings.manage',
        'ads.manage',
        'dashboard.view',
        'themes.manage',
        'api_keys.manage',
        'backups.manage',
        'translations.manage',
    ];

    public const ROLE_PERMISSIONS = [
        self::ROLE_AUTHOR_REPORTER => [
            'posts.create',
            'posts.edit.own',
            'posts.submit_review',
            'posts.delete.own',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ],
        self::ROLE_EDITOR => [
            'posts.create',
            'posts.edit.own',
            'posts.edit.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'categories.manage',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ],
        self::ROLE_CONTRIBUTOR => [
            'posts.create',
            'posts.edit.own',
            'posts.submit_review',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ],
        self::ROLE_TRANSLATOR => [
            'translations.manage',
            'posts.create',
            'posts.edit.own',
            'dashboard.view',
        ],
        self::ROLE_ADMIN => [
            'posts.create',
            'posts.edit.own',
            'posts.edit.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'categories.manage',
            'tags.manage',
            'media.manage',
            'menus.manage',
            'users.manage',
            'users.create',
            'settings.manage',
            'ads.manage',
            'dashboard.view',
            'themes.manage',
        ],
        self::ROLE_SUPER_ADMIN => self::PERMISSIONS,
    ];

    public static function roles(): array
    {
        return array_keys(self::ROLE_PERMISSIONS);
    }

    public static function editorialRoles(): array
    {
        return [
            self::ROLE_AUTHOR_REPORTER,
            self::ROLE_CONTRIBUTOR,
        ];
    }
}
