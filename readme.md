# Instagram feed from a profile in TYPO3


## Introduction

Because of the annoying Instagram API we searched for a simple way to show feed images without using any API.
Inspired from the JavaScript plugin https://github.com/jsanahuja/InstagramFeed and there functionality


## Explanation

Very simple plugin where you can only add your instagram id and that's it. Because it could be that too much requests
would lead to a ban, entries are cached for 24h via TYPO3 caching framework.
There are max. 12 images and text from a profile id.


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
| 1.0.0      | 2020-04-29 | Task       | Initial release  |
