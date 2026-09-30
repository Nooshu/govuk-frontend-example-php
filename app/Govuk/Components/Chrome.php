<?php

declare(strict_types=1);

namespace App\Govuk\Components;

use App\Govuk\Attributes;
use App\Govuk\Nunjucks;
use App\Govuk\Params;
use App\Govuk\SafeString;

/**
 * Ported from GOV.UK Frontend macros (via Python/Go reference) for byte-for-byte fixture parity.
 */
final class Chrome
{
    private const LOGO_CROWN = '    <g>
      <circle cx="20" cy="17.6" r="3.7"/>
      <circle cx="10.2" cy="23.5" r="3.7"/>
      <circle cx="3.7" cy="33.2" r="3.7"/>
      <circle cx="31.7" cy="30.6" r="3.7"/>
      <circle cx="43.3" cy="17.6" r="3.7"/>
      <circle cx="53.2" cy="23.5" r="3.7"/>
      <circle cx="59.7" cy="33.2" r="3.7"/>
      <circle cx="31.7" cy="30.6" r="3.7"/>
      <path d="M33.1,9.8c.2-.1.3-.3.5-.5l4.6,2.4v-6.8l-4.6,1.5c-.1-.2-.3-.3-.5-.5l1.9-5.9h-6.7l1.9,5.9c-.2.1-.3.3-.5.5l-4.6-1.5v6.8l4.6-2.4c.1.2.3.3.5.5l-2.6,8c-.9,2.8,1.2,5.7,4.1,5.7h0c3,0,5.1-2.9,4.1-5.7l-2.6-8ZM37,37.9s-3.4,3.8-4.1,6.1c2.2,0,4.2-.5,6.4-2.8l-.7,8.5c-2-2.8-4.4-4.1-5.7-3.8.1,3.1.5,6.7,5.8,7.2,3.7.3,6.7-1.5,7-3.8.4-2.6-2-4.3-3.7-1.6-1.4-4.5,2.4-6.1,4.9-3.2-1.9-4.5-1.8-7.7,2.4-10.9,3,4,2.6,7.3-1.2,11.1,2.4-1.3,6.2,0,4,4.6-1.2-2.8-3.7-2.2-4.2.2-.3,1.7.7,3.7,3,4.2,1.9.3,4.7-.9,7-5.9-1.3,0-2.4.7-3.9,1.7l2.4-8c.6,2.3,1.4,3.7,2.2,4.5.6-1.6.5-2.8,0-5.3l5,1.8c-2.6,3.6-5.2,8.7-7.3,17.5-7.4-1.1-15.7-1.7-24.5-1.7h0c-8.8,0-17.1.6-24.5,1.7-2.1-8.9-4.7-13.9-7.3-17.5l5-1.8c-.5,2.5-.6,3.7,0,5.3.8-.8,1.6-2.3,2.2-4.5l2.4,8c-1.5-1-2.6-1.7-3.9-1.7,2.3,5,5.2,6.2,7,5.9,2.3-.4,3.3-2.4,3-4.2-.5-2.4-3-3.1-4.2-.2-2.2-4.6,1.6-6,4-4.6-3.7-3.7-4.2-7.1-1.2-11.1,4.2,3.2,4.3,6.4,2.4,10.9,2.5-2.8,6.3-1.3,4.9,3.2-1.8-2.7-4.1-1-3.7,1.6.3,2.3,3.3,4.1,7,3.8,5.4-.5,5.7-4.2,5.8-7.2-1.3-.2-3.7,1-5.7,3.8l-.7-8.5c2.2,2.3,4.2,2.7,6.4,2.8-.7-2.3-4.1-6.1-4.1-6.1h10.6,0Z"/>
    </g>';

    private const LOGO_LOGOTYPE = '    <circle class="govuk-logo-dot" cx="226" cy="36" r="7.3"/>
    <path d="M93.94 41.25c.4 1.81 1.2 3.21 2.21 4.62 1 1.4 2.21 2.41 3.61 3.21s3.21 1.2 5.22 1.2 3.61-.4 4.82-1c1.4-.6 2.41-1.4 3.21-2.41.8-1 1.4-2.01 1.61-3.01s.4-2.01.4-3.01v.14h-10.86v-7.02h20.07v24.08h-8.03v-5.56c-.6.8-1.38 1.61-2.19 2.41-.8.8-1.81 1.2-2.81 1.81-1 .4-2.21.8-3.41 1.2s-2.41.4-3.81.4a18.56 18.56 0 0 1-14.65-6.63c-1.6-2.01-3.01-4.41-3.81-7.02s-1.4-5.62-1.4-8.83.4-6.02 1.4-8.83a20.45 20.45 0 0 1 19.46-13.65c3.21 0 4.01.2 5.82.8 1.81.4 3.61 1.2 5.02 2.01 1.61.8 2.81 2.01 4.01 3.21s2.21 2.61 2.81 4.21l-7.63 4.41c-.4-1-1-1.81-1.61-2.61-.6-.8-1.4-1.4-2.21-2.01-.8-.6-1.81-1-2.81-1.4-1-.4-2.21-.4-3.61-.4-2.01 0-3.81.4-5.22 1.2-1.4.8-2.61 1.81-3.61 3.21s-1.61 2.81-2.21 4.62c-.4 1.81-.6 3.71-.6 5.42s.8 5.22.8 5.22Zm57.8-27.9c3.21 0 6.22.6 8.63 1.81 2.41 1.2 4.82 2.81 6.62 4.82S170.2 24.39 171 27s1.4 5.62 1.4 8.83-.4 6.02-1.4 8.83-2.41 5.02-4.01 7.02-4.01 3.61-6.62 4.82-5.42 1.81-8.63 1.81-6.22-.6-8.63-1.81-4.82-2.81-6.42-4.82-3.21-4.41-4.01-7.02-1.4-5.62-1.4-8.83.4-6.02 1.4-8.83 2.41-5.02 4.01-7.02 4.01-3.61 6.42-4.82 5.42-1.81 8.63-1.81Zm0 36.73c1.81 0 3.61-.4 5.02-1s2.61-1.81 3.61-3.01 1.81-2.81 2.21-4.41c.4-1.81.8-3.61.8-5.62 0-2.21-.2-4.21-.8-6.02s-1.2-3.21-2.21-4.62c-1-1.2-2.21-2.21-3.61-3.01s-3.21-1-5.02-1-3.61.4-5.02 1c-1.4.8-2.61 1.81-3.61 3.01s-1.81 2.81-2.21 4.62c-.4 1.81-.8 3.61-.8 5.62 0 2.41.2 4.21.8 6.02.4 1.81 1.2 3.21 2.21 4.41s2.21 2.21 3.61 3.01c1.4.8 3.21 1 5.02 1Zm36.32 7.96-12.24-44.15h9.83l8.43 32.77h.4l8.23-32.77h9.83L200.3 58.04h-12.24Zm74.14-7.96c2.18 0 3.51-.6 3.51-.6 1.2-.6 2.01-1 2.81-1.81s1.4-1.81 1.81-2.81a13 13 0 0 0 .8-4.01V13.9h8.63v28.15c0 2.41-.4 4.62-1.4 6.62-.8 2.01-2.21 3.61-3.61 5.02s-3.41 2.41-5.62 3.21-4.62 1.2-7.02 1.2-5.02-.4-7.02-1.2c-2.21-.8-4.01-1.81-5.62-3.21s-2.81-3.01-3.61-5.02-1.4-4.21-1.4-6.62V13.9h8.63v26.95c0 1.61.2 3.01.8 4.01.4 1.2 1.2 2.21 2.01 2.81.8.8 1.81 1.4 2.81 1.81 0 0 1.34.6 3.51.6Zm34.22-36.18v18.92l15.65-18.92h10.82l-15.03 17.32 16.03 26.83h-10.21l-11.44-20.21-5.62 6.22v13.99h-8.83V13.9"/>';

    private const FOOTER_LICENCE_LOGO = '<svg
            aria-hidden="true"
            focusable="false"
            class="govuk-footer__licence-logo"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 483.2 195.7"
            height="17"
            width="41"
          >
            <path
              fill="currentColor"
              d="M421.5 142.8V.1l-50.7 32.3v161.1h112.4v-50.7zm-122.3-9.6A47.12 47.12 0 0 1 221 97.8c0-26 21.1-47.1 47.1-47.1 16.7 0 31.4 8.7 39.7 21.8l42.7-27.2A97.63 97.63 0 0 0 268.1 0c-36.5 0-68.3 20.1-85.1 49.7A98 98 0 0 0 97.8 0C43.9 0 0 43.9 0 97.8s43.9 97.8 97.8 97.8c36.5 0 68.3-20.1 85.1-49.7a97.76 97.76 0 0 0 149.6 25.4l19.4 22.2h3v-87.8h-80l24.3 27.5zM97.8 145c-26 0-47.1-21.1-47.1-47.1s21.1-47.1 47.1-47.1 47.2 21 47.2 47S123.8 145 97.8 145"
            />
          </svg>';

    private const FOOTER_COPYRIGHT_HREF = 'https://www.nationalarchives.gov.uk/information-management/re-using-public-sector-information/uk-government-licensing-framework/crown-copyright/';

    private const PAGINATION_ARROW_PREVIOUS = '  <svg class="govuk-pagination__icon govuk-pagination__icon--prev" xmlns="http://www.w3.org/2000/svg" height="13" width="15" aria-hidden="true" focusable="false" viewBox="0 0 15 13">
    <path d="m6.5938-0.0078125-6.7266 6.7266 6.7441 6.4062 1.377-1.449-4.1856-3.9768h12.896v-2h-12.984l4.2931-4.293-1.414-1.414z"></path>
  </svg>';

    private const PAGINATION_ARROW_NEXT = '  <svg class="govuk-pagination__icon govuk-pagination__icon--next" xmlns="http://www.w3.org/2000/svg" height="13" width="15" aria-hidden="true" focusable="false" viewBox="0 0 15 13">
    <path d="m8.107-0.0078125-1.4136 1.414 4.2926 4.293h-12.986v2h12.896l-4.1855 3.9766 1.377 1.4492 6.7441-6.4062-6.7246-6.7266z"></path>
  </svg>';

    public static function renderLogo(Params $p): string
    {
        $useLogotype = Nunjucks::truthy(Nunjucks::def($p->get('useLogotype'), true));
        $width = '32';
        $viewBox = '64';
        if ($useLogotype) {
            $width = '162';
            $viewBox = '324';
        }
        $role = 'presentation';
        $ariaLabel = $p->get('ariaLabelText');
        if (Nunjucks::truthy($ariaLabel)) {
            $role = 'img';
        }
        $parts = [];
        $parts[] = '
  <svg
    focusable="false"
    role="'.$role.'"
'.'    xmlns="http://www.w3.org/2000/svg"
'.'    viewBox="0 0 '.$viewBox.' 60"
    height="30"
    width="'.$width.'"
'.'    fill="currentcolor"'.Nunjucks::attributeIf('class', $p->get('classes')).Nunjucks::attributeIf('aria-label', $ariaLabel).Attributes::render($p->get('attributes')).'
  >';
        if (Nunjucks::truthy($ariaLabel)) {
            $parts[] = '<title>'.Nunjucks::out($ariaLabel).'</title>';
        }
        $parts[] = '    '.Nunjucks::indent(Nunjucks::trim(self::LOGO_CROWN), 2, false).'
';
        if ($useLogotype) {
            $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(self::LOGO_LOGOTYPE), 2, false).'
';
        }
        $parts[] = '  </svg>
';

        return implode('', $parts);
    }

    public static function renderGenericHeader(Params $p): string
    {
        $namespace = Nunjucks::out(Nunjucks::def($p->get('_namespace'), 'govuk-generic'));

        return '<div class="'.$namespace.'-header'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
'.'  <div class="'.$namespace.'-header__container '.Nunjucks::out(Nunjucks::defTruthy($p->get('containerClasses'), 'govuk-width-container')).'">
'.'    <div class="'.$namespace.'-header__logo">
'.'      <a href="'.Nunjucks::out(Nunjucks::defTruthy($p->get('url'), '/')).'" class="'.$namespace.'-header__homepage-link">
'.'        '.Nunjucks::content($p, 'logoHtml', 'logoText').'
'.'      </a>
    </div>
  </div>
</div>';
    }

    public static function renderHeader(Params $p): string
    {
        $logo = self::renderLogo(Params::make('classes', 'govuk-header__logotype', 'ariaLabelText', 'GOV.UK'));
        $logoContent = '  '.Nunjucks::trim($logo).'
';
        $productName = $p->get('productName');
        if (Nunjucks::truthy($productName)) {
            $logoContent .= '<span class="govuk-header__product-name">'.Nunjucks::out($productName).'</span>';
        }

        return self::renderGenericHeader(Params::make('_namespace', 'govuk', 'logoHtml', new SafeString(Nunjucks::indent($logoContent, 8, false)), 'url', Nunjucks::defTruthy($p->get('homepageUrl'), '//gov.uk'), 'containerClasses', $p->get('containerClasses'), 'classes', $p->get('classes'), 'attributes', $p->get('attributes')));
    }

    public static function renderFooter(Params $p): string
    {
        $parts = [];
        $parts[] = '<div class="govuk-footer'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).'>
';
        $parts[] = '  <div class="govuk-width-container'.Nunjucks::classesIf($p->get('containerClasses')).'">';
        $parts[] = self::renderLogo(Params::make('classes', 'govuk-footer__crown', 'useLogotype', false));
        $parts[] = '
';
        $navigation = Nunjucks::items($p->get('navigation'));
        if (count($navigation) > 0) {
            $parts[] = '      <div class="govuk-footer__navigation">
';
            foreach ($navigation as $nav) {
                $parts[] = '          <div class="govuk-footer__section govuk-grid-column-'.Nunjucks::out(Nunjucks::defTruthy(Nunjucks::get($nav, 'width'), 'full')).'">
';
                $parts[] = '            <h2 class="govuk-footer__heading govuk-heading-m">'.Nunjucks::out(Nunjucks::get($nav, 'title')).'</h2>
';
                $links = Nunjucks::items(Nunjucks::get($nav, 'items'));
                if (count($links) > 0) {
                    $listClasses = '';
                    $columns = Nunjucks::get($nav, 'columns');
                    if (Nunjucks::truthy($columns)) {
                        $listClasses = ' govuk-footer__list--columns-'.Nunjucks::escape(Nunjucks::str($columns));
                    }
                    $parts[] = '              <ul class="govuk-footer__list'.$listClasses.'">
';
                    foreach ($links as $link) {
                        if ((! Nunjucks::truthy(Nunjucks::get($link, 'href')) || ! Nunjucks::truthy(Nunjucks::get($link, 'text')))) {
                            continue;
                        }
                        $parts[] = '                    <li class="govuk-footer__list-item">
';
                        $parts[] = '                      <a class="govuk-footer__link" href="'.Nunjucks::out(Nunjucks::get($link, 'href')).'"'.Attributes::render(Nunjucks::get($link, 'attributes')).'>
';
                        $parts[] = '                        '.Nunjucks::out(Nunjucks::get($link, 'text')).'
';
                        $parts[] = '                      </a>
                    </li>
';
                    }
                    $parts[] = '              </ul>
';
                }
                $parts[] = '          </div>
';
            }
            $parts[] = '      </div>
';
            $parts[] = '      <hr class="govuk-footer__section-break">
';
        }
        $parts[] = '    <div class="govuk-footer__meta">
';
        $parts[] = '      <div class="govuk-footer__meta-item govuk-footer__meta-item--grow">
';
        $meta = $p->get('meta');
        if (Nunjucks::truthy($meta)) {
            $parts[] = '        <h2 class="govuk-visually-hidden">'.Nunjucks::out(Nunjucks::defTruthy(Nunjucks::get($meta, 'visuallyHiddenTitle'), 'Support links')).'</h2>
';
            $links = Nunjucks::items(Nunjucks::get($meta, 'items'));
            if (count($links) > 0) {
                $parts[] = '        <ul class="govuk-footer__inline-list">
';
                foreach ($links as $link) {
                    $parts[] = '          <li class="govuk-footer__inline-list-item">
';
                    $parts[] = '            <a class="govuk-footer__link" href="'.Nunjucks::out(Nunjucks::get($link, 'href')).'"'.Attributes::render(Nunjucks::get($link, 'attributes')).'>
';
                    $parts[] = '              '.Nunjucks::out(Nunjucks::get($link, 'text')).'
';
                    $parts[] = '            </a>
          </li>
';
                }
                $parts[] = '        </ul>
';
            }
            if ((Nunjucks::truthy(Nunjucks::get($meta, 'text')) || Nunjucks::truthy(Nunjucks::get($meta, 'html')))) {
                $parts[] = '        <div class="govuk-footer__meta-custom">
';
                $parts[] = '          '.Nunjucks::contentIndent($meta, 'html', 'text', 10).'
';
                $parts[] = '        </div>
';
            }
        }
        $licence = $p->get('contentLicence');
        if ($licence !== null) {
            $parts[] = '          '.self::FOOTER_LICENCE_LOGO.'
';
            $parts[] = '          <span class="govuk-footer__licence-description">
';
            if ((Nunjucks::truthy(Nunjucks::get($licence, 'html')) || Nunjucks::truthy(Nunjucks::get($licence, 'text')))) {
                $parts[] = '            '.Nunjucks::contentIndent($licence, 'html', 'text', 12).'
';
            } else {
                $parts[] = '            All content is available under the
            <a
              class="govuk-footer__link"
              href="https://www.nationalarchives.gov.uk/doc/open-government-licence/version/3/"
              rel="license"
            >Open Government Licence v3.0</a>, except where otherwise stated
';
            }
            $parts[] = '          </span>
';
        }
        $parts[] = '      </div>
';
        $parts[] = '      <div class="govuk-footer__meta-item">
';
        $parts[] = '        <a
          class="govuk-footer__link govuk-footer__copyright-logo"
          href="'.self::FOOTER_COPYRIGHT_HREF.'"
        >
';
        $copyright_ = $p->get('copyright');
        if ((Nunjucks::truthy(Nunjucks::get($copyright_, 'html')) || Nunjucks::truthy(Nunjucks::get($copyright_, 'text')))) {
            $parts[] = '          '.Nunjucks::contentIndent($copyright_, 'html', 'text', 10).'
';
        } else {
            $parts[] = '          © Crown copyright
';
        }
        $parts[] = '        </a>
      </div>
';
        $parts[] = '    </div>
  </div>
</div>';

        return implode('', $parts);
    }

    public static function renderBreadcrumbs(Params $p): string
    {
        $classNames = 'govuk-breadcrumbs';
        $classes = $p->get('classes');
        if (Nunjucks::truthy($classes)) {
            $classNames .= ' '.Nunjucks::str($classes);
        }
        if (Nunjucks::truthy($p->get('collapseOnMobile'))) {
            $classNames .= ' govuk-breadcrumbs--collapse-on-mobile';
        }
        $parts = [];
        $parts[] = '<nav class="'.Nunjucks::escape($classNames).'"'.Attributes::render($p->get('attributes')).' aria-label="'.Nunjucks::out(Nunjucks::def($p->get('labelText'), 'Breadcrumb')).'">
';
        $parts[] = '  <ol class="govuk-breadcrumbs__list">
';
        foreach (Nunjucks::items($p->get('items')) as $item) {
            $href = Nunjucks::get($item, 'href');
            if (Nunjucks::truthy($href)) {
                $parts[] = '    <li class="govuk-breadcrumbs__list-item">
';
                $parts[] = '      <a class="govuk-breadcrumbs__link" href="'.Nunjucks::out($href).'"'.Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::content($item, 'html', 'text').'</a>
';
                $parts[] = '    </li>
';
            } else {
                $parts[] = '    <li class="govuk-breadcrumbs__list-item" aria-current="page">'.Nunjucks::content($item, 'html', 'text').'</li>
';
            }
        }
        $parts[] = '  </ol>
</nav>';

        return implode('', $parts);
    }

    public static function renderLanguageNavigation(Params $p): string
    {
        $parts = [];
        $parts[] = '<nav class="govuk-language-navigation'.Nunjucks::classesIf($p->get('classes')).'"'.Attributes::render($p->get('attributes')).' aria-label="'.Nunjucks::out(Nunjucks::def($p->get('ariaLabel'), 'Language')).'">
';
        $parts[] = '  <ul class="govuk-language-navigation__list">
';
        foreach (Nunjucks::items($p->get('items')) as $item) {
            $href = Nunjucks::get($item, 'href');
            $parts[] = '    <li class="govuk-language-navigation__list-item">
';
            if ((Nunjucks::truthy(Nunjucks::get($item, 'current')) || ! Nunjucks::truthy($href))) {
                $parts[] = '      <span class="govuk-language-navigation__text'.Nunjucks::classesIf(Nunjucks::get($item, 'classes')).'"
        aria-current="true"'.Nunjucks::attributeIf('lang', Nunjucks::get($item, 'lang')).Nunjucks::attributeIf('dir', Nunjucks::get($item, 'dir')).Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::content($item, 'html', 'text').'</span>
';
            } else {
                $hrefLang = Nunjucks::get($item, 'hrefLang');
                if (! Nunjucks::truthy($hrefLang)) {
                    $hrefLang = Nunjucks::get($item, 'lang');
                }
                $parts[] = '      <a class="govuk-language-navigation__link'.Nunjucks::classesIf(Nunjucks::get($item, 'classes')).'" href="'.Nunjucks::out($href).'" rel="alternate"'.Nunjucks::attributeIf('lang', Nunjucks::get($item, 'lang')).Nunjucks::attributeIf('hreflang', $hrefLang).Nunjucks::attributeIf('dir', Nunjucks::get($item, 'dir')).Attributes::render(Nunjucks::get($item, 'attributes')).'>'.Nunjucks::content($item, 'html', 'text');
                $description = Nunjucks::get($item, 'languageDescriptionText');
                if (Nunjucks::truthy($description)) {
                    $parts[] = '<span class="govuk-visually-hidden"> '.Nunjucks::out($description).'</span>';
                }
                $parts[] = '      </a>
';
            }
            $parts[] = '    </li>
';
        }
        $parts[] = '  </ul>
</nav>';

        return implode('', $parts);
    }

    public static function renderServiceNavigation(Params $p): string
    {
        $slots = $p->get('slots');
        $menuButtonText = Nunjucks::defTruthy($p->get('menuButtonText'), 'Menu');
        $navigationId = Nunjucks::out(Nunjucks::defTruthy($p->get('navigationId'), 'navigation'));
        $endSlot = Nunjucks::get($slots, 'end');
        $endSlotHtml = Nunjucks::get($endSlot, 'html');
        if (is_string($endSlot)) {
            $endSlotHtml = $endSlot;
        }
        $endSlotIsObject = ($endSlot instanceof Params);
        $endSlotInline = ($endSlotIsObject && Nunjucks::looseEq($endSlot->get('align'), 'inline'));
        $commonAttributes = 'class="govuk-service-navigation'.Nunjucks::classesIf($p->get('classes')).'"
'.'data-module="govuk-service-navigation"'.Attributes::render($p->get('attributes')).'
';
        $inner = [];
        $inner[] = '  <div class="govuk-width-container'.Nunjucks::flagIf(' govuk-service-navigation__inlining-container', $endSlotInline).'">

    ';
        $start = Nunjucks::get($slots, 'start');
        if (Nunjucks::truthy($start)) {
            $inner[] = Nunjucks::str($start);
        }
        $inner[] = '<div class="govuk-service-navigation__container">
      
';
        $serviceName = $p->get('serviceName');
        if (Nunjucks::truthy($serviceName)) {
            $inner[] = '        <span class="govuk-service-navigation__service-name">
';
            $serviceUrl = $p->get('serviceUrl');
            if (Nunjucks::truthy($serviceUrl)) {
                $inner[] = '            <a href="'.Nunjucks::out($serviceUrl).'" class="govuk-service-navigation__link">
';
                $inner[] = '              '.Nunjucks::out($serviceName).'
            </a>
';
            } else {
                $inner[] = '            <span class="govuk-service-navigation__text">'.Nunjucks::out($serviceName).'</span>
';
            }
            $inner[] = '        </span>
';
        }
        $inner[] = '
      
';
        $navigationItems = [];
        foreach (Nunjucks::items($p->get('navigation')) as $item) {
            if (Nunjucks::truthy($item)) {
                $navigationItems[] = $item;
            }
        }
        $collapse = Nunjucks::truthy(Nunjucks::def($p->get('collapseNavigationOnMobile'), count($navigationItems) > 1));
        $navigationStart = Nunjucks::get($slots, 'navigationStart');
        $navigationEnd = Nunjucks::get($slots, 'navigationEnd');
        if ((count($navigationItems) > 0 || Nunjucks::truthy($navigationStart) || Nunjucks::truthy($navigationEnd))) {
            $inner[] = '        <nav aria-label="'.Nunjucks::out(Nunjucks::defTruthy($p->get('navigationLabel'), $menuButtonText)).'" class="govuk-service-navigation__wrapper'.Nunjucks::classesIf($p->get('navigationClasses')).'">
';
            if ($collapse) {
                $menuButtonLabel = $p->get('menuButtonLabel');
                $ariaLabel = '';
                if ((Nunjucks::truthy($menuButtonLabel) && ! Nunjucks::looseEq($menuButtonLabel, $menuButtonText))) {
                    $ariaLabel = ' aria-label="'.Nunjucks::out($menuButtonLabel).'"';
                }
                $inner[] = '          <button type="button" class="govuk-service-navigation__toggle govuk-js-service-navigation-toggle" aria-controls="'.$navigationId.'"'.$ariaLabel.' hidden aria-hidden="true">
';
                $inner[] = '            '.Nunjucks::out($menuButtonText).'
          </button>
';
            }
            $inner[] = '
          <ul class="govuk-service-navigation__list" id="'.$navigationId.'" >

            ';
            if (Nunjucks::truthy($navigationStart)) {
                $inner[] = Nunjucks::str($navigationStart);
            }
            $inner[] = '
';
            foreach ($navigationItems as $item) {
                $active = (Nunjucks::truthy(Nunjucks::get($item, 'active')) || Nunjucks::truthy(Nunjucks::get($item, 'current')));
                if ($active) {
                    $linkInner = '
                                    
                  <strong class="govuk-service-navigation__active-fallback">'.Nunjucks::content($item, 'html', 'text').'</strong>
';
                } else {
                    $linkInner = '
                                    
'.Nunjucks::content($item, 'html', 'text');
                }
                $ariaCurrent = '';
                if ($active) {
                    $value = 'true';
                    if (Nunjucks::truthy(Nunjucks::get($item, 'current'))) {
                        $value = 'page';
                    }
                    $ariaCurrent = ' aria-current="'.$value.'"';
                }
                $inner[] = '              
';
                $inner[] = '              <li class="govuk-service-navigation__item'.Nunjucks::flagIf(' govuk-service-navigation__item--active', $active).'">
';
                $href = Nunjucks::get($item, 'href');
                if (Nunjucks::truthy($href)) {
                    $inner[] = '                  <a class="govuk-service-navigation__link" href="'.Nunjucks::out($href).'"'.$ariaCurrent.Attributes::render(Nunjucks::get($item, 'attributes')).'>'.$linkInner.'
                  </a>
';
                } elseif ((Nunjucks::truthy(Nunjucks::get($item, 'html')) || Nunjucks::truthy(Nunjucks::get($item, 'text')))) {
                    $inner[] = '                  <span class="govuk-service-navigation__text"'.$ariaCurrent.'>'.$linkInner.'
                  </span>
';
                }
                $inner[] = '              </li>

';
            }
            $inner[] = '            ';
            if (Nunjucks::truthy($navigationEnd)) {
                $inner[] = Nunjucks::str($navigationEnd);
            }
            $inner[] = '</ul>
        </nav>
';
        }
        $inner[] = '    </div>

    ';
        if (Nunjucks::truthy($endSlotHtml)) {
            $inner[] = Nunjucks::str($endSlotHtml);
        }
        $inner[] = '</div>
';
        if ((Nunjucks::truthy($p->get('serviceName')) || Nunjucks::truthy(Nunjucks::get($slots, 'start')) || Nunjucks::truthy($endSlotHtml))) {
            return '  <section aria-label="'.Nunjucks::out(Nunjucks::def($p->get('ariaLabel'), 'Service information')).'" '.$commonAttributes.'>
    '.implode('', $inner).'
  </section>
';
        }

        return '  <div '.$commonAttributes.'>
    '.implode('', $inner).'
  </div>
';
    }

    public static function renderPagination(Params $p): string
    {
        [$previous, $next_] = [$p->get('previous'), $p->get('next')];
        $blockLevel = (! Nunjucks::truthy($p->get('items')) && (Nunjucks::truthy($next_) || Nunjucks::truthy($previous)));
        $parts = [];
        $parts[] = '<nav class="govuk-pagination'.Nunjucks::flagIf(' govuk-pagination--block', $blockLevel).Nunjucks::classesIf($p->get('classes')).'" aria-label="'.Nunjucks::out(Nunjucks::defTruthy($p->get('landmarkLabel'), 'Pagination')).'"'.Attributes::render($p->get('attributes')).'>
';
        if ((Nunjucks::truthy($previous) && Nunjucks::truthy(Nunjucks::get($previous, 'href')))) {
            $parts[] = self::paginationArrowLink($previous, 'prev', $blockLevel, self::paginationLinkLabel($previous, 'Previous'));
        }
        $entries = $p->get('items');
        if (Nunjucks::truthy($entries)) {
            $parts[] = '  <ul class="govuk-pagination__list">
';
            foreach (Nunjucks::items($entries) as $item) {
                if (($item === null || Nunjucks::length($item) === 0)) {
                    continue;
                }
                $parts[] = '      '.Nunjucks::indent(self::paginationPageItem($item), 2, false).'
';
            }
            $parts[] = '  </ul>
';
        }
        if ((Nunjucks::truthy($next_) && Nunjucks::truthy(Nunjucks::get($next_, 'href')))) {
            $parts[] = self::paginationArrowLink($next_, 'next', $blockLevel, self::paginationLinkLabel($next_, 'Next'));
        }
        $parts[] = '</nav>';

        return implode('', $parts);
    }

    private static function paginationLinkLabel(mixed $link, string $fallback): string
    {
        [$html, $text] = [Nunjucks::get($link, 'html'), Nunjucks::get($link, 'text')];
        if (Nunjucks::truthy($html)) {
            return Nunjucks::trim(Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 8, false));
        }
        if (Nunjucks::truthy($text)) {
            return Nunjucks::out($text);
        }

        return $fallback.'<span class="govuk-visually-hidden"> page</span>';
    }

    private static function paginationArrowLink(mixed $link, string $kind, bool $blockLevel, string $label): string
    {
        $arrow = self::PAGINATION_ARROW_NEXT;
        if ($kind === 'prev') {
            $arrow = self::PAGINATION_ARROW_PREVIOUS;
        }
        $parts = [];
        $parts[] = '  <div class="govuk-pagination__'.$kind.'">
';
        $parts[] = '    <a class="govuk-link govuk-pagination__link" href="'.Nunjucks::out(Nunjucks::get($link, 'href')).'" rel="'.$kind.'"'.Attributes::render(Nunjucks::get($link, 'attributes')).'>
';
        if (($blockLevel || $kind === 'prev')) {
            $parts[] = Nunjucks::indent($arrow, 4, true).'
';
        }
        $labelText = Nunjucks::get($link, 'labelText');
        $parts[] = '      <span class="govuk-pagination__link-title'.Nunjucks::flagIf(' govuk-pagination__link-title--decorated', ($blockLevel && ! Nunjucks::truthy($labelText))).'">
        '.$label.'
      </span>
';
        if ((Nunjucks::truthy($labelText) && $blockLevel)) {
            $parts[] = '      <span class="govuk-visually-hidden">:</span>
';
            $parts[] = '      <span class="govuk-pagination__link-label">'.Nunjucks::out($labelText).'</span>
';
        }
        if ((! $blockLevel && $kind === 'next')) {
            $parts[] = Nunjucks::indent($arrow, 4, true).'
';
        }
        $parts[] = '    </a>
  </div>
';

        return implode('', $parts);
    }

    private static function paginationPageItem(mixed $item): string
    {
        $parts = [];
        $parts[] = '<li class="govuk-pagination__item'.Nunjucks::flagIf(' govuk-pagination__item--current', Nunjucks::get($item, 'current')).Nunjucks::flagIf(' govuk-pagination__item--ellipsis', Nunjucks::get($item, 'ellipsis')).'">
';
        if (Nunjucks::truthy(Nunjucks::get($item, 'ellipsis'))) {
            $parts[] = '    &ctdot;
';
        } else {
            $parts[] = '    <a class="govuk-link govuk-pagination__link" href="'.Nunjucks::out(Nunjucks::get($item, 'href')).'" aria-label="'.Nunjucks::out(Nunjucks::def(Nunjucks::get($item, 'visuallyHiddenText'), 'Page '.Nunjucks::str(Nunjucks::get($item, 'number')))).'"'.Nunjucks::flagIf(' aria-current="page"', Nunjucks::get($item, 'current')).Attributes::render(Nunjucks::get($item, 'attributes')).'>
';
            $parts[] = '      '.Nunjucks::out(Nunjucks::get($item, 'number')).'
    </a>
';
        }
        $parts[] = '  </li>';

        return implode('', $parts);
    }

    public static function renderCookieBanner(Params $p): string
    {
        $parts = [];
        $parts[] = '<div class="govuk-cookie-banner'.Nunjucks::classesIf($p->get('classes')).'" data-nosnippet role="region" aria-label="'.Nunjucks::out(Nunjucks::defTruthy($p->get('ariaLabel'), 'Cookie banner')).'"'.Nunjucks::flagIf(' hidden', $p->get('hidden')).Attributes::render($p->get('attributes')).'>
';
        foreach (Nunjucks::items($p->get('messages')) as $message) {
            $parts[] = '  <div class="govuk-cookie-banner__message'.Nunjucks::classesIf(Nunjucks::get($message, 'classes')).' govuk-width-container"'.Nunjucks::attributeIf('role', Nunjucks::get($message, 'role')).Attributes::render(Nunjucks::get($message, 'attributes')).Nunjucks::flagIf(' hidden', Nunjucks::get($message, 'hidden')).'>

';
            $parts[] = '    <div class="govuk-grid-row">
';
            $parts[] = '      <div class="govuk-grid-column-two-thirds">
';
            if ((Nunjucks::truthy(Nunjucks::get($message, 'headingHtml')) || Nunjucks::truthy(Nunjucks::get($message, 'headingText')))) {
                $parts[] = '        <h2 class="govuk-cookie-banner__heading govuk-heading-m">
';
                $parts[] = '          '.Nunjucks::contentIndent($message, 'headingHtml', 'headingText', 10).'
';
                $parts[] = '        </h2>
';
            }
            $parts[] = '        <div class="govuk-cookie-banner__content">
';
            [$html, $text] = [Nunjucks::get($message, 'html'), Nunjucks::get($message, 'text')];
            if (Nunjucks::truthy($html)) {
                $parts[] = '          '.Nunjucks::indent(Nunjucks::trim(Nunjucks::str($html)), 10, false).'
';
            } elseif (Nunjucks::truthy($text)) {
                $parts[] = '          <p class="govuk-body">'.Nunjucks::out($text).'</p>
';
            }
            $parts[] = '        </div>
      </div>
    </div>

';
            $actions = Nunjucks::items(Nunjucks::get($message, 'actions'));
            $rawActions = Nunjucks::get($message, 'actions');
            if ((is_array($rawActions) && array_is_list($rawActions))) {
                $parts[] = '    <div class="govuk-button-group">
';
                foreach ($actions as $action) {
                    $parts[] = '      '.Nunjucks::indent(Nunjucks::trim(self::cookieBannerAction($action)), 6, false).'
';
                }
                $parts[] = '    </div>
';
            }
            $parts[] = '
  </div>
';
        }
        $parts[] = '</div>';

        return implode('', $parts);
    }

    private static function cookieBannerAction(mixed $action): string
    {
        $href = Nunjucks::get($action, 'href');
        if ((! Nunjucks::truthy($href) || Nunjucks::str(Nunjucks::get($action, 'type')) === 'button')) {
            return Button::renderButton(Params::make('text', Nunjucks::get($action, 'text'), 'type', Nunjucks::defTruthy(Nunjucks::get($action, 'type'), 'button'), 'name', Nunjucks::get($action, 'name'), 'value', Nunjucks::get($action, 'value'), 'classes', Nunjucks::get($action, 'classes'), 'href', $href, 'attributes', Nunjucks::get($action, 'attributes')));
        }

        return '<a class="govuk-link'.Nunjucks::classesIf(Nunjucks::get($action, 'classes')).'" href="'.Nunjucks::out($href).'"'.Attributes::render(Nunjucks::get($action, 'attributes')).'>'.Nunjucks::out(Nunjucks::get($action, 'text')).'</a>';
    }
}
