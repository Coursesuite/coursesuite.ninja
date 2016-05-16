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
    
    return '<article class="system-sections">
'.((LR::ifvar($cx, ((is_array($in) && isset($in['action'])) ? $in['action'] : null), false)) ? '		<h3>Editing record '.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'.</h3>
		<form method="post" action="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editSections/'.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'/save">
'.LR::wi($cx, ((is_array($in) && isset($in['data'])) ? $in['data'] : null), null, $in, function($cx, $in) {return '			<div><label>Label: <input type="text" name="label" value="'.LR::encq($cx, ((is_array($in) && isset($in['label'])) ? $in['label'] : null)).'"></label></div>
			<div><label>Epiphet: <input type="text" name="epiphet" value="'.LR::encq($cx, ((is_array($in) && isset($in['epiphet'])) ? $in['epiphet'] : null)).'"></label></div>
			<div><label>CSS Class: <input type="text" name="cssclass" value="'.LR::encq($cx, ((is_array($in) && isset($in['cssclass'])) ? $in['cssclass'] : null)).'"></label></div>
			<div>It is:
				<input type="radio" name="visible" value="1" '.LR::hbch($cx, 'equals', array(array('1',((is_array($in) && isset($in['visible'])) ? $in['visible'] : null)),array()), $in, false, function($cx, $in) {return ' checked';}).' id="y"><label for="y">Visible</label>
				<input type="radio" name="visible" value="0" '.LR::hbch($cx, 'equals', array(array('0',((is_array($in) && isset($in['visible'])) ? $in['visible'] : null)),array()), $in, false, function($cx, $in) {return ' checked';}).' id="n"><label for="n">Hidden</label>
			</div>
			<div><label>Order: <input type="number" min="1" max="999" step="1" value="'.LR::encq($cx, ((is_array($in) && isset($in['sort'])) ? $in['sort'] : null)).'"></label></div>
			<div><label for="htmlpre">HTML Pre</label><br><textarea name="html_pre" rows=10 cols=80>'.LR::encq($cx, ((is_array($in) && isset($in['html_pre'])) ? $in['html_pre'] : null)).'</textarea></div>
			<div><label for="htmlpost">HTML Post</label><br><textarea name="html_post" rows=10 cols=80>'.LR::encq($cx, ((is_array($in) && isset($in['html_post'])) ? $in['html_post'] : null)).'</textarea></div>
			<div><input type="submit" value="Save & Return"></div>
';}).'		</form>
' : '	<h3>Store sections</h3>
	<p>shown in current sort order; drag to change order, edit to change data</p>
	<ul>
'.LR::sec($cx, ((is_array($in) && isset($in['sections'])) ? $in['sections'] : null), null, $in, true, function($cx, $in) {return '		<li><a href="'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['baseurl'])) ? $cx['scopes'][count($cx['scopes'])-1]['baseurl'] : null)).'admin/editSections/'.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'/edit">'.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'. '.LR::encq($cx, ((is_array($in) && isset($in['label'])) ? $in['label'] : null)).' ('.LR::encq($cx, ((is_array($in) && isset($in['epiphet'])) ? $in['epiphet'] : null)).')</a></li>
';}).'	</ul>
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editSections/0/new">Create a new section</a></p>
').'
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editSections/">Back to edit sections</a></p>
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin">Back to admin index</a></p>
 
</article>';
}; ?>