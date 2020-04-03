const environment = process.env.NODE_ENV
const isDevelopment = 'development' === environment

module.exports = ({options}) => ({

  plugins: {

    /**
     *	https://stylelint.io/user-guide/rules/
     */
    stylelint: {
      "rules": {
        "at-rule-blacklist": [],
        "at-rule-name-case": "lower",
        "at-rule-name-newline-after": "always-multi-line",
        "at-rule-no-unknown": true,
        "at-rule-no-vendor-prefix": true,
        "at-rule-semicolon-newline-after": "always",
        "block-closing-brace-empty-line-before": "never",
        "block-no-empty": true,
        "color-hex-case": "lower",
        "color-named": "never",
        "color-no-invalid-hex": true,
        "comment-whitespace-inside": "always",
        "comment-word-blacklist": [],
        "custom-property-empty-line-before": "never",
        "declaration-block-no-shorthand-property-overrides": true,
        "declaration-colon-newline-after": "always-multi-line",
        "declaration-empty-line-before": "never",
        "font-family-no-missing-generic-family-keyword": true,
        "function-calc-no-unspaced-operator": true,
        "function-linear-gradient-no-nonstandard-direction": true,
        "function-max-empty-lines": 0,
        "function-name-case": "lower",
        "function-parentheses-newline-inside": "never-multi-line",
        "function-parentheses-space-inside": "never",
        "function-url-quotes": "never",
        "media-feature-name-case": "lower",
        "media-feature-name-no-unknown": true,
        "media-feature-name-no-vendor-prefix": true,
        "media-feature-parentheses-space-inside": "never",
        "no-duplicate-at-import-rules": true,
        "no-empty-source": true,
        "no-extra-semicolons": true,
        "no-invalid-double-slash-comments": true,
        "number-max-precision": 10,
        "number-no-trailing-zeros": true,
        "property-case": "lower",
        "property-no-unknown": true,
        "selector-attribute-brackets-space-inside": "never",
        "selector-descendant-combinator-no-non-space": true,
        "selector-list-comma-newline-before": "never-multi-line",
        "selector-max-empty-lines": 0,
        "selector-no-vendor-prefix": true,
        "selector-pseudo-class-case": "lower",
        "selector-pseudo-class-no-unknown": true,
        "selector-pseudo-class-parentheses-space-inside": "never",
        "selector-pseudo-element-case": "lower",
        "selector-pseudo-element-no-unknown": true,
        "selector-type-case": "lower",
        "string-no-newline": true,
        "time-min-milliseconds": 0,
        "unit-case": "lower",
        "unit-no-unknown": true,
        "value-keyword-case": "lower",
        "value-list-max-empty-lines": 0,
        "value-no-vendor-prefix": true,
        /**
         * Development Build rule changes/additions
         */
        ...isDevelopment && {
          "at-rule-name-space-after": "always",
          "at-rule-semicolon-space-before": "never",
          "block-closing-brace-newline-after": "always-multi-line",
          "block-closing-brace-newline-before": "always-multi-line",
          "block-closing-brace-space-before": "always-single-line",
          "block-opening-brace-newline-after": "always-multi-line",
          "block-opening-brace-space-after": "always-single-line",
          "block-opening-brace-space-before": "always",
          "color-hex-length": "long",
          "declaration-bang-space-after": "never",
          "declaration-bang-space-before": "always",
          "declaration-block-semicolon-newline-after": "always",
          "declaration-block-single-line-max-declarations": 1,
          "declaration-block-trailing-semicolon": "always",
          "declaration-colon-space-after": "always",
          "declaration-colon-space-before": "never",
          "function-comma-space-after": "always",
          "function-comma-space-before": "never",
          "function-whitespace-after": "always",
          "media-feature-colon-space-after": "always",
          "media-feature-colon-space-before": "never",
          "media-feature-range-operator-space-after": "always",
          "media-feature-range-operator-space-before": "always",
          "number-leading-zero": "always",
          "selector-attribute-operator-space-after": "never",
          "selector-attribute-operator-space-before": "never",
          "selector-combinator-space-after": "always",
          "selector-combinator-space-before": "always",
          "selector-list-comma-space-before": "never",
          "value-list-comma-newline-after": "always-multi-line",
          "value-list-comma-newline-before": "never-multi-line",
          "value-list-comma-space-after": "always",
        }
      }
    },

    /**
     * https://github.com/postcss/autoprefixer
     */
    autoprefixer: {
      "grid": true,
    },

    /**
     * http://cssnano.co/guides/optimisations/
     */
    cssnano: !!options.minify && {
      "autoprefixer": false,
      "calc": true,
      "colormin": true,
      "cssDeclarationSorter": true,
      "discardComments": true,
      "discardDuplicates": true,
      "discardEmpty": true,
      "discardOverridden": true,
      "mergeLonghand": true,
      "mergeRules": true,
      "minifyGradients": true,
      "minifyParams": true,
      "minifySelectors": true,
      "normalizeCharset": true,
      "normalizeDisplayValues": true,
      "normalizePositions": true,
      "normalizeRepeatStyle": true,
      "normalizeString": true,
      "normalizeUnicode": true,
      "normalizeUrl": true,
      "normalizeWhitespace": true,
      "orderedValues": true,
      "reduceInitial": true,
      "reduceTransforms": true,
      "safe": true,
      "svgo": true,
      "uniqueSelectors": true,
    },

  },
})
