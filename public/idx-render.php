<?php use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array();
    $partials = array();
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => false,
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
    
    return '
'.LR::sec($cx, ((isset($in['section']) && is_array($in)) ? $in['section'] : null), null, $in, true, function($cx, $in) {return '    '.((LR::ifvar($cx, ((isset($in['same(visible']) && is_array($in)) ? $in['same(visible'] : null), false)) ? '<section class=\''.htmlentities((string)((isset($in['cssclass']) && is_array($in)) ? $in['cssclass'] : null), ENT_QUOTES, 'UTF-8').'\'>
        '.((LR::ifvar($cx, ((isset($in['label']) && is_array($in)) ? $in['label'] : null), false)) ? '<h3>'.htmlentities((string)((isset($in['label']) && is_array($in)) ? $in['label'] : null), ENT_QUOTES, 'UTF-8').'</h3>' : '').'
        '.((LR::ifvar($cx, ((isset($in['epiphet']) && is_array($in)) ? $in['epiphet'] : null), false)) ? '<h4>'.htmlentities((string)((isset($in['epiphet']) && is_array($in)) ? $in['epiphet'] : null), ENT_QUOTES, 'UTF-8').'</h4>' : '').'
        '.((LR::ifvar($cx, ((isset($in['html']) && is_array($in)) ? $in['html'] : null), false)) ? ''.((isset($in['html']) && is_array($in)) ? $in['html'] : null).'' : '').'
        <nav class=\'app-section-names\'>
'.LR::sec($cx, ((isset($in['apps']) && is_array($in)) ? $in['apps'] : null), null, $in, true, function($cx, $in) {return '            <div class=\'tile app\'>
                <figure>
                    <img src=\''.htmlentities((string)((isset($in['icon']) && is_array($in)) ? $in['icon'] : null), ENT_QUOTES, 'UTF-8').'\'>
                    <figcaption>'.htmlentities((string)((isset($in['name']) && is_array($in)) ? $in['name'] : null), ENT_QUOTES, 'UTF-8').'</figcaption>
                </figure>
                <div class=\'information\'>
                    '.htmlentities((string)((isset($in['tagline']) && is_array($in)) ? $in['tagline'] : null), ENT_QUOTES, 'UTF-8').'
                </div>
                <div class=\'actions\'>
                    <a href=\''.htmlentities((string)((isset($in['storeurl']) && is_array($in)) ? $in['storeurl'] : null), ENT_QUOTES, 'UTF-8').'\'>More info</a>
                    '.((LR::ifvar($cx, ((isset($cx['scopes'][count($cx['scopes'])-2]['token']) && is_array($cx['scopes'][count($cx['scopes'])-2])) ? $cx['scopes'][count($cx['scopes'])-2]['token'] : null), false)) ? '<a href=\''.htmlentities((string)((isset($in['launch']) && is_array($in)) ? $in['launch'] : null), ENT_QUOTES, 'UTF-8').'?token='.htmlentities((string)((isset($cx['scopes'][count($cx['scopes'])-2]['token']) && is_array($cx['scopes'][count($cx['scopes'])-2])) ? $cx['scopes'][count($cx['scopes'])-2]['token'] : null), ENT_QUOTES, 'UTF-8').'\' target=\'_blank\' class=\'launch\'>Launch</a>' : '').'
                </div>
            </div>
';}).'        </nav>
    </section>' : '').'
';}).'';
}; ? >