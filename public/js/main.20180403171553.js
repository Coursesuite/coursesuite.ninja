/*! Hammer.JS - v2.0.8 - 2016-04-23
 * http://hammerjs.github.io/
 *
 * Copyright (c) 2016 Jorik Tangelder;
 * Licensed under the MIT license */
function copyToClipboard(t){var e=t.textContent
if(window.clipboardData&&window.clipboardData.setData)return clipboardData.setData("Text",e)
if(document.queryCommandSupported&&document.queryCommandSupported("copy")){var n=document.createElement("textarea")
n.textContent=e,n.style.position="fixed",document.body.appendChild(n),n.select()
var i=!1
try{i=document.execCommand("copy")}catch(t){console.warn("Copy to clipboard failed.",t)}finally{document.body.removeChild(n)}if(i){var r=t.dataset.label
t.dataset.label="Copied!",setTimeout(function(){t.dataset.label=r},2e3)}return i}}function bindAjaxSubmits(){$("form[method='ajax']").on("submit",function(t){var e=$(this),n=$("div.output",e),i=e.parent(),r=$("[type='submit']",e)
return n.empty(),console.log("submit",r),r.addClass("submitting"),$.post(e.attr("action"),e.serialize(),function(t){return r.removeClass("submitting"),t.html?(i.html(t.html),void bindAjaxSubmits()):(t.csrf&&$("input[name='csrf_token']",e).length&&$("input[name='csrf_token']",e).val(t.csrf),t.message&&n&&(n.addClass("row").append($("<label></label>")),$("<output>").addClass(t.className).val(t.message).appendTo(n)),t.redirect&&(location.href=t.redirect),void(t.reload&&location.reload(!0)))}),!1})}!function(t,e,n,i){"use strict"
function r(t,e,n){return setTimeout(u(t,n),e)}function o(t,e,n){return!!Array.isArray(t)&&(s(t,n[e],n),!0)}function s(t,e,n){var r
if(t)if(t.forEach)t.forEach(e,n)
else if(t.length!==i)for(r=0;r<t.length;)e.call(n,t[r],r,t),r++
else for(r in t)t.hasOwnProperty(r)&&e.call(n,t[r],r,t)}function a(e,n,i){var r="DEPRECATED METHOD: "+n+"\n"+i+" AT \n"
return function(){var n=new Error("get-stack-trace"),i=n&&n.stack?n.stack.replace(/^[^\(]+?[\n$]/gm,"").replace(/^\s+at\s+/gm,"").replace(/^Object.<anonymous>\s*\(/gm,"{anonymous}()@"):"Unknown Stack Trace",o=t.console&&(t.console.warn||t.console.log)
return o&&o.call(t.console,r,i),e.apply(this,arguments)}}function c(t,e,n){var i,r=e.prototype
i=t.prototype=Object.create(r),i.constructor=t,i._super=r,n&&ht(i,n)}function u(t,e){return function(){return t.apply(e,arguments)}}function l(t,e){return typeof t==ft?t.apply(e?e[0]||i:i,e):t}function h(t,e){return t===i?e:t}function p(t,e,n){s(v(e),function(e){t.addEventListener(e,n,!1)})}function d(t,e,n){s(v(e),function(e){t.removeEventListener(e,n,!1)})}function f(t,e){for(;t;){if(t==e)return!0
t=t.parentNode}return!1}function m(t,e){return t.indexOf(e)>-1}function v(t){return t.trim().split(/\s+/g)}function g(t,e,n){if(t.indexOf&&!n)return t.indexOf(e)
for(var i=0;i<t.length;){if(n&&t[i][n]==e||!n&&t[i]===e)return i
i++}return-1}function y(t){return Array.prototype.slice.call(t,0)}function T(t,e,n){for(var i=[],r=[],o=0;o<t.length;){var s=e?t[o][e]:t[o]
g(r,s)<0&&i.push(t[o]),r[o]=s,o++}return n&&(i=e?i.sort(function(t,n){return t[e]>n[e]}):i.sort()),i}function E(t,e){for(var n,r,o=e[0].toUpperCase()+e.slice(1),s=0;s<pt.length;){if(n=pt[s],r=n?n+o:e,r in t)return r
s++}return i}function b(){return Et++}function C(e){var n=e.ownerDocument||e
return n.defaultView||n.parentWindow||t}function x(t,e){var n=this
this.manager=t,this.callback=e,this.element=t.element,this.target=t.options.inputTarget,this.domHandler=function(e){l(t.options.enable,[t])&&n.handler(e)},this.init()}function A(t){var e,n=t.options.inputClass
return new(e=n?n:xt?z:At?q:Ct?H:Y)(t,I)}function I(t,e,n){var i=n.pointers.length,r=n.changedPointers.length,o=e&Nt&&i-r===0,s=e&(Pt|$t)&&i-r===0
n.isFirst=!!o,n.isFinal=!!s,o&&(t.session={}),n.eventType=e,S(t,n),t.emit("hammer.input",n),t.recognize(n),t.session.prevInput=n}function S(t,e){var n=t.session,i=e.pointers,r=i.length
n.firstInput||(n.firstInput=k(e)),r>1&&!n.firstMultiple?n.firstMultiple=k(e):1===r&&(n.firstMultiple=!1)
var o=n.firstInput,s=n.firstMultiple,a=s?s.center:o.center,c=e.center=N(i)
e.timeStamp=gt(),e.deltaTime=e.timeStamp-o.timeStamp,e.angle=M(a,c),e.distance=$(a,c),w(n,e),e.offsetDirection=P(e.deltaX,e.deltaY)
var u=_(e.deltaTime,e.deltaX,e.deltaY)
e.overallVelocityX=u.x,e.overallVelocityY=u.y,e.overallVelocity=vt(u.x)>vt(u.y)?u.x:u.y,e.scale=s?O(s.pointers,i):1,e.rotation=s?R(s.pointers,i):0,e.maxPointers=n.prevInput?e.pointers.length>n.prevInput.maxPointers?e.pointers.length:n.prevInput.maxPointers:e.pointers.length,D(n,e)
var l=t.element
f(e.srcEvent.target,l)&&(l=e.srcEvent.target),e.target=l}function w(t,e){var n=e.center,i=t.offsetDelta||{},r=t.prevDelta||{},o=t.prevInput||{}
e.eventType!==Nt&&o.eventType!==Pt||(r=t.prevDelta={x:o.deltaX||0,y:o.deltaY||0},i=t.offsetDelta={x:n.x,y:n.y}),e.deltaX=r.x+(n.x-i.x),e.deltaY=r.y+(n.y-i.y)}function D(t,e){var n,r,o,s,a=t.lastInterval||e,c=e.timeStamp-a.timeStamp
if(e.eventType!=$t&&(c>kt||a.velocity===i)){var u=e.deltaX-a.deltaX,l=e.deltaY-a.deltaY,h=_(c,u,l)
r=h.x,o=h.y,n=vt(h.x)>vt(h.y)?h.x:h.y,s=P(u,l),t.lastInterval=e}else n=a.velocity,r=a.velocityX,o=a.velocityY,s=a.direction
e.velocity=n,e.velocityX=r,e.velocityY=o,e.direction=s}function k(t){for(var e=[],n=0;n<t.pointers.length;)e[n]={clientX:mt(t.pointers[n].clientX),clientY:mt(t.pointers[n].clientY)},n++
return{timeStamp:gt(),pointers:e,center:N(e),deltaX:t.deltaX,deltaY:t.deltaY}}function N(t){var e=t.length
if(1===e)return{x:mt(t[0].clientX),y:mt(t[0].clientY)}
for(var n=0,i=0,r=0;e>r;)n+=t[r].clientX,i+=t[r].clientY,r++
return{x:mt(n/e),y:mt(i/e)}}function _(t,e,n){return{x:e/t||0,y:n/t||0}}function P(t,e){return t===e?Mt:vt(t)>=vt(e)?0>t?Rt:Ot:0>e?Yt:zt}function $(t,e,n){n||(n=Lt)
var i=e[n[0]]-t[n[0]],r=e[n[1]]-t[n[1]]
return Math.sqrt(i*i+r*r)}function M(t,e,n){n||(n=Lt)
var i=e[n[0]]-t[n[0]],r=e[n[1]]-t[n[1]]
return 180*Math.atan2(r,i)/Math.PI}function R(t,e){return M(e[1],e[0],Ht)+M(t[1],t[0],Ht)}function O(t,e){return $(e[0],e[1],Ht)/$(t[0],t[1],Ht)}function Y(){this.evEl=Vt,this.evWin=Wt,this.pressed=!1,x.apply(this,arguments)}function z(){this.evEl=Gt,this.evWin=Zt,x.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function X(){this.evTarget=Qt,this.evWin=Kt,this.started=!1,x.apply(this,arguments)}function F(t,e){var n=y(t.touches),i=y(t.changedTouches)
return e&(Pt|$t)&&(n=T(n.concat(i),"identifier",!0)),[n,i]}function q(){this.evTarget=ee,this.targetIds={},x.apply(this,arguments)}function L(t,e){var n=y(t.touches),i=this.targetIds
if(e&(Nt|_t)&&1===n.length)return i[n[0].identifier]=!0,[n,n]
var r,o,s=y(t.changedTouches),a=[],c=this.target
if(o=n.filter(function(t){return f(t.target,c)}),e===Nt)for(r=0;r<o.length;)i[o[r].identifier]=!0,r++
for(r=0;r<s.length;)i[s[r].identifier]&&a.push(s[r]),e&(Pt|$t)&&delete i[s[r].identifier],r++
return a.length?[T(o.concat(a),"identifier",!0),a]:void 0}function H(){x.apply(this,arguments)
var t=u(this.handler,this)
this.touch=new q(this.manager,t),this.mouse=new Y(this.manager,t),this.primaryTouch=null,this.lastTouches=[]}function U(t,e){t&Nt?(this.primaryTouch=e.changedPointers[0].identifier,V.call(this,e)):t&(Pt|$t)&&V.call(this,e)}function V(t){var e=t.changedPointers[0]
if(e.identifier===this.primaryTouch){var n={x:e.clientX,y:e.clientY}
this.lastTouches.push(n)
var i=this.lastTouches,r=function(){var t=i.indexOf(n)
t>-1&&i.splice(t,1)}
setTimeout(r,ne)}}function W(t){for(var e=t.srcEvent.clientX,n=t.srcEvent.clientY,i=0;i<this.lastTouches.length;i++){var r=this.lastTouches[i],o=Math.abs(e-r.x),s=Math.abs(n-r.y)
if(ie>=o&&ie>=s)return!0}return!1}function j(t,e){this.manager=t,this.set(e)}function B(t){if(m(t,ue))return ue
var e=m(t,le),n=m(t,he)
return e&&n?ue:e||n?e?le:he:m(t,ce)?ce:ae}function G(){if(!oe)return!1
var e={},n=t.CSS&&t.CSS.supports
return["auto","manipulation","pan-y","pan-x","pan-x pan-y","none"].forEach(function(i){e[i]=!n||t.CSS.supports("touch-action",i)}),e}function Z(t){this.options=ht({},this.defaults,t||{}),this.id=b(),this.manager=null,this.options.enable=h(this.options.enable,!0),this.state=de,this.simultaneous={},this.requireFail=[]}function J(t){return t&ye?"cancel":t&ve?"end":t&me?"move":t&fe?"start":""}function Q(t){return t==zt?"down":t==Yt?"up":t==Rt?"left":t==Ot?"right":""}function K(t,e){var n=e.manager
return n?n.get(t):t}function tt(){Z.apply(this,arguments)}function et(){tt.apply(this,arguments),this.pX=null,this.pY=null}function nt(){tt.apply(this,arguments)}function it(){Z.apply(this,arguments),this._timer=null,this._input=null}function rt(){tt.apply(this,arguments)}function ot(){tt.apply(this,arguments)}function st(){Z.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function at(t,e){return e=e||{},e.recognizers=h(e.recognizers,at.defaults.preset),new ct(t,e)}function ct(t,e){this.options=ht({},at.defaults,e||{}),this.options.inputTarget=this.options.inputTarget||t,this.handlers={},this.session={},this.recognizers=[],this.oldCssProps={},this.element=t,this.input=A(this),this.touchAction=new j(this,this.options.touchAction),ut(this,!0),s(this.options.recognizers,function(t){var e=this.add(new t[0](t[1]))
t[2]&&e.recognizeWith(t[2]),t[3]&&e.requireFailure(t[3])},this)}function ut(t,e){var n=t.element
if(n.style){var i
s(t.options.cssProps,function(r,o){i=E(n.style,o),e?(t.oldCssProps[i]=n.style[i],n.style[i]=r):n.style[i]=t.oldCssProps[i]||""}),e||(t.oldCssProps={})}}function lt(t,n){var i=e.createEvent("Event")
i.initEvent(t,!0,!0),i.gesture=n,n.target.dispatchEvent(i)}var ht,pt=["","webkit","Moz","MS","ms","o"],dt=e.createElement("div"),ft="function",mt=Math.round,vt=Math.abs,gt=Date.now
ht="function"!=typeof Object.assign?function(t){if(t===i||null===t)throw new TypeError("Cannot convert undefined or null to object")
for(var e=Object(t),n=1;n<arguments.length;n++){var r=arguments[n]
if(r!==i&&null!==r)for(var o in r)r.hasOwnProperty(o)&&(e[o]=r[o])}return e}:Object.assign
var yt=a(function(t,e,n){for(var r=Object.keys(e),o=0;o<r.length;)(!n||n&&t[r[o]]===i)&&(t[r[o]]=e[r[o]]),o++
return t},"extend","Use `assign`."),Tt=a(function(t,e){return yt(t,e,!0)},"merge","Use `assign`."),Et=1,bt=/mobile|tablet|ip(ad|hone|od)|android/i,Ct="ontouchstart"in t,xt=E(t,"PointerEvent")!==i,At=Ct&&bt.test(navigator.userAgent),It="touch",St="pen",wt="mouse",Dt="kinect",kt=25,Nt=1,_t=2,Pt=4,$t=8,Mt=1,Rt=2,Ot=4,Yt=8,zt=16,Xt=Rt|Ot,Ft=Yt|zt,qt=Xt|Ft,Lt=["x","y"],Ht=["clientX","clientY"]
x.prototype={handler:function(){},init:function(){this.evEl&&p(this.element,this.evEl,this.domHandler),this.evTarget&&p(this.target,this.evTarget,this.domHandler),this.evWin&&p(C(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&d(this.element,this.evEl,this.domHandler),this.evTarget&&d(this.target,this.evTarget,this.domHandler),this.evWin&&d(C(this.element),this.evWin,this.domHandler)}}
var Ut={mousedown:Nt,mousemove:_t,mouseup:Pt},Vt="mousedown",Wt="mousemove mouseup"
c(Y,x,{handler:function(t){var e=Ut[t.type]
e&Nt&&0===t.button&&(this.pressed=!0),e&_t&&1!==t.which&&(e=Pt),this.pressed&&(e&Pt&&(this.pressed=!1),this.callback(this.manager,e,{pointers:[t],changedPointers:[t],pointerType:wt,srcEvent:t}))}})
var jt={pointerdown:Nt,pointermove:_t,pointerup:Pt,pointercancel:$t,pointerout:$t},Bt={2:It,3:St,4:wt,5:Dt},Gt="pointerdown",Zt="pointermove pointerup pointercancel"
t.MSPointerEvent&&!t.PointerEvent&&(Gt="MSPointerDown",Zt="MSPointerMove MSPointerUp MSPointerCancel"),c(z,x,{handler:function(t){var e=this.store,n=!1,i=t.type.toLowerCase().replace("ms",""),r=jt[i],o=Bt[t.pointerType]||t.pointerType,s=o==It,a=g(e,t.pointerId,"pointerId")
r&Nt&&(0===t.button||s)?0>a&&(e.push(t),a=e.length-1):r&(Pt|$t)&&(n=!0),0>a||(e[a]=t,this.callback(this.manager,r,{pointers:e,changedPointers:[t],pointerType:o,srcEvent:t}),n&&e.splice(a,1))}})
var Jt={touchstart:Nt,touchmove:_t,touchend:Pt,touchcancel:$t},Qt="touchstart",Kt="touchstart touchmove touchend touchcancel"
c(X,x,{handler:function(t){var e=Jt[t.type]
if(e===Nt&&(this.started=!0),this.started){var n=F.call(this,t,e)
e&(Pt|$t)&&n[0].length-n[1].length===0&&(this.started=!1),this.callback(this.manager,e,{pointers:n[0],changedPointers:n[1],pointerType:It,srcEvent:t})}}})
var te={touchstart:Nt,touchmove:_t,touchend:Pt,touchcancel:$t},ee="touchstart touchmove touchend touchcancel"
c(q,x,{handler:function(t){var e=te[t.type],n=L.call(this,t,e)
n&&this.callback(this.manager,e,{pointers:n[0],changedPointers:n[1],pointerType:It,srcEvent:t})}})
var ne=2500,ie=25
c(H,x,{handler:function(t,e,n){var i=n.pointerType==It,r=n.pointerType==wt
if(!(r&&n.sourceCapabilities&&n.sourceCapabilities.firesTouchEvents)){if(i)U.call(this,e,n)
else if(r&&W.call(this,n))return
this.callback(t,e,n)}},destroy:function(){this.touch.destroy(),this.mouse.destroy()}})
var re=E(dt.style,"touchAction"),oe=re!==i,se="compute",ae="auto",ce="manipulation",ue="none",le="pan-x",he="pan-y",pe=G()
j.prototype={set:function(t){t==se&&(t=this.compute()),oe&&this.manager.element.style&&pe[t]&&(this.manager.element.style[re]=t),this.actions=t.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var t=[]
return s(this.manager.recognizers,function(e){l(e.options.enable,[e])&&(t=t.concat(e.getTouchAction()))}),B(t.join(" "))},preventDefaults:function(t){var e=t.srcEvent,n=t.offsetDirection
if(this.manager.session.prevented)return void e.preventDefault()
var i=this.actions,r=m(i,ue)&&!pe[ue],o=m(i,he)&&!pe[he],s=m(i,le)&&!pe[le]
if(r){var a=1===t.pointers.length,c=t.distance<2,u=t.deltaTime<250
if(a&&c&&u)return}return s&&o?void 0:r||o&&n&Xt||s&&n&Ft?this.preventSrc(e):void 0},preventSrc:function(t){this.manager.session.prevented=!0,t.preventDefault()}}
var de=1,fe=2,me=4,ve=8,ge=ve,ye=16,Te=32
Z.prototype={defaults:{},set:function(t){return ht(this.options,t),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(t){if(o(t,"recognizeWith",this))return this
var e=this.simultaneous
return t=K(t,this),e[t.id]||(e[t.id]=t,t.recognizeWith(this)),this},dropRecognizeWith:function(t){return o(t,"dropRecognizeWith",this)?this:(t=K(t,this),delete this.simultaneous[t.id],this)},requireFailure:function(t){if(o(t,"requireFailure",this))return this
var e=this.requireFail
return t=K(t,this),-1===g(e,t)&&(e.push(t),t.requireFailure(this)),this},dropRequireFailure:function(t){if(o(t,"dropRequireFailure",this))return this
t=K(t,this)
var e=g(this.requireFail,t)
return e>-1&&this.requireFail.splice(e,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(t){return!!this.simultaneous[t.id]},emit:function(t){function e(e){n.manager.emit(e,t)}var n=this,i=this.state
ve>i&&e(n.options.event+J(i)),e(n.options.event),t.additionalEvent&&e(t.additionalEvent),i>=ve&&e(n.options.event+J(i))},tryEmit:function(t){return this.canEmit()?this.emit(t):void(this.state=Te)},canEmit:function(){for(var t=0;t<this.requireFail.length;){if(!(this.requireFail[t].state&(Te|de)))return!1
t++}return!0},recognize:function(t){var e=ht({},t)
return l(this.options.enable,[this,e])?(this.state&(ge|ye|Te)&&(this.state=de),this.state=this.process(e),void(this.state&(fe|me|ve|ye)&&this.tryEmit(e))):(this.reset(),void(this.state=Te))},process:function(t){},getTouchAction:function(){},reset:function(){}},c(tt,Z,{defaults:{pointers:1},attrTest:function(t){var e=this.options.pointers
return 0===e||t.pointers.length===e},process:function(t){var e=this.state,n=t.eventType,i=e&(fe|me),r=this.attrTest(t)
return i&&(n&$t||!r)?e|ye:i||r?n&Pt?e|ve:e&fe?e|me:fe:Te}}),c(et,tt,{defaults:{event:"pan",threshold:10,pointers:1,direction:qt},getTouchAction:function(){var t=this.options.direction,e=[]
return t&Xt&&e.push(he),t&Ft&&e.push(le),e},directionTest:function(t){var e=this.options,n=!0,i=t.distance,r=t.direction,o=t.deltaX,s=t.deltaY
return r&e.direction||(e.direction&Xt?(r=0===o?Mt:0>o?Rt:Ot,n=o!=this.pX,i=Math.abs(t.deltaX)):(r=0===s?Mt:0>s?Yt:zt,n=s!=this.pY,i=Math.abs(t.deltaY))),t.direction=r,n&&i>e.threshold&&r&e.direction},attrTest:function(t){return tt.prototype.attrTest.call(this,t)&&(this.state&fe||!(this.state&fe)&&this.directionTest(t))},emit:function(t){this.pX=t.deltaX,this.pY=t.deltaY
var e=Q(t.direction)
e&&(t.additionalEvent=this.options.event+e),this._super.emit.call(this,t)}}),c(nt,tt,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[ue]},attrTest:function(t){return this._super.attrTest.call(this,t)&&(Math.abs(t.scale-1)>this.options.threshold||this.state&fe)},emit:function(t){if(1!==t.scale){var e=t.scale<1?"in":"out"
t.additionalEvent=this.options.event+e}this._super.emit.call(this,t)}}),c(it,Z,{defaults:{event:"press",pointers:1,time:251,threshold:9},getTouchAction:function(){return[ae]},process:function(t){var e=this.options,n=t.pointers.length===e.pointers,i=t.distance<e.threshold,o=t.deltaTime>e.time
if(this._input=t,!i||!n||t.eventType&(Pt|$t)&&!o)this.reset()
else if(t.eventType&Nt)this.reset(),this._timer=r(function(){this.state=ge,this.tryEmit()},e.time,this)
else if(t.eventType&Pt)return ge
return Te},reset:function(){clearTimeout(this._timer)},emit:function(t){this.state===ge&&(t&&t.eventType&Pt?this.manager.emit(this.options.event+"up",t):(this._input.timeStamp=gt(),this.manager.emit(this.options.event,this._input)))}}),c(rt,tt,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[ue]},attrTest:function(t){return this._super.attrTest.call(this,t)&&(Math.abs(t.rotation)>this.options.threshold||this.state&fe)}}),c(ot,tt,{defaults:{event:"swipe",threshold:10,velocity:.3,direction:Xt|Ft,pointers:1},getTouchAction:function(){return et.prototype.getTouchAction.call(this)},attrTest:function(t){var e,n=this.options.direction
return n&(Xt|Ft)?e=t.overallVelocity:n&Xt?e=t.overallVelocityX:n&Ft&&(e=t.overallVelocityY),this._super.attrTest.call(this,t)&&n&t.offsetDirection&&t.distance>this.options.threshold&&t.maxPointers==this.options.pointers&&vt(e)>this.options.velocity&&t.eventType&Pt},emit:function(t){var e=Q(t.offsetDirection)
e&&this.manager.emit(this.options.event+e,t),this.manager.emit(this.options.event,t)}}),c(st,Z,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:9,posThreshold:10},getTouchAction:function(){return[ce]},process:function(t){var e=this.options,n=t.pointers.length===e.pointers,i=t.distance<e.threshold,o=t.deltaTime<e.time
if(this.reset(),t.eventType&Nt&&0===this.count)return this.failTimeout()
if(i&&o&&n){if(t.eventType!=Pt)return this.failTimeout()
var s=!this.pTime||t.timeStamp-this.pTime<e.interval,a=!this.pCenter||$(this.pCenter,t.center)<e.posThreshold
this.pTime=t.timeStamp,this.pCenter=t.center,a&&s?this.count+=1:this.count=1,this._input=t
var c=this.count%e.taps
if(0===c)return this.hasRequireFailures()?(this._timer=r(function(){this.state=ge,this.tryEmit()},e.interval,this),fe):ge}return Te},failTimeout:function(){return this._timer=r(function(){this.state=Te},this.options.interval,this),Te},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==ge&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),at.VERSION="2.0.8",at.defaults={domEvents:!1,touchAction:se,enable:!0,inputTarget:null,inputClass:null,preset:[[rt,{enable:!1}],[nt,{enable:!1},["rotate"]],[ot,{direction:Xt}],[et,{direction:Xt},["swipe"]],[st],[st,{event:"doubletap",taps:2},["tap"]],[it]],cssProps:{userSelect:"none",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}}
var Ee=1,be=2
ct.prototype={set:function(t){return ht(this.options,t),t.touchAction&&this.touchAction.update(),t.inputTarget&&(this.input.destroy(),this.input.target=t.inputTarget,this.input.init()),this},stop:function(t){this.session.stopped=t?be:Ee},recognize:function(t){var e=this.session
if(!e.stopped){this.touchAction.preventDefaults(t)
var n,i=this.recognizers,r=e.curRecognizer;(!r||r&&r.state&ge)&&(r=e.curRecognizer=null)
for(var o=0;o<i.length;)n=i[o],e.stopped===be||r&&n!=r&&!n.canRecognizeWith(r)?n.reset():n.recognize(t),!r&&n.state&(fe|me|ve)&&(r=e.curRecognizer=n),o++}},get:function(t){if(t instanceof Z)return t
for(var e=this.recognizers,n=0;n<e.length;n++)if(e[n].options.event==t)return e[n]
return null},add:function(t){if(o(t,"add",this))return this
var e=this.get(t.options.event)
return e&&this.remove(e),this.recognizers.push(t),t.manager=this,this.touchAction.update(),t},remove:function(t){if(o(t,"remove",this))return this
if(t=this.get(t)){var e=this.recognizers,n=g(e,t);-1!==n&&(e.splice(n,1),this.touchAction.update())}return this},on:function(t,e){if(t!==i&&e!==i){var n=this.handlers
return s(v(t),function(t){n[t]=n[t]||[],n[t].push(e)}),this}},off:function(t,e){if(t!==i){var n=this.handlers
return s(v(t),function(t){e?n[t]&&n[t].splice(g(n[t],e),1):delete n[t]}),this}},emit:function(t,e){this.options.domEvents&&lt(t,e)
var n=this.handlers[t]&&this.handlers[t].slice()
if(n&&n.length){e.type=t,e.preventDefault=function(){e.srcEvent.preventDefault()}
for(var i=0;i<n.length;)n[i](e),i++}},destroy:function(){this.element&&ut(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},ht(at,{INPUT_START:Nt,INPUT_MOVE:_t,INPUT_END:Pt,INPUT_CANCEL:$t,STATE_POSSIBLE:de,STATE_BEGAN:fe,STATE_CHANGED:me,STATE_ENDED:ve,STATE_RECOGNIZED:ge,STATE_CANCELLED:ye,STATE_FAILED:Te,DIRECTION_NONE:Mt,DIRECTION_LEFT:Rt,DIRECTION_RIGHT:Ot,DIRECTION_UP:Yt,DIRECTION_DOWN:zt,DIRECTION_HORIZONTAL:Xt,DIRECTION_VERTICAL:Ft,DIRECTION_ALL:qt,Manager:ct,Input:x,TouchAction:j,TouchInput:q,MouseInput:Y,PointerEventInput:z,TouchMouseInput:H,SingleTouchInput:X,Recognizer:Z,AttrRecognizer:tt,Tap:st,Pan:et,Swipe:ot,Pinch:nt,Rotate:rt,Press:it,on:p,off:d,each:s,merge:Tt,extend:yt,assign:ht,inherit:c,bindFn:u,prefixed:E})
var Ce="undefined"!=typeof t?t:"undefined"!=typeof self?self:{}
Ce.Hammer=at,"function"==typeof define&&define.amd?define(function(){return at}):"undefined"!=typeof module&&module.exports?module.exports=at:t[n]=at}(window,document,"Hammer"),document.querySelector("html").setAttribute("class",("ontouchstart"in window?"":"no-")+"touch"),function(t,e){function n(t){if("#text"!=t.firstChild.nodeName)return t.firstChild
t=t.firstChild
do t=t.nextSibling
while(t&&"#text"==t.nodeName)
return t||null}function i(t){var e=t.nodeName.toUpperCase()
return"DETAILS"!=e&&("SUMMARY"==e||i(t.parentNode))}function r(t){var n="keypress"==t.type,r=t.target||t.srcElement
if(n||i(r)){if(n&&(n=t.which||t.keyCode,32!=n&&13!=n))return
if(null===this.getAttribute("open")?this.setAttribute("open","open"):this.removeAttribute("open"),setTimeout(function(){e.body.className=e.body.className},13),n)return t.preventDefault&&t.preventDefault(),!1}}if(!("open"in e.createElement("details"))){var o,s,a=function(){return e.addEventListener?function(e,n,i){if(e&&e.nodeName||e===t)e.addEventListener(n,i,!1)
else if(e&&e.length)for(var r=0;r<e.length;r++)a(e[r],n,i)}:function(e,n,i){if(e&&e.nodeName||e===t)e.attachEvent("on"+n,function(){return i.call(e,t.event)})
else if(e&&e.length)for(var r=0;r<e.length;r++)a(e[r],n,i)}}(),c=e.getElementsByTagName("details"),u=c.length,l=null
for(e.createElement("summary").appendChild(e.createTextNode("Details"));u--;){for(l=n(c[u]),null!=l&&"SUMMARY"==l.nodeName.toUpperCase()||(l=e.createElement("summary"),l.appendChild(e.createTextNode("Details")),c[u].firstChild?c[u].insertBefore(l,c[u].firstChild):c[u].appendChild(l)),s=c[u].childNodes.length;s--;)"#text"===c[u].childNodes[s].nodeName&&(c[u].childNodes[s].nodeValue||"").replace(/\s/g,"").length&&(o=e.createElement("text"),o.appendChild(c[u].childNodes[s]),c[u].insertBefore(o,c[u].childNodes[s]))
l.legend=!0,l.tabIndex=0}e.createElement("details"),a(c,"click",r),a(c,"keypress",r),function(){var t=e.createElement("style"),n=e.getElementsByTagName("head")[0],i=void 0===t.innerText?"textContent":"innerText",r="details{display: block;},details > *{display: none;},details.open > *{display: block;},details[open] > *{display: block;},details > summary:first-child{display: block;cursor: pointer;},details[open]{display: block;}".split(",")
u=r.length,t[i]=r.join("\n"),n.insertBefore(t,n.firstChild)}()}}(window,document)
var ajaxSubmit=function(t){var e=$(t)
$feedback=$("#form-feedback",e).html("").removeAttr("class"),$.post(e.attr("action"),e.serialize(),function(t){t.csrf&&$("input[name='csrf_token']",e).length&&$("input[name='csrf_token']",e).val(t.csrf),t.positive?e.html(t.message):($p=$("<p>").addClass(t.className).html(t.message),$feedback.html($p),grecaptcha.reset()),t.sent&&(e.get(0).reset(),grecaptcha.reset()),t.reload&&location.reload(!0)})},renderGoogleInvisibleRecaptcha=function(){for(var t=0;t<document.forms.length;++t){var e=document.forms[t],n=e.querySelector(".recaptcha-holder"),i="ajax"==e.getAttribute("method")
null!==n&&!function(t){var e=grecaptcha.render(n,{sitekey:"6LewIiYUAAAAAJcV-bQRfk824cYcsYwkIZ99Bpsy",size:"invisible",badge:"inline",callback:function(e){i?ajaxSubmit(t):HTMLFormElement.prototype.submit.call(t)}})
t.onsubmit=function(t){t.preventDefault(),grecaptcha.execute(e)}}(e)}}
$(function(){UIkit.util.on(document,"hide","[uk-alert]",function(t){$.post("/data/alert",{id:t.target.dataset.id})}),bindAjaxSubmits(),$("#store_index").on("click",".tile.app > figure",function(t){$(this).closest(".tile").find("a:first").get(0).click()}),$("#store_index .timeline-heading h4").on("click",function(t){var e=$(this).text()
t.preventDefault(),$("textarea[name='your-message']","#contact-form").val("Hi,\nI'm contacting about a service listed on your CourseSuite website - "+e+"\n\nHere are my needs:\n-"),document.querySelector("#contact-form").scrollIntoView()}),$("a[href='#toggle-once']").each(function(t,e){$(e).next().hide(),$(e).on("click",function(t){t.preventDefault(),$(this).hide().next().fadeIn()})}),$("a[data-action='dismiss-message']").on("click",function(t){var e=$(this)
$.getJSON("/message/done/"+this.getAttribute("data-action-id"),function(t){t.updated&&(console.log("updated",e),e.closest("div.acknowledgement-item").fadeOut(250,function(){console.log("faded",this),$(this).remove()}))})}),setTimeout(function(){$("#bgndVideo").length&&$("#bgndVideo").YTPlayer()},150),$("textarea[data-markdown]").each(function(t,e){e.simplemde=new SimpleMDE({element:e,spellChecker:!1,placeholder:'Markdown / HTML is allowed.\nDrag images onto this editor to upload & link them\nTo nest markdown inside html, add attribute markdown="1" of tags containing markdown.'}),inlineAttachment.editors.codemirror4.attach(e.simplemde.codemirror,{uploadUrl:"https://api.imgur.com/3/image",extraHeaders:{Authorization:"Client-ID 662ce7a8f142394"},extraParams:{name:"Your image title",description:"Dragged onto editor using inline-attachment"},uploadFieldName:"image",onFileUploadResponse:function(t){var e=JSON.parse(t.responseText),n=e.data.id,i=e.data.type.split("/")[1],r=e.data.title||"Untitled",o="https://i.imgur.com/"+n+"."+i,s="http://i.imgur.com/"+n+"m."+i,a="[!["+r+"]("+s+")]("+o+")",c=this.editor.getValue().replace(this.lastValue,a)
return this.editor.setValue(c),!1}})})})
