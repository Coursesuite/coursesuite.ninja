<?php use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array(            'equals' => function($arg1, $arg2, $options) {
                    if (strcasecmp((string)$arg1, (string)$arg2) == 0) {
                        return $options['fn']();
                    } else if (isset($options['inverse'])) {
                        return $options['inverse']();
                    }
                  },
);
    $partials = array();
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => true,
            'prop' => false,
            'method' => false,
            'lambda' => false,
            'mustlok' => false,
            'mustlam' => false,
            'echo' => false,
            'partnc' => false,
            'knohlp' => false,
            'debug' => isset($options['debug']) ? $options['debug'] : 1,
        ),
        'constants' => array(),
        'helpers' => isset($options['helpers']) ? array_merge($helpers, $options['helpers']) : $helpers,
        'partials' => isset($options['partials']) ? array_merge($partials, $options['partials']) : $partials,
        'scopes' => array(),
        'sp_vars' => isset($options['data']) ? array_merge(array('root' => $in), $options['data']) : array('root' => $in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '\LightnCandy\Runtime',
    );
    
    return '<article>
'.LR::sec($cx, ((is_array($in) && isset($in['section'])) ? $in['section'] : null), null, $in, true, function($cx, $in) {return '    '.LR::hbch($cx, 'equals', array(array(((is_array($in) && isset($in['visible'])) ? $in['visible'] : null),1),array()), $in, false, function($cx, $in) {return '<section class=\''.LR::encq($cx, ((is_array($in) && isset($in['cssclass'])) ? $in['cssclass'] : null)).'\'>
<div class="store-items">
        '.((LR::ifvar($cx, ((is_array($in) && isset($in['label'])) ? $in['label'] : null), false)) ? '<h3>'.LR::encq($cx, ((is_array($in) && isset($in['label'])) ? $in['label'] : null)).'</h3>' : '').'
        '.((LR::ifvar($cx, ((is_array($in) && isset($in['epiphet'])) ? $in['epiphet'] : null), false)) ? '<h4>'.LR::encq($cx, ((is_array($in) && isset($in['epiphet'])) ? $in['epiphet'] : null)).'</h4>' : '').'
        '.((LR::ifvar($cx, ((is_array($in) && isset($in['html_pre'])) ? $in['html_pre'] : null), false)) ? ''.LR::raw($cx, ((is_array($in) && isset($in['html_pre'])) ? $in['html_pre'] : null)).'' : '').'
        <nav class=\'app-section-names\'>
'.LR::sec($cx, ((is_array($in) && isset($in['apps'])) ? $in['apps'] : null), null, $in, true, function($cx, $in) {return '            <div class=\'tile app\'>
                <figure style="background-image:url('.LR::encq($cx, ((is_array($in) && isset($in['icon'])) ? $in['icon'] : null)).')">
                    <img src=\'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7\'>
                    <figcaption>'.LR::encq($cx, ((is_array($in) && isset($in['name'])) ? $in['name'] : null)).'</figcaption>
                </figure>
                '.((LR::ifvar($cx, ((is_array($in) && isset($in['tagline'])) ? $in['tagline'] : null), false)) ? '<div class=\'information\'>
                    '.LR::encq($cx, ((is_array($in) && isset($in['tagline'])) ? $in['tagline'] : null)).'
                </div>' : '').'
                <div class=\'actions\'>
                    '.LR::hbch($cx, 'equals', array(array(((is_array($in) && isset($in['auth_type'])) ? $in['auth_type'] : null),1),array()), $in, false, function($cx, $in) {return '<a href=\''.LR::encq($cx, ((is_array($in) && isset($in['url'])) ? $in['url'] : null)).'\' target=\'_blank\'>Launch</a>';}, function($cx, $in) {return '
                    <a href=\''.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-2]) && is_array($cx['scopes'][count($cx['scopes'])-2]) && isset($cx['scopes'][count($cx['scopes'])-2]['storeurl'])) ? $cx['scopes'][count($cx['scopes'])-2]['storeurl'] : null)).''.LR::encq($cx, ((is_array($in) && isset($in['app_key'])) ? $in['app_key'] : null)).'\'>More info</a>
                    '.((LR::ifvar($cx, ((isset($cx['scopes'][count($cx['scopes'])-2]) && is_array($cx['scopes'][count($cx['scopes'])-2]) && isset($cx['scopes'][count($cx['scopes'])-2]['token'])) ? $cx['scopes'][count($cx['scopes'])-2]['token'] : null), false)) ? '<a href=\''.LR::encq($cx, ((is_array($in) && isset($in['launch'])) ? $in['launch'] : null)).'?token='.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-2]) && is_array($cx['scopes'][count($cx['scopes'])-2]) && isset($cx['scopes'][count($cx['scopes'])-2]['token'])) ? $cx['scopes'][count($cx['scopes'])-2]['token'] : null)).'\' target=\'_blank\' class=\'launch\'>Launch</a>' : '').'';}).'
                </div>
            </div>
';}).'        </nav>
        '.((LR::ifvar($cx, ((is_array($in) && isset($in['html_post'])) ? $in['html_post'] : null), false)) ? ''.LR::raw($cx, ((is_array($in) && isset($in['html_post'])) ? $in['html_post'] : null)).'' : '').'
</div>
    </section>';}).'
';}).'</article>';
}; ?>