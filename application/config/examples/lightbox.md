# how to add a slideshow

To add a slideshow you need to use the following template html in your section.

A slideshow is a list of DIV tags that contain a hyperlink that points to an image. The site then fixes it up and display a nice slideshow for you. 
But you have to format the images properly. Each image needs to have a hyperlink around it. In Markdown, a hyperlink has the format:

`[caption-in-square-brackets](hyperlink-in-round-brackets){properties-in-curly-brackets}`

The hyperlink might need to have css classes or other properties attached to it, which is what you put into the curly brackets. Classnames start with a `.` and id's start with a `#` and everything else is turned into an attribute.

An image has almost the same format, except there is an exclamation point at the start:

`![caption-for-image](path-to-image){properties-for-image}`

To wrap an image in a hyperlink, you have to take the whole image tag and stick it into the square brackets for the hyperlink. You get something like:

`[![image-caption](image-path){image-properties}](hyperlink-to-image){hyperlink-properties}`




"slide,slide,slide,fade,fade,scale"
uk-child-width-1-3@m
uk-child-width-1-4@m

```html
<div markdown="1" uk-lightbox='animation:slide' class="uk-child-width-1-3@m uk-child-width-1-1@s uk-child-width-1-4@xl" uk-grid>
	<div>
		[![slide-alt-tag](http://path-to/image.jpg)](http://path-to/image.jpg){.uk-inline .uk-box-shadow-small .uk-box-shadow-hover-large caption="slide caption in here"}
	</div>
</div>
<style>body>div.uk-lightbox{background-color:{{App.colour}} !important;}</style>
