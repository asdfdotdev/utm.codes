# utm.codes Build

utm.codes uses webpack to combine, compress, and optimize static resources.

## Setup

For best compatibility please use the version of node included in the .nvmrc file.

## Install

From the project root directory use npm to install the required node modules.

```
$ npm ci
```

Once installation is complete you can run any build scripts to rebuild static resources.

## Build Scripts

```
$ npm run COMMAND_NAME
```

**prod** - production build, runs only once, minify css/js, removes and deletes sourcemaps

**production** - alias for *prod*

**dev** - development build, adds sourcemaps, js is uncompressed, and watches for source file changes for continuous rebuild

**development** - alias for *dev*
