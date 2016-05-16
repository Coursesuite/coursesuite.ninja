<?php use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array(            'equals' => function($arg1, $arg2, $options) {
                    if (strcasecmp((string)$arg1, (string)$arg2) == 0) {
                        return $options['fn']();
                    } else if (isset($options['inverse'])) {
                        return $options['inverse']();
                    }
                  },
            'dump' => function($arg1) {
                    return print_r($arg1, true);
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
		<form method="post" action="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editApps/'.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'/save">
'.LR::wi($cx, ((is_array($in) && isset($in['data'])) ? $in['data'] : null), null, $in, function($cx, $in) {return '			<div><label>App Key: <input type="text" name="app_key" value="'.LR::encq($cx, ((is_array($in) && isset($in['app_key'])) ? $in['app_key'] : null)).'"></label></div>
			<div><label>Name: <input type="text" name="name" value="'.LR::encq($cx, ((is_array($in) && isset($in['name'])) ? $in['name'] : null)).'"></label></div>
			<div><label>tagline: <input type="text" name="tagline" value="'.LR::encq($cx, ((is_array($in) && isset($in['tagline'])) ? $in['tagline'] : null)).'"></label></div>
			<div><label>icon: <input type="text" name="icon" value="'.LR::encq($cx, ((is_array($in) && isset($in['icon'])) ? $in['icon'] : null)).'"></label></div>
			<div><label>domain url: <input type="text" name="url" value="'.LR::encq($cx, ((is_array($in) && isset($in['url'])) ? $in['url'] : null)).'"></label></div>
			<div><label>launch url: <input type="text" name="launch" value="'.LR::encq($cx, ((is_array($in) && isset($in['launch'])) ? $in['launch'] : null)).'"></label></div>
			<div><label>feed url: <input type="text" name="feed" value="'.LR::encq($cx, ((is_array($in) && isset($in['feed'])) ? $in['feed'] : null)).'"></label></div>
			<div><label>Auth type: <select name="auth_type">
				<option value="0"'.LR::hbch($cx, 'equals', array(array('0',((is_array($in) && isset($in['auth_type'])) ? $in['auth_type'] : null)),array()), $in, false, function($cx, $in) {return ' selected';}).'>Integrated</option>
				<option value="1"'.LR::hbch($cx, 'equals', array(array('1',((is_array($in) && isset($in['auth_type'])) ? $in['auth_type'] : null)),array()), $in, false, function($cx, $in) {return ' selected';}).'>None</option>
				</select></label>
			</div>
			<div>It is:
				<input type="radio" name="active" value="1" '.LR::hbch($cx, 'equals', array(array('1',((is_array($in) && isset($in['active'])) ? $in['active'] : null)),array()), $in, false, function($cx, $in) {return ' checked';}).' id="y"><label for="y">Visible</label>
				<input type="radio" name="active" value="0" '.LR::hbch($cx, 'equals', array(array('0',((is_array($in) && isset($in['active'])) ? $in['active'] : null)),array()), $in, false, function($cx, $in) {return ' checked';}).' id="n"><label for="n">Hidden</label>
			</div>
			<div><label for="description">Description</label><br><textarea name="description" rows=10 cols=80>'.LR::encq($cx, ((is_array($in) && isset($in['description'])) ? $in['description'] : null)).'</textarea></div>
			<div><label for="media">Media</label><br><textarea name="media" rows=5 cols=80>'.LR::encq($cx, ((is_array($in) && isset($in['media'])) ? $in['media'] : null)).'</textarea></div>
			<div><input type="submit" value="Save & Return"></div>
';}).'		</form>
		<h4>Media</h4>
		<ul>
			'.((!LR::ifvar($cx, ((is_array($in) && isset($in['files'])) ? $in['files'] : null), false)) ? '<li>No media found.</li>' : '').'
			'.LR::sec($cx, ((is_array($in) && isset($in['files'])) ? $in['files'] : null), null, $in, true, function($cx, $in) {return '<li><a href="'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['baseurl'])) ? $cx['scopes'][count($cx['scopes'])-1]['baseurl'] : null)).'admin/editApps/'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['id'])) ? $cx['scopes'][count($cx['scopes'])-1]['id'] : null)).'/delete/'.LR::encq($cx, $in).'" title="Delete file now"><i class="cs-trash"></i></a>
								<a href="'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['baseurl'])) ? $cx['scopes'][count($cx['scopes'])-1]['baseurl'] : null)).'img/apps/'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]['data']) && isset($cx['scopes'][count($cx['scopes'])-1]['data']['app_key'])) ? $cx['scopes'][count($cx['scopes'])-1]['data']['app_key'] : null)).'/'.LR::encq($cx, $in).'" data-action="hover-thumb">'.LR::encq($cx, $in).'</a></li>';}).'
		</ul>
		<form method="post" action="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editApps/'.LR::encq($cx, ((is_array($in) && isset($in['id'])) ? $in['id'] : null)).'/upload" enctype="multipart/form-data">
		    <div><label>Upload media: <input type="file" name="imageUpload"></label></div>
		    <div><label>Or specify URL: <input type="text" name="url" placeholder="E.g. youtube url"></label></div>
		    <div><label>Caption: <input type="text" name="caption" placeholder="short caption for media"></label></div>
			<div><input type="submit" value="Upload" name="submit"></div>
		</form>
		'.LR::encq($cx, LR::hbch($cx, 'dump', array(array(((is_array($in['data']) && isset($in['data']['media'])) ? $in['data']['media'] : null)),array()), 'encq', $in)).'
' : '	<h3>Apps</h3>
	<ul>
'.LR::sec($cx, ((is_array($in) && isset($in['apps'])) ? $in['apps'] : null), null, $in, true, function($cx, $in) {return '		<li><a href="'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['baseurl'])) ? $cx['scopes'][count($cx['scopes'])-1]['baseurl'] : null)).'admin/editApps/'.LR::encq($cx, ((is_array($in) && isset($in['app_id'])) ? $in['app_id'] : null)).'/edit">'.LR::encq($cx, ((is_array($in) && isset($in['app_id'])) ? $in['app_id'] : null)).'. '.LR::encq($cx, ((is_array($in) && isset($in['name'])) ? $in['name'] : null)).'</a></li>
';}).'	</ul>
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editApps/0/new">Create a new app</a></p>
').'
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/editApps/">Back to edit apps</a></p>
	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin">Back to admin index</a></p>
 
</article>
';
}; ?>