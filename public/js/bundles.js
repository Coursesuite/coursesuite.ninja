$(document).ready(function() {
	var menuTabs = document.getElementsByClassName('bundle-sidebar-item');
	for (var i = 0; i < menuTabs.length; i++) {
		var item = menuTabs[i];
		// Add functions to the menu tabs
		item.onclick = function(){
			// shrinkIcons();
			showHideBundle(this);

			showHideTab(menuTabs, this);
		};
	}

	$(".fancybox").fancybox({
		fitToView	: false,
		autoSize	: true,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		type        : 'inline',
	});
});

function showHideBundle(clicked) {
	var bundles = document.getElementsByClassName('bundle');
	// Hide other bundles
	for (var i = 0; i < bundles.length; i++) {
		bundles[i].style.display = 'none';
	}
	// Show selected bundle
	var showBundle = document.getElementById(clicked.id.split('-').pop());
	showBundle.style.display = 'block';
}

function showHideTab(menuTabs, clicked) {
	for (var i = 0; i < menuTabs.length; i++) {
		menuTabs[i].style.borderRight = '2px solid #cecece';
		setGradient(menuTabs[i]);
	}
	clicked.style.borderRight = 'none';
	clicked.style.backgroundImage = 'url(http://r.coursesuite.ninja/squairy_light.png)';
}

function setGradient(element) {
	element.style.backgroundImage = '';
	element.style.background = '#ffffff';
	element.style.background = '-moz-linear-gradient(left,  transparent 27%, #e1e1e1 100%)';
	element.style.background = '-webkit-linear-gradient(left, transparent 27%,#e1e1e1 100%)';
	element.style.background = 'linear-gradient(to right,  transparent 27%,#e1e1e1 100%)';
	element.style.filter = "progid:DXImageTransform.Microsoft.gradient( startColorstr='transparent', endColorstr='#e1e1e1',GradientType=1 )";
}
// ------------------ shrink grow anims ----------------------------------------------

function whichTransitionEvent(){
    var t;
    var el = document.createElement('fakeelement');
    var transitions = {
      'transition':'transitionend',
      'OTransition':'oTransitionEnd',
      'MozTransition':'transitionend',
      'WebkitTransition':'webkitTransitionEnd'
    }

    for(t in transitions){
        if( el.style[t] !== undefined ){
            return transitions[t];
        }
    }
}

/* shrinks all active (large) icons over 1 second */
function shrinkIcons(showBundle) {
	var icons = document.getElementsByClassName('bundle-icon');
	var transitionEnd = whichTransitionEvent();
	// icons[0].addEventListener(transitionEnd, showBundle, false);
	for (i = 0; i < icons.length; i++) {
		icons[i].style.setProperty('-webkit-transition', '1s ease-in-out');
		icons[i].style.width = '40px';
		icons[i].style.height = '40px';
		icons[i].style.opacity = '0.2';
	}	
}

function growIcons() {
	var icons = document.getElementsByClassName('bundle-icon');
	var transitionEnd = whichTransitionEvent();

	for (i = 0; i < icons.length; i++) {
		icons[i].style.setProperty('-webkit-transition', '1s ease-in-out');
		icons[i].style.width = '100px';
		icons[i].style.height = '100px';
		icons[i].style.opacity = '1';
	}

}

function removeAnim() {
	var icons = document.getElementsByClassName('bundle-icon');
	for (var i =0; i < icons.length; i++) {
		icons[i].style.removeProperty('-webkit-transition', '1s ease-in-out');
	}
}