# Neverland

Neverland is the name of the default skin of WikiToLearn

## Setup

The css of Neverland is written with scss syntax (http://sass-lang.com/) and it's based on bootstrap (http://getbootstrap.com/). Neverland also uses gulp (http://gulpjs.com/)
to watch the scss and output the css.

You need to have nodejs and npm installed: https://nodejs.org/en/ 

To setup gulp and scss you need to install the appropriate npm package. CD into the Neverland folder and:
```
npm install
```
```
bower install
```

## Basic commands

To watch your changes with scss and output the css whenevver the scss file is saved:
```
gulp css
```

If you also want to enable a live editing without refreshing the browser page:
```
gulp watch
```
it uses BrowserSync (https://www.browsersync.io/) to start a server and reload the page whenever you save the scss file

If you want ot ouput a minified css, then:
```
gulp css --minify
```

## SCSS structure

Neverland has a scss folder and is based on several scss modules. Every module contains the proper css and every module is include inside the `_wtl.scss` module. 

The `main.scss` file just imports the wtl scss + the bootstrap scss

```
.
├── main.scss
├── modules
│   ├── _articles.scss
│   ├── _buttons.scss
│   ├── _footer.scss
│   ├── _forms.scss
│   ├── _helpers.scss
│   ├── _icons.scss
│   ├── _navbar.scss
│   ├── _notifications.scss
│   ├── _page-actions.scss
│   ├── reader.scss
│   ├── _responsive.scss
│   ├── _sidebar.scss
│   ├── _specialpages.scss
│   ├── _suggestions.scss
│   ├── _tables.scss
│   ├── _templates.scss
│   └── _type.scss
└── _wtl.scss
```

## Packages used

* gulp
* BrowserSync
* SCSS
* Autoprefixer (https://github.com/postcss/autoprefixer)
