# Instagram feed from a profile in TYPO3


## Introduction

Because of the annoying Instagram API we searched for a simple way to show instagram feed images without using any 
facebook API.

Since version 3 any rss feed can be used to import instagram images and text from the feed. Images are stored locally
for best privacy for your visitors.

## What's new in 3.0

In former versions we were inspired from the JavaScript plugin https://github.com/jsanahuja/InstagramFeed and there 
functionality. So we build a similar function with PHP and the caching framework.
Even if we used only one access per day to instagram.com, instagram was blocking the server requests after about one
month.
So now we have to think different: 
* How to get the feed without facebook API
* Without costs
* With privacy

We decided to use a free RSS-Feed (https://rss.app) to get the instagram images.


## Explanation

Very simple plugin where you can only add your rss url and that's it. 
Feed is cached for 24h via TYPO3 caching framework. A fluid template can be used to change the
html markup.


## Installation

`composer require in2code/instagram`


## Configuration

### RSS feed generation

Create a RSS-feed with your instagram images. We would recommend to use the free service https://rss.app with only a
few clicks (see example RSS https://rss.app/feeds/CizuljZUv53Wxfha.xml).

Add this url to your FlexForm configuration

### HTML output modification

Overwrite and modify the HTML output:

```
plugin {
    tx_instagram_pi1 {
        view {
            templateRootPaths {
                0 = EXT:instagram/Resources/Private/Templates/
            }
        }
    }
}
```


Example html:

```
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
	  xmlns:instagram="http://typo3.org/ns/In2code/Instagram/ViewHelpers"
	  data-namespace-typo3-fluid="true">

<div class="c-socialwall">
	<f:for each="{feed.channel.item}" as="item">
		<div class="c-socialwall__item c-socialwall__item--instagram">
			<f:link.external uri="{item.link}" title="{feed.channel.title}" target="_blank" rel="noopener">

				<instagram:isLocalImageExisting item="{item}">
					<f:then>
						<f:image src="/typo3temp/assets/tx_instagram/{item.guid}.jpg" alt="{item.title}" title="{item.title}" width="500c" height="500c" />
					</f:then>
					<f:else>
						<img src="{item.imageurl}" alt="{item.title}" title="{item.title}" width="500" height="500" />
					</f:else>
				</instagram:isLocalImageExisting>

				<p>{image.node.edge_media_to_caption.edges.0.node.text}</p>
			</f:link.external>
		</div>
	</f:for>
</div>

</html>
```


## Screenshots

![Images from the instagram feed](Documentation/Images/frontend.png "Images from the instagram feed")

![Plugin](Documentation/Images/backend.png "Plugin")


## Changelog

| Version    | Date       | State      | Description      |
| ---------- | ---------- | ---------- | ---------------- |
| 3.0.0 !!!  | 2020-06-05 | Task       | Use RSS-feed now for a workarround that server request are blocked by instagram |
| 2.0.0      | 2020-05-08 | Task       | Store images locally now to improve privacy of your visitors. Use content element uid for building individual caches now |
| 1.1.0      | 2020-04-29 | Task       | Open links in new tabs, don't cache the view because of own caching framework usage  |
| 1.0.0      | 2020-04-29 | Task       | Initial release  |
