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
    
    return '<article class="system-log">

	<form onsubmit="return false;">
		<label>Digest user: <select onchange="document.location=this.value;"><option value="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin/showLog">any</option>
		'.LR::sec($cx, ((is_array($in) && isset($in['digest_users'])) ? $in['digest_users'] : null), null, $in, true, function($cx, $in) {return '<option value="'.LR::encq($cx, ((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['baseurl'])) ? $cx['scopes'][count($cx['scopes'])-1]['baseurl'] : null)).'admin/showLog/user/'.LR::encq($cx, ((is_array($in) && isset($in['digest_user'])) ? $in['digest_user'] : null)).'"'.LR::hbch($cx, 'equals', array(array(((is_array($in) && isset($in['digest_user'])) ? $in['digest_user'] : null),((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['filter_value'])) ? $cx['scopes'][count($cx['scopes'])-1]['filter_value'] : null)),array()), $in, false, function($cx, $in) {return ' selected';}).'>'.LR::encq($cx, LR::hbch($cx, 'dump', array(array(((is_array($in) && isset($in['digest_user'])) ? $in['digest_user'] : null)),array()), 'encq', $in)).'</option>';}).'
		</select></label>
		
		<label>dates after: <input type="text" class=flatpickr data-maxdate=today value="'.LR::encq($cx, ((is_array($in) && isset($in['filter_value'])) ? $in['filter_value'] : null)).'" data-syslog=true>  
	</form>

	<table>
		<thead>
			<tr><th>method</th><th>digest user</th><th>date</th><th>message</th><th>param0</th><th>param1</th><th>param2</th><th>param3</th></tr>
		</thead>
		<tbody>
'.LR::sec($cx, ((is_array($in) && isset($in['messages'])) ? $in['messages'] : null), null, $in, true, function($cx, $in) {return '			<tr>
				<td>'.LR::encq($cx, ((is_array($in) && isset($in['method_name'])) ? $in['method_name'] : null)).'</td>
				<td>'.LR::encq($cx, ((is_array($in) && isset($in['digest_user'])) ? $in['digest_user'] : null)).'</td>
				<td>'.LR::encq($cx, ((is_array($in) && isset($in['added'])) ? $in['added'] : null)).'</td>
				<td>'.LR::encq($cx, ((is_array($in) && isset($in['message'])) ? $in['message'] : null)).'</td>
				<td class="pre">'.LR::encq($cx, ((is_array($in) && isset($in['param0'])) ? $in['param0'] : null)).'</td>
				<td class="pre">'.LR::encq($cx, ((is_array($in) && isset($in['param1'])) ? $in['param1'] : null)).'</td>
				<td class="pre">'.LR::encq($cx, ((is_array($in) && isset($in['param2'])) ? $in['param2'] : null)).'</td>
				<td class="pre">'.LR::encq($cx, ((is_array($in) && isset($in['param3'])) ? $in['param3'] : null)).'</td>
			</tr>
';}).'		</tbody>
	</table>

	<p><a href="'.LR::encq($cx, ((is_array($in) && isset($in['baseurl'])) ? $in['baseurl'] : null)).'admin">Back to admin index</a></p>
 
</article>';
}; ?>