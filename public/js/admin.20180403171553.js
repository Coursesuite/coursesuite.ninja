$(function(){$("[sortable]").each(function(e,t){Sortable.create(t,{animation:150,handle:".my-handle",onStart:function(e){$(e.item).find("[data-id]").trigger("click")},onEnd:function(e){Array.from(document.querySelectorAll("div.tab-bar > ul[sortable] > li")).map(function(e,t){document.querySelector("div.tab-bar div[id='tab_"+e.querySelector("[data-id]").dataset.id+"'] ").dataset.sort=t})
var t=Array.from(document.querySelectorAll("div.tab-bar div[id]")),o=document.querySelector("div.tab-bar div.tabs")
t.sort(function(e,t){var o=~~e.dataset.sort,a=~~t.dataset.sort
return o<a?-1:o>a?1:0})
for(var a=0;a<t.length;++a)o.appendChild(t[a])}})}),$("div.tab-bar li > [data-id]").on("click",function(e){$(e.target).closest("li").addClass("active").siblings().removeClass("active")
var t=$("#tab_"+e.target.dataset.id)
t.addClass("active").siblings().removeClass("active")
var o=t.find("textarea[data-markdown]").get(0).simplemde
o.codemirror.refresh()}),$("#upload_widget_opener").cloudinary_upload_widget({cloud_name:"coursesuite",upload_preset:"ietcpiwn",cropping:"server",cropping_show_dimensions:!0,folder:"coursesuite",sources:["local","url","camera"]},function(e,t){console.log(e,t)}),$("[data-sortable]").each(function(e,t){Sortable.create(e,{handle:".cs-air",onEnd:function(e){console.log(e,e.oldIndex,e.newIndex),e.from.getAttribute("data-table")&&$.post("/admin/editSections/0/order",{table:e.from.getAttribute("data-table"),field:e.from.getAttribute("data-field"),order:function(){var t=[]
return $(e.item).parent().children().each(function(e,o){t[t.length]=o.getAttribute("data-id")}),t}},function(e){console.log(e)})},animation:350})}),$("input[data-action='previewEditor']").on("click",function(e){e.preventDefault()
var t=$("textarea#content").val(),o=$("body")
o.removeClass("no-scroll"),window.scrollTo(0,0),$("#preview").remove(),document.querySelector("body").scrollTop=0,$("<div id='preview'><div class='content'><iframe src='about:blank' width='100%' height='100%' frameborder='0' id='editorPreview'></iframe></div>").on("click",function(e){$("#preview").addClass("closing"),o.removeClass("no-scroll"),setTimeout(function(){$("#preview").remove()},250)}).appendTo(o.addClass("no-scroll"))
var a=document.getElementById("editorPreview").contentWindow.document
a.open(),a.write(t),a.close()}),"function"==typeof flatpickr&&(flatpickr("input.flatpickr",{onOpen:function(){this.hasBeenShown=!0},onChange:function(e){this.hasBeenShown&&(document.location=document.location.origin+"/admin/showLog/date/"+escape(e.toISOString().slice(0,10)))}}),flatpickr("input[type='datetime']")),$("a[data-action='hover-thumb']").hover(function(e){var t=$("<img>").attr({src:this.href,id:"hoverThumb"}).css({position:"absolute",top:e.pageY+10+"px",left:e.pageX+10+"px","box-shadow":"0 0 25px rgba(0,0,0,.25)"})
$("body").append(t)},function(){$("#hoverThumb").remove()}).mousemove(function(e){$("#hoverThumb").css({top:e.pageY+10+"px",left:e.pageX+10+"px"})})})
