<?php

namespace App\Enums;

enum AdPosition: string
{
    case HeaderBanner = 'header_banner';
    case SidebarTop = 'sidebar_top';
    case SidebarBottom = 'sidebar_bottom';
    case InArticle = 'in_article';
    case FooterBanner = 'footer_banner';
    case BeforeContent = 'before_content';
    case AfterContent = 'after_content';

    public static function labels(): array
    {
        return array_column(
            array_map(fn (self $c) => [
                'value' => $c->value,
                'label' => ucwords(str_replace('_', ' ', $c->value)),
            ], self::cases()),
            'label',
            'value',
        );
    }
}
