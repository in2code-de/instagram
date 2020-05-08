# Instagram feed from a profile in TYPO3


## Introduction

Because of the annoying Instagram API we searched for a simple way to show feed images without using any API.
Inspired from the JavaScript plugin https://github.com/jsanahuja/InstagramFeed and there functionality we build
the same with PHP.


## Explanation

Very simple plugin where you can only add your instagram id and that's it. Because it could be that too much requests
would lead to a ban, entries are cached for 24h via TYPO3 caching framework. A fluid template can be used to change the
html markup. There are max. 12 images and text from a instagram profile page.


## Installation

`composer require in2code/instagram`


## Configuration

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


## Screenshots

![Images from the instagram feed](Documentation/Images/frontend.png "Images from the instagram feed")

![Plugin](Documentation/Images/backend.png "Plugin")


## Changelog

| Version    | Date       | State      | Description      |
| ---------- | ---------- | ---------- | ---------------- |
| 2.0.0      | 2020-05-08 | Task       | Store images locally now to improve privacy of your visitors. Use content element uid for building individual caches now |
| 1.1.0      | 2020-04-29 | Task       | Open links in new tabs, don't cache the view because of own caching framework usage  |
| 1.0.0      | 2020-04-29 | Task       | Initial release  |
