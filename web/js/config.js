System.config({
  defaultJSExtensions: true,
  transpiler: "babel",
  babelOptions: {
    "optional": [
      "runtime",
      "optimisation.modules.system"
    ]
  },
  paths: {
    "github:*": "jspm_packages/github/*",
    "npm:*": "jspm_packages/npm/*"
  },
  bundles: {
    "bundles/trinity.bundle.js": [
      "github:modpreneur/trinityJS@master",
      "github:modpreneur/trinityJS@master/trinity",
      "github:modpreneur/trinityJS@master/Router",
      "github:modpreneur/trinityJS@master/App",
      "github:modpreneur/trinityJS@master/Controller",
      "github:modpreneur/trinityJS@master/Services",
      "github:modpreneur/trinityJS@master/Store",
      "github:modpreneur/trinityJS@master/TrinityForm",
      "github:modpreneur/trinityJS@master/Gateway",
      "github:modpreneur/trinityJS@master/Collection",
      "github:modpreneur/trinityJS@master/TrinityTab",
      "github:modpreneur/trinityJS@master/components",
      "npm:babel-runtime@5.8.34/helpers/class-call-check",
      "github:modpreneur/trinityJS@master/utils/Dom",
      "npm:babel-runtime@5.8.34/core-js/object/keys",
      "npm:babel-runtime@5.8.34/helpers/create-class",
      "github:modpreneur/trinityJS@master/utils/closureEvents",
      "github:modpreneur/trinityJS@master/utils/Xhr",
      "npm:babel-runtime@5.8.34/helpers/get",
      "npm:babel-runtime@5.8.34/helpers/inherits",
      "github:modpreneur/trinityJS@master/components/TrinityForm",
      "github:modpreneur/trinityJS@master/utils/classlist",
      "npm:core-js@1.2.6/library/fn/object/keys",
      "npm:babel-runtime@5.8.34/core-js/object/define-property",
      "npm:babel-runtime@5.8.34/core-js/object/seal",
      "npm:babel-runtime@5.8.34/core-js/object/is-frozen",
      "npm:babel-runtime@5.8.34/core-js/object/freeze",
      "npm:babel-runtime@5.8.34/core-js/object/get-own-property-descriptor",
      "npm:babel-runtime@5.8.34/core-js/object/create",
      "npm:fbemitter@2.0.0",
      "npm:babel-runtime@5.8.34/core-js/object/set-prototype-of",
      "npm:core-js@1.2.6/library/modules/$.core",
      "npm:core-js@1.2.6/library/fn/object/define-property",
      "npm:core-js@1.2.6/library/modules/es6.object.keys",
      "npm:core-js@1.2.6/library/fn/object/seal",
      "npm:core-js@1.2.6/library/fn/object/is-frozen",
      "npm:core-js@1.2.6/library/fn/object/freeze",
      "npm:core-js@1.2.6/library/fn/object/get-own-property-descriptor",
      "npm:core-js@1.2.6/library/fn/object/create",
      "npm:fbemitter@2.0.0/index",
      "npm:core-js@1.2.6/library/fn/object/set-prototype-of",
      "npm:core-js@1.2.6/library/modules/$",
      "npm:core-js@1.2.6/library/modules/$.to-object",
      "npm:core-js@1.2.6/library/modules/$.object-sap",
      "npm:core-js@1.2.6/library/modules/es6.object.seal",
      "npm:core-js@1.2.6/library/modules/es6.object.is-frozen",
      "npm:core-js@1.2.6/library/modules/es6.object.freeze",
      "npm:core-js@1.2.6/library/modules/es6.object.get-own-property-descriptor",
      "npm:fbemitter@2.0.0/lib/BaseEventEmitter",
      "npm:core-js@1.2.6/library/modules/es6.object.set-prototype-of",
      "npm:core-js@1.2.6/library/modules/$.defined",
      "npm:core-js@1.2.6/library/modules/$.fails",
      "npm:core-js@1.2.6/library/modules/$.export",
      "npm:core-js@1.2.6/library/modules/$.is-object",
      "npm:core-js@1.2.6/library/modules/$.to-iobject",
      "npm:fbjs@0.1.0-alpha.7/lib/emptyFunction",
      "npm:fbjs@0.1.0-alpha.7/lib/invariant",
      "npm:fbemitter@2.0.0/lib/EmitterSubscription",
      "npm:fbemitter@2.0.0/lib/EventSubscriptionVendor",
      "npm:core-js@1.2.6/library/modules/$.set-proto",
      "npm:core-js@1.2.6/library/modules/$.global",
      "npm:core-js@1.2.6/library/modules/$.ctx",
      "npm:core-js@1.2.6/library/modules/$.iobject",
      "npm:fbemitter@2.0.0/lib/EventSubscription",
      "npm:core-js@1.2.6/library/modules/$.an-object",
      "npm:core-js@1.2.6/library/modules/$.a-function",
      "npm:core-js@1.2.6/library/modules/$.cof"
    ]
  },

  map: {
    "async": "npm:async@1.5.0",
    "babel": "npm:babel-core@5.8.34",
    "babel-runtime": "npm:babel-runtime@5.8.34",
    "core-js": "npm:core-js@1.2.6",
    "flux": "npm:flux@2.1.1",
    "jquery": "github:components/jquery@2.1.4",
    "lodash": "npm:lodash@3.10.1",
    "mousetrap": "npm:mousetrap@1.5.3",
    "trinity": "github:modpreneur/trinityJS@master",
    "github:jspm/nodelibs-assert@0.1.0": {
      "assert": "npm:assert@1.3.0"
    },
    "github:jspm/nodelibs-domain@0.1.0": {
      "domain-browser": "npm:domain-browser@1.1.7"
    },
    "github:jspm/nodelibs-events@0.1.1": {
      "events": "npm:events@1.0.2"
    },
    "github:jspm/nodelibs-path@0.1.0": {
      "path-browserify": "npm:path-browserify@0.0.0"
    },
    "github:jspm/nodelibs-process@0.1.2": {
      "process": "npm:process@0.11.2"
    },
    "github:jspm/nodelibs-util@0.1.0": {
      "util": "npm:util@0.10.3"
    },
    "github:modpreneur/trinityJS@master": {
      "fbemitter": "npm:fbemitter@2.0.0",
      "lodash": "npm:lodash@3.10.1"
    },
    "npm:asap@2.0.3": {
      "domain": "github:jspm/nodelibs-domain@0.1.0",
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:assert@1.3.0": {
      "util": "npm:util@0.10.3"
    },
    "npm:async@1.5.0": {
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:babel-runtime@5.8.34": {
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:core-js@1.2.6": {
      "fs": "github:jspm/nodelibs-fs@0.1.2",
      "path": "github:jspm/nodelibs-path@0.1.0",
      "process": "github:jspm/nodelibs-process@0.1.2",
      "systemjs-json": "github:systemjs/plugin-json@0.1.0"
    },
    "npm:domain-browser@1.1.7": {
      "events": "github:jspm/nodelibs-events@0.1.1"
    },
    "npm:fbemitter@2.0.0": {
      "fbjs": "npm:fbjs@0.1.0-alpha.7",
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:fbjs@0.1.0-alpha.7": {
      "core-js": "npm:core-js@1.2.6",
      "fs": "github:jspm/nodelibs-fs@0.1.2",
      "path": "github:jspm/nodelibs-path@0.1.0",
      "process": "github:jspm/nodelibs-process@0.1.2",
      "promise": "npm:promise@7.1.1",
      "whatwg-fetch": "npm:whatwg-fetch@0.9.0"
    },
    "npm:flux@2.1.1": {
      "fbemitter": "npm:fbemitter@2.0.0",
      "fbjs": "npm:fbjs@0.1.0-alpha.7",
      "immutable": "npm:immutable@3.7.6",
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:inherits@2.0.1": {
      "util": "github:jspm/nodelibs-util@0.1.0"
    },
    "npm:lodash@3.10.1": {
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:mousetrap@1.5.3": {
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:path-browserify@0.0.0": {
      "process": "github:jspm/nodelibs-process@0.1.2"
    },
    "npm:process@0.11.2": {
      "assert": "github:jspm/nodelibs-assert@0.1.0"
    },
    "npm:promise@7.1.1": {
      "asap": "npm:asap@2.0.3",
      "fs": "github:jspm/nodelibs-fs@0.1.2"
    },
    "npm:util@0.10.3": {
      "inherits": "npm:inherits@2.0.1",
      "process": "github:jspm/nodelibs-process@0.1.2"
    }
  }
});
