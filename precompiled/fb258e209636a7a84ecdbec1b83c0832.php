<?php use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array(            'View::AppMatrix' => function($App, $AppTierFeatures, $AppTiers, $UserSubscription, $options) {

        $table = array();
        $colspan = count($AppTiers);
        $table[] = '<table class="app-matrix"><thead>';
        $table[] = "<tr><td></td><td colspan='$colspan'><div id='tier-bracket'>Tiers</div></td></tr>";
        $table[] = '<tr><td></td>';
        foreach ($AppTiers as $tier) {
            $table[] = "<th class='tier-level-" . $tier["tier_level"] . "'>" . $tier["name"] . "</th>";
        }
        $table[] = '</tr></thead><tbody>';

		// app feature matrix
        foreach ($AppTierFeatures as $info) {
            $table[] = '<tr><th>' . $info["feature"] . '</th>';
            foreach ($AppTiers as $tier) {
                $tier_level = (int)$tier["tier_level"];
                $info_level = (int)$info["level"];
                $value = $info["mismatch_label"];
                if ($tier_level >= $info_level) {
                    $value = $info["match_label"];
                }
                $table[] = "<td class='tier-level-$tier_level'>$value</td>";
            }
            $table[] = '</tr>';
        }
        $table[] = '</tbody><tfoot>';

		// price in footer
        $table[] = '<tr class="price fill"><th>Price (USD):</th>';
        foreach ($AppTiers as $tier) {
	        $table[] = '<td>$' . $tier["price"] . ' /' . $tier["period"] . '</td>';
	    }
	    $table[] = '</tr>';

		// purchase / launch buttons
        $table[] = '<tr class="fill"><th></th>';
        if (Session::userIsLoggedIn()) {
            foreach ($AppTiers as $tier) {
		        $label = 'Subscribe';
		        $button_url = trim($tier["store_url"]) . "?referrer=" . Text::base64enc(Encryption::encrypt(Session::CurrentUserId())) . "&mode=test";
		        $class_name = '';
		        if (!empty($UserSubscription)) {
			        $user_tier_level = $UserSubscription[0]["tier"]["tier_id"];
			        if ($tier["tier_id"] == $user_tier_level) {
				        $label = 'Launch App';
				        $class_name = 'current-tier';
				        $button_url = AppModel::getLaunchUrl($App["app_id"]);
			        } else if ($tier["tier_id"] < $user_tier_level) {
				        $label = 'Downgrade';
			        } else if ($tier["tier_id"] > $user_tier_level) {
				        $label = 'Upgrade';
			        }
		        }
                $table[] = "<td><a href='$button_url' class='$class_name'>$label</a></td>";
            }
        } else {
            $table[] = "<td colspan='$colspan'><a href='" . Config::get('URL') . "login/'>Please log in to subscribe</a></td>";
        }
        $table[] = '</tr>';
        
        // caveat
        $table[] = "<tr><td></td><td colspan='$colspan'><p>This product is part of a paid subscription that offers multiple products. Subscriptions are charged monthly until cancelled. You can change your tier at any time.</p><div class='text-center'><img src='/img/fastspring.png'></div></td></tr>";
        $table[] = '</tfoot></table>';
        return implode('', $table);
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
    
    return '<section class="store-item-specific">

<nav class="crumb">
    <a href="/" title="Head back to the storefront"><i class="cs-shop"></i></a> &hellip;
    <a href="/store/info/'.LR::encq($cx, ((is_array($in['App']) && isset($in['App']['app_key'])) ? $in['App']['app_key'] : null)).'">'.LR::encq($cx, ((is_array($in['App']) && isset($in['App']['name'])) ? $in['App']['name'] : null)).'</a>
</nav>

<section class="info">
    '.((LR::ifvar($cx, ((is_array($in['App']) && isset($in['App']['media'])) ? $in['App']['media'] : null), false)) ? '<div class="media">
        <div class="slide-wrapper">
        	<div class="current_slide viewport"><div id="current_slide">[no slides yet]</div></div>
            <div class="slide_controls viewport"><nav id="slide_controls">[...]</nav></div>
        </div>
    </div>' : '').'
    '.((LR::ifvar($cx, ((is_array($in) && isset($in['AppTierFeatures'])) ? $in['AppTierFeatures'] : null), false)) ? '<div class="tierinfo">
        '.LR::raw($cx, LR::hbch($cx, 'View::AppMatrix', array(array(((is_array($in) && isset($in['App'])) ? $in['App'] : null),((is_array($in) && isset($in['AppTierFeatures'])) ? $in['AppTierFeatures'] : null),((is_array($in) && isset($in['AppTiers'])) ? $in['AppTiers'] : null),((is_array($in) && isset($in['UserSubscription'])) ? $in['UserSubscription'] : null)),array()), 'raw', $in)).'
    </div>' : '').'
</section>

</section>

<section class="store-item-description app-'.LR::encq($cx, ((is_array($in['App']) && isset($in['App']['app_key'])) ? $in['App']['app_key'] : null)).'">
<article>'.LR::raw($cx, ((is_array($in['App']) && isset($in['App']['description'])) ? $in['App']['description'] : null)).'</article>
</section>

<script type="text/javascript">var slides = '.((LR::ifvar($cx, ((is_array($in['App']) && isset($in['App']['media'])) ? $in['App']['media'] : null), false)) ? ''.LR::raw($cx, ((is_array($in['App']) && isset($in['App']['media'])) ? $in['App']['media'] : null)).'' : 'undefined').';</script>';
}; ?>